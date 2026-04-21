<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fetch all active products
$products = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY category, name")->fetchAll();

// Fetch distinct categories for filter buttons
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE is_active = 1 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Page Header -->
<div class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #023047 0%, #0a5a8a 50%, #126e9f 100%); min-height: 220px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.08;">
        <svg width="100%" height="100%">
            <pattern id="fishPattern" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                <text x="10" y="40" font-size="24">&#x1F41F;</text>
                <text x="50" y="70" font-size="18">&#x1F41A;</text>
            </pattern>
            <rect width="100%" height="100%" fill="url(#fishPattern)"/>
        </svg>
    </div>
    <div class="container position-relative py-4 text-center" data-aos="fade-up">
        <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 mb-3" style="border-radius: 20px; font-size: 0.85rem;">
            <i class="fas fa-fish me-1"></i> Fresh from the Coast
        </span>
        <h1 class="display-4 fw-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Our Fish Products</h1>
        <p class="text-white-50 mb-3" style="max-width: 600px; margin: 0 auto;">Premium quality fresh fish delivered to your doorstep. Hand-picked from the finest coastal waters of Tamil Nadu.</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Products</li>
            </ol>
        </nav>
    </div>
    <!-- Wave separator -->
    <div class="position-absolute bottom-0 start-0 w-100">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 60L48 52C96 44 192 28 288 22C384 16 480 20 576 28C672 36 768 48 864 48C960 48 1056 36 1152 28C1248 20 1344 16 1392 14L1440 12V60H1392C1344 60 1248 60 1152 60C1056 60 960 60 864 60C768 60 672 60 576 60C480 60 384 60 288 60C192 60 96 60 48 60H0Z" fill="#F8F9FA"/>
        </svg>
    </div>
</div>

