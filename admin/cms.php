<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';
// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['page_id'])) {
    $id = (int)$_POST['page_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    $stmt = $pdo->prepare("UPDATE cms_pages SET title = ?, content = ? WHERE id = ?");
    if ($stmt->execute([$title, $content, $id])) {
        $msg = "<div class='alert alert-success mt-3 py-2'>Page updated successfully.</div>";
    }
}

// Fetch pages
$pages = $pdo->query("SELECT * FROM cms_pages ORDER BY id ASC")->fetchAll();

// Check if editing specific page
$edit_page = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM cms_pages WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_page = $stmt->fetch();
}
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <h4 class="fw-bold mb-0">Content Management System</h4>
    <a href="cms.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list me-1"></i> View All Pages</a>
</div>

<?php echo $msg; ?>

<?php if($edit_page): ?>
    <!-- Edit Form -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-4">Editing: <?php echo htmlspecialchars($edit_page['title']); ?> (<?php echo htmlspecialchars($edit_page['slug']); ?>)</h5>
        <form method="POST">
            <input type="hidden" name="page_id" value="<?php echo $edit_page['id']; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Page Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($edit_page['title']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold small text-muted">Page HTML Content</label>
                <textarea class="form-control" name="content" rows="15" required><?php echo htmlspecialchars($edit_page['content']); ?></textarea>
                <small class="text-muted mt-1 d-block">You can use standard HTML tags layout.</small>
            </div>
            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
            <a href="cms.php" class="btn btn-light ms-2">Cancel</a>
        </form>
    </div>
<?php else: ?>
    <!-- List Pages -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Page Slug</th>
                        <th>Title</th>
                        <th>Last Updated</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pages as $p): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['slug']); ?></span></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($p['title']); ?></td>
                            <td class="text-muted small"><?php echo date('d M Y, H:i', strtotime($p['updated_at'])); ?></td>
                            <td class="text-end">
                                <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit me-1"></i> Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i> To add the CMS content to the frontend, you'll need to create standard endpoint pages (e.g. <code>terms.php</code>) that pull from the <code>cms_pages</code> database table.
    </div>
<?php endif; ?>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
