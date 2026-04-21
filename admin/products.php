<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';
$uploadDir = '../assets/images/product/';

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// --- CREATE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $category = trim($_POST['category']);
    $name = trim($_POST['name']);
    $tamil_name = trim($_POST['tamil_name']);
    $desc = trim($_POST['description']);
    $weight = trim($_POST['weight']);
    $pv = (int)$_POST['pv'];
    $price = (float)$_POST['price'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Image upload
    $image = 'default_product.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $image = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (category, name, tamil_name, description, weight, pv, price, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$category, $name, $tamil_name, $desc, $weight, $pv, $price, $image, $is_active])) {
        $msg = "<div class='alert alert-success alert-dismissible fade show mt-3 py-2' role='alert'><i class='fas fa-check-circle me-2'></i>Product added successfully.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $msg = "<div class='alert alert-danger alert-dismissible fade show mt-3 py-2' role='alert'><i class='fas fa-exclamation-circle me-2'></i>Failed to add product.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// --- UPDATE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = (int)$_POST['product_id'];
    $category = trim($_POST['category']);
    $name = trim($_POST['name']);
    $tamil_name = trim($_POST['tamil_name']);
    $desc = trim($_POST['description']);
    $weight = trim($_POST['weight']);
    $pv = (int)$_POST['pv'];
    $price = (float)$_POST['price'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Check for new image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $newImage = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImage);

            // Delete old image if not default
            $old = $pdo->prepare("SELECT image FROM products WHERE id = ?");
            $old->execute([$id]);
            $oldImage = $old->fetchColumn();
            if ($oldImage && $oldImage !== 'default_product.png' && file_exists($uploadDir . $oldImage)) {
                // Keep extracted images, only delete uploaded ones
                if (strpos($oldImage, 'product_') === 0) {
                    unlink($uploadDir . $oldImage);
                }
            }

            $stmt = $pdo->prepare("UPDATE products SET category=?, name=?, tamil_name=?, description=?, weight=?, pv=?, price=?, image=?, is_active=? WHERE id=?");
            $stmt->execute([$category, $name, $tamil_name, $desc, $weight, $pv, $price, $newImage, $is_active, $id]);
        }
    } else {
        $stmt = $pdo->prepare("UPDATE products SET category=?, name=?, tamil_name=?, description=?, weight=?, pv=?, price=?, is_active=? WHERE id=?");
        $stmt->execute([$category, $name, $tamil_name, $desc, $weight, $pv, $price, $is_active, $id]);
    }
    $msg = "<div class='alert alert-info alert-dismissible fade show mt-3 py-2' role='alert'><i class='fas fa-edit me-2'></i>Product updated successfully.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// --- TOGGLE STATUS ---
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE products SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    header("Location: products.php");
    exit;
}