<section class="py-5" style="background-color: var(--fin-light-bg);">
    <div class="container">
        <!-- Intro Section -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold" style="color: var(--fin-blue);">Premium Quality Fresh Fish</h2>
            <p class="text-muted" style="max-width: 700px; margin: 0 auto;">
                SANJANARAJ brings you the finest selection of fresh fish and seafood from the pristine coastal waters. 
                Each product is carefully sourced, ensuring premium quality and freshness for your family's daily meals.
            </p>
        </div>

        <!-- Category Filter Buttons -->
        <?php if (!empty($categories)): ?>
        <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="col-12 text-center">
                <button class="btn btn-outline-primary active px-4 rounded-pill m-1 filter-btn" data-filter="all" onclick="filterProducts('all', this)">
                    <i class="fas fa-th me-1"></i> All
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button class="btn btn-outline-primary px-4 rounded-pill m-1 filter-btn" data-filter="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $cat))); ?>" onclick="filterProducts('<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $cat))); ?>', this)">
                        <?php
                            $catIcons = [
                                'Premium Beach Fish' => '<i class="fas fa-crown"></i>',
                                'Regular Beach Fish' => '<i class="fas fa-fish"></i>',
                                'Special Beach Fish' => '<i class="fas fa-star"></i>',
                                'Shellfish Beach' => '<i class="fas fa-shrimp"></i>',
                                'Freshwater' => '<i class="fas fa-water"></i>',
                                'Beach Fish' => '<i class="fas fa-fish"></i>',
                            ];
                            echo ($catIcons[$cat] ?? '<i class="fas fa-fish"></i>') . ' ' . htmlspecialchars($cat);
                        ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Products Grid -->
        <div class="row g-4" id="productsGrid">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-fish fa-4x mb-3 text-muted" style="opacity: 0.3;"></i>
                    <h5 class="text-muted">No products available at the moment.</h5>
                    <p class="text-muted small">Please check back soon for our fresh fish products!</p>
                </div>
            <?php endif; ?>

            <?php 
            $delay = 100;
            foreach ($products as $p): 
                $catSlug = strtolower(str_replace(' ', '-', $p['category']));
                $imgPath = '/sanjanaraj/assets/images/product/' . $p['image'];
                
                // Category badge colors
                $badgeStyles = [
                    'Premium Beach Fish' => 'background: linear-gradient(135deg, #667eea, #764ba2); color: white;',
                    'Regular Beach Fish' => 'background: linear-gradient(135deg, #11998e, #38ef7d); color: white;',
                    'Special Beach Fish' => 'background: linear-gradient(135deg, #F2994A, #F2C94C); color: white;',
                    'Shellfish Beach' => 'background: linear-gradient(135deg, #EB5757, #F2994A); color: white;',
                    'Freshwater' => 'background: linear-gradient(135deg, #2193b0, #6dd5ed); color: white;',
                    'Beach Fish' => 'background: linear-gradient(135deg, #834d9b, #d04ed6); color: white;',
                ];
                $badgeStyle = $badgeStyles[$p['category']] ?? 'background: #6c757d; color: white;';
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6 product-item" data-category="<?php echo $catSlug; ?>" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                <div class="card h-100 border-0 position-relative overflow-hidden" style="border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); transition: all 0.4s ease;">
                    <!-- Product Image -->
                    <div class="position-relative overflow-hidden" style="height: 240px;">
                        <img src="<?php echo $imgPath; ?>" 
                             class="w-100 h-100" 
                             alt="<?php echo htmlspecialchars($p['name']); ?>"
                             style="object-fit: cover; transition: transform 0.5s ease;"
                             onerror="this.src='https://placehold.co/400x300/023047/ffffff?text=<?php echo urlencode($p['name']); ?>'">
                        <!-- Category Badge -->
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge px-3 py-2" style="<?php echo $badgeStyle; ?> border-radius: 20px; font-size: 0.72rem; font-weight: 600; backdrop-filter: blur(10px);">
                                <?php echo htmlspecialchars($p['category']); ?>
                            </span>
                        </div>
                        <!-- PV Badge -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge px-2 py-2" style="background: rgba(0,0,0,0.6); color: white; border-radius: 10px; font-size: 0.72rem; backdrop-filter: blur(10px);">
                                <?php echo $p['pv']; ?> PV
                            </span>
                        </div>
                        <!-- Overlay gradient -->
                        <div class="position-absolute bottom-0 start-0 w-100" style="height: 60px; background: linear-gradient(transparent, rgba(0,0,0,0.05));"></div>
                    </div>

                    <!-- Product Info -->
                    <div class="card-body text-center px-4 pt-3 pb-2">
                        <h5 class="card-title fw-bold mb-1" style="color: var(--fin-blue); font-family: 'Outfit', sans-serif;"><?php echo htmlspecialchars($p['name']); ?></h5>
                        <?php if ($p['tamil_name']): ?>
                            <p class="text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars($p['tamil_name']); ?></p>
                        <?php endif; ?>
                        <p class="card-text small text-muted mb-2" style="line-height: 1.5; min-height: 42px;">
                            <?php echo htmlspecialchars($p['description']); ?>
                        </p>
                        <!-- Weight & Price Row -->
                        <div class="d-flex justify-content-center align-items-center gap-3 mb-1">
                            <span class="badge bg-light text-dark px-3 py-2" style="border-radius: 8px; font-size: 0.8rem;">
                                <i class="fas fa-weight-hanging me-1 text-muted"></i><?php echo htmlspecialchars($p['weight']); ?>
                            </span>
                            <h4 class="fw-bold mb-0" style="color: var(--fin-orange);">₹<?php echo number_format($p['price'], 0); ?></h4>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="card-footer bg-white border-0 text-center pb-4 px-4">
                        <a href="register.php" class="btn w-100 py-2" style="background: linear-gradient(135deg, var(--fin-blue), #0a5a8a); color: white; border-radius: 12px; font-weight: 600; transition: all 0.3s ease;">
                            <i class="fas fa-shopping-cart me-1"></i> Join to Buy
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                $delay += 50;
                if ($delay > 400) $delay = 100;
            endforeach; 
            ?>
        </div>

        <!-- Product Count Summary -->
        <?php if (!empty($products)): ?>
        <div class="text-center mt-4" data-aos="fade-up">
            <p class="text-muted small">Showing <strong id="visibleCount"><?php echo count($products); ?></strong> of <?php echo count($products); ?> products</p>
        </div>
        <?php endif; ?>

        <!-- Why Choose Us Section -->
        <div class="row mt-5 pt-4 g-4">
            <div class="col-12 text-center mb-3" data-aos="fade-up">
                <h3 class="fw-bold" style="color: var(--fin-blue);">Why Choose Our Products?</h3>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100" style="transition: transform 0.3s;">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width:60px;height:60px;background:linear-gradient(135deg, #11998e, #38ef7d);">
                        <i class="fas fa-leaf fa-lg text-white"></i>
                    </div>
                    <h5 class="fw-bold">100% Fresh</h5>
                    <p class="text-muted small mb-0">All our fish products are sourced fresh daily from the coastal waters of Tamil Nadu.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100" style="transition: transform 0.3s;">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width:60px;height:60px;background:linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-truck fa-lg text-white"></i>
                    </div>
                    <h5 class="fw-bold">Direct Delivery</h5>
                    <p class="text-muted small mb-0">From the fishermen's boat directly to your kitchen. No middlemen, best prices guaranteed.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100" style="transition: transform 0.3s;">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width:60px;height:60px;background:linear-gradient(135deg, #F2994A, #F2C94C);">
                        <i class="fas fa-coins fa-lg text-white"></i>
                    </div>
                    <h5 class="fw-bold">Earn PV Points</h5>
                    <p class="text-muted small mb-0">Every purchase earns you valuable PV (Point Value) that contributes to your network rewards.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center mt-5 pt-3" data-aos="fade-up">
            <div class="p-5 rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #023047, #0a5a8a);">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.1;">
                    <svg width="100%" height="100%">
                        <pattern id="dotPattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.5" fill="white"/>
                        </pattern>
                        <rect width="100%" height="100%" fill="url(#dotPattern)"/>
                    </svg>
                </div>
                <div class="position-relative">
                    <h4 class="fw-bold text-white mb-2">Ready to start selling our products?</h4>
                    <p class="text-white-50 mb-4">Join our network, promote fresh fish products, and earn generous commissions on every sale.</p>
                    <a href="register.php" class="btn px-5 py-3" style="background: var(--fin-orange); color: white; border-radius: 12px; font-weight: 700; font-size: 1.05rem; transition: all 0.3s ease;">
                        <i class="fas fa-rocket me-2"></i>Become a Distributor
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Card Hover Effects & Category Filter Script -->
<style>
    .product-item .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.12) !important;
    }
    .product-item .card:hover img {
        transform: scale(1.08);
    }
    .product-item .card-footer .btn:hover {
        background: linear-gradient(135deg, var(--fin-orange), #e65f00) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(255, 106, 0, 0.35);
    }
    .filter-btn.active {
        background-color: var(--fin-blue) !important;
        border-color: var(--fin-blue) !important;
        color: white !important;
    }
    .product-item.hidden {
        display: none !important;
    }
    /* Feature card hover */
    .col-md-4 .bg-white:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1) !important;
    }
</style>

<script>
function filterProducts(category, btn) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const items = document.querySelectorAll('.product-item');
    let visibleCount = 0;
    
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });
    
    // Update count
    const countEl = document.getElementById('visibleCount');
    if (countEl) countEl.textContent = visibleCount;
}
</script>

<?php include 'includes/footer.php'; ?>
