<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANJANARAJ Private Limited | Empowering India</title>
    <meta name="description" content="SANJANARAJ Private Limited provides leading multi-level marketing business opportunities in India. Start your journey towards financial freedom today!">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/sanjanaraj/assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="/sanjanaraj/index.php">
            <img src="/sanjanaraj/assets/images/logo.png" alt="SANJANARAJ Logo" height="45" onerror="this.src='https://placehold.co/200x50?text=Future+India'"> 
            <div class="lh-1 mt-1 d-flex flex-column justify-content-center">
                <div class="fw-bolder text-uppercase" style="color: var(--fin-blue); font-family: 'Outfit', sans-serif; font-size: clamp(0.9rem, 3.5vw, 1.25rem); letter-spacing: 0.5px;">SANJANARAJ</div>
                <div class="fw-semibold text-uppercase text-muted" style="font-family: 'Inter', sans-serif; font-size: clamp(0.55rem, 2vw, 0.65rem); letter-spacing: 1.5px;">Private Limited</div>
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/sanjanaraj/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/sanjanaraj/about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/sanjanaraj/opportunity.php">Business Opportunity</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/sanjanaraj/products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/sanjanaraj/contact.php">Contact</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/sanjanaraj/user/dashboard.php" class="btn btn-primary-custom">Dashboard</a>
                    <a href="/sanjanaraj/logout.php" class="btn btn-outline-danger">Logout</a>
                <?php else: ?>
                    <a href="/sanjanaraj/login.php" class="btn btn-outline-primary" style="font-weight:600; border-color:var(--fin-blue); color:var(--fin-blue);">Login</a>
                    <a href="/sanjanaraj/register.php" class="btn btn-secondary-custom">Join Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