// --- DELETE ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Get image name before deleting
    $imgStmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $imgStmt->execute([$id]);
    $imgName = $imgStmt->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Delete uploaded image file (keep default/extracted ones)
        if ($imgName && $imgName !== 'default_product.png' && strpos($imgName, 'product_') === 0 && file_exists($uploadDir . $imgName)) {
            unlink($uploadDir . $imgName);
        }
        $msg = "<div class='alert alert-warning alert-dismissible fade show mt-3 py-2' role='alert'><i class='fas fa-trash me-2'></i>Product deleted.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// --- FETCH ALL PRODUCTS ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterCategory = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$filterStatus = isset($_GET['status']) ? trim($_GET['status']) : '';

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR tamil_name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filterCategory) {
    $query .= " AND category = ?";
    $params[] = $filterCategory;
}
if ($filterStatus === '1') {
    $query .= " AND is_active = 1";
} elseif ($filterStatus === '0') {
    $query .= " AND is_active = 0";
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for filter
$categories = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$activeProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
$inactiveProducts = $totalProducts - $activeProducts;
?>

<style>
    .stat-card { border-radius: 12px; padding: 20px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .product-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef; }
    .modal-custom .modal-content { border-radius: 16px; border: none; overflow: hidden; }
    .modal-custom .modal-header { background: linear-gradient(135deg, #023047, #0a5a8a); color: white; border: none; }
    .modal-custom .modal-header .btn-close { filter: brightness(0) invert(1); }
    .form-control:focus, .form-select:focus { border-color: var(--fin-orange); box-shadow: 0 0 0 0.2rem rgba(255, 106, 0, 0.15); }
    .badge-category { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; font-weight: 500; }
    .table th { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; }
    .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
    .search-bar { border-radius: 10px; border: 2px solid #e9ecef; padding: 10px 16px; transition: border-color 0.3s; }
    .search-bar:focus { border-color: var(--fin-orange); box-shadow: none; }
    .image-preview { width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border: 3px dashed #dee2e6; }
    .filter-btn.active { background: var(--fin-blue) !important; color: white !important; border-color: var(--fin-blue) !important; }
    .pv-badge { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; font-weight: 600; }
</style>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-fish me-2" style="color: var(--fin-orange);"></i>Product Management</h4>
        <p class="text-muted small mb-0">Manage your fish product catalog</p>
    </div>
    <button class="btn btn-primary btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#createProductModal" style="background: var(--fin-blue); border-color: var(--fin-blue); border-radius: 10px;">
        <i class="fas fa-plus me-1"></i> Add Product
    </button>
</div>

<?php echo $msg; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-white shadow-sm d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(10,90,138,0.1);">
                <i class="fas fa-box text-primary"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Total Products</div>
                <h4 class="fw-bold mb-0"><?php echo $totalProducts; ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-white shadow-sm d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(25,135,84,0.1);">
                <i class="fas fa-check-circle text-success"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Active</div>
                <h4 class="fw-bold mb-0 text-success"><?php echo $activeProducts; ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-white shadow-sm d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(220,53,69,0.1);">
                <i class="fas fa-ban text-danger"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Inactive</div>
                <h4 class="fw-bold mb-0 text-danger"><?php echo $inactiveProducts; ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 search-bar" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="border-radius: 0 10px 10px 0;">
                </div>
            </div>
            <div class="col-md-3">
                <select name="cat" class="form-select" style="border-radius: 10px;">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $filterCategory === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" style="border-radius: 10px;">
                    <option value="">All Status</option>
                    <option value="1" <?php echo $filterStatus === '1' ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $filterStatus === '0' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100" style="border-radius: 10px; background: var(--fin-blue); border-color: var(--fin-blue);"><i class="fas fa-filter me-1"></i> Filter</button>
                <a href="products.php" class="btn btn-sm btn-outline-secondary" style="border-radius: 10px;" title="Clear"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="bg-light">
                    <th class="ps-4">ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Weight</th>
                    <th>PV</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                    <tr>
                        <td class="text-muted ps-4">#<?php echo $p['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <?php
                                    $imgPath = '/sanjanaraj/assets/images/product/' . $p['image'];
                                    $imgFile = $uploadDir . $p['image'];
                                ?>
                                <img src="<?php echo $imgPath; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-thumb" onerror="this.src='https://placehold.co/50x50?text=🐟'">
                                <div>
                                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($p['name']); ?></h6>
                                    <?php if ($p['tamil_name']): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($p['tamil_name']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $catColors = [
                                    'Premium Beach Fish' => 'bg-primary',
                                    'Regular Beach Fish' => 'bg-info',
                                    'Special Beach Fish' => 'bg-warning text-dark',
                                    'Shellfish Beach' => 'bg-danger',
                                    'Freshwater' => 'bg-success',
                                    'Beach Fish' => 'bg-secondary',
                                ];
                                $catClass = $catColors[$p['category']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $catClass; ?> badge-category"><?php echo htmlspecialchars($p['category']); ?></span>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($p['weight']); ?></td>
                        <td><span class="badge pv-badge"><?php echo $p['pv']; ?> PV</span></td>
                        <td class="fw-bold" style="color: var(--fin-orange);">₹<?php echo number_format($p['price'], 2); ?></td>
                        <td>
                            <?php if($p['is_active']): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success" style="border-radius: 20px; padding: 5px 12px;">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Active
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger" style="border-radius: 20px; padding: 5px 12px;">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Inactive
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="?toggle=<?php echo $p['id']; ?>" class="action-btn btn btn-sm <?php echo $p['is_active'] ? 'btn-outline-warning' : 'btn-outline-success'; ?>" title="<?php echo $p['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                <i class="fas <?php echo $p['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                            </a>
                            <button class="action-btn btn btn-sm btn-outline-primary edit-btn"
                                data-id="<?php echo $p['id']; ?>"
                                data-name="<?php echo htmlspecialchars($p['name']); ?>"
                                data-tamil="<?php echo htmlspecialchars($p['tamil_name']); ?>"
                                data-category="<?php echo htmlspecialchars($p['category']); ?>"
                                data-description="<?php echo htmlspecialchars($p['description']); ?>"
                                data-weight="<?php echo htmlspecialchars($p['weight']); ?>"
                                data-pv="<?php echo $p['pv']; ?>"
                                data-price="<?php echo $p['price']; ?>"
                                data-active="<?php echo $p['is_active']; ?>"
                                data-image="<?php echo htmlspecialchars($p['image']); ?>"
                                title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <a href="?delete=<?php echo $p['id']; ?>" class="action-btn btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-fish fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                <h6 class="fw-bold">No products found</h6>
                                <p class="small">Try adjusting your filters or add a new product.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade modal-custom" id="createProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="action" value="create">
            <div class="modal-header py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Seer Fish" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Tamil Name</label>
                        <input type="text" name="tamil_name" class="form-control" placeholder="e.g. வஞ்சிரம்">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="Premium Beach Fish">Premium Beach Fish</option>
                            <option value="Regular Beach Fish">Regular Beach Fish</option>
                            <option value="Special Beach Fish">Special Beach Fish</option>
                            <option value="Shellfish Beach">Shellfish Beach</option>
                            <option value="Freshwater">Freshwater</option>
                            <option value="Beach Fish">Beach Fish</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Weight</label>
                        <input type="text" name="weight" class="form-control" value="1 KG" placeholder="e.g. 1 KG">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">PV (Point Value)</label>
                        <input type="number" name="pv" class="form-control" value="0" placeholder="0">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="createActive" checked>
                            <label class="form-check-label fw-bold small" for="createActive">Active (In Stock)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-muted">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe the product..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-muted">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'createPreview')">
                        <img id="createPreview" src="" alt="" class="image-preview mt-2 d-none">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: var(--fin-blue); border-color: var(--fin-blue); border-radius: 10px;">
                    <i class="fas fa-save me-1"></i> Save Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade modal-custom" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="product_id" id="editProductId">
            <div class="modal-header py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-pen-to-square me-2"></i>Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Tamil Name</label>
                        <input type="text" name="tamil_name" id="editTamilName" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                        <select name="category" id="editCategory" class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="Premium Beach Fish">Premium Beach Fish</option>
                            <option value="Regular Beach Fish">Regular Beach Fish</option>
                            <option value="Special Beach Fish">Special Beach Fish</option>
                            <option value="Shellfish Beach">Shellfish Beach</option>
                            <option value="Freshwater">Freshwater</option>
                            <option value="Beach Fish">Beach Fish</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Weight</label>
                        <input type="text" name="weight" id="editWeight" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" id="editPrice" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">PV (Point Value)</label>
                        <input type="number" name="pv" id="editPV" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editActive">
                            <label class="form-check-label fw-bold small" for="editActive">Active (In Stock)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-muted">Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-muted">Product Image</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img id="editCurrentImage" src="" alt="" class="product-thumb" style="width:60px;height:60px;" onerror="this.src='https://placehold.co/60x60?text=🐟'">
                            <small class="text-muted">Current image. Upload a new one to replace.</small>
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'editPreview')">
                        <img id="editPreview" src="" alt="" class="image-preview mt-2 d-none">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: var(--fin-orange); border-color: var(--fin-orange); border-radius: 10px;">
                    <i class="fas fa-save me-1"></i> Update Product
                </button>
            </div>
        </form>
    </div>
</div>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image Preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Edit Modal Population
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('editProductId').value = this.dataset.id;
        document.getElementById('editName').value = this.dataset.name;
        document.getElementById('editTamilName').value = this.dataset.tamil;
        document.getElementById('editCategory').value = this.dataset.category;
        document.getElementById('editDescription').value = this.dataset.description;
        document.getElementById('editWeight').value = this.dataset.weight;
        document.getElementById('editPV').value = this.dataset.pv;
        document.getElementById('editPrice').value = this.dataset.price;
        document.getElementById('editActive').checked = this.dataset.active === '1';
        document.getElementById('editCurrentImage').src = '/sanjanaraj/assets/images/product/' + this.dataset.image;

        const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
        editModal.show();
    });
});
</script>
</body>
</html>
