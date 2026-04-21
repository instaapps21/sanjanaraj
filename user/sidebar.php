<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: /sanjanaraj/login.php");
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - SANJANARAJ</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --fin-blue: #0A3D91;
            --fin-orange: #FF6A00;
        }
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background-color: var(--fin-blue); color: white; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); color: white; border-left: 4px solid var(--fin-orange); }
        .navbar-top { background-color: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        @media (max-width: 768px) {
            .sidebar { position: fixed; left: -250px; z-index: 1050; transition: left 0.3s ease; }
            .sidebar.active { left: 0; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
            .sidebar-overlay.active { display: block; }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggle = document.getElementById('sidebarToggle');
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            if(toggle && sidebar && overlay) {
                toggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
        });
    </script>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="d-flex bg-light">
    <!-- Sidebar -->
    <div class="sidebar flex-shrink-0" id="sidebar" style="width: 250px;">
        <div class="p-4 text-center border-bottom border-secondary bg-white">
            <img src="/sanjanaraj/assets/images/logo.png" alt="SANJANARAJ Logo" style="max-height: 50px;">
            <div class="mt-2"><small class="text-white-50 fw-bold" style="background-color: var(--fin-blue); padding: 2px 8px; border-radius: 4px;">User Portal</small></div>
        </div>
        <div class="mt-3">
            <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home me-2 w-20"></i> Dashboard</a>
            <a href="kyc.php" class="<?php echo $current_page == 'kyc.php' ? 'active' : ''; ?>"><i class="fas fa-id-card me-2 w-20"></i> KYC Verification</a>
            <a href="placements.php" class="<?php echo $current_page == 'placements.php' ? 'active' : ''; ?>"><i class="fas fa-user-plus me-2 w-20"></i> New Joiners</a>
            <a href="tree.php" class="<?php echo $current_page == 'tree.php' ? 'active' : ''; ?>"><i class="fas fa-sitemap me-2 w-20"></i> Network Tree</a>
            <a href="wallet.php" class="<?php echo $current_page == 'wallet.php' ? 'active' : ''; ?>"><i class="fas fa-wallet me-2 w-20"></i> My Wallet</a>
            <a href="support.php" class="<?php echo $current_page == 'support.php' ? 'active' : ''; ?>"><i class="fas fa-headset me-2 w-20"></i> Support Tickets</a>
            <a href="/sanjanaraj/index.php"><i class="fas fa-globe me-2 w-20"></i> Main Website</a>
            <a href="/sanjanaraj/logout.php" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2 w-20"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 w-100 overflow-hidden">
        <!-- Top Nav -->
        <nav class="navbar navbar-expand-lg navbar-top p-3 px-4 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-md-none me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h5 class="mb-0 fw-bold text-dark d-none d-sm-block">Dashboard Overview</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3 fw-bold">ID: <span class="text-primary"><?php echo htmlspecialchars($_SESSION['referral_id']); ?></span></span>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:40px;height:40px;">
                    <?php echo substr($_SESSION['user_name'], 0, 1); ?>
                </div>
            </div>
        </nav>
        <div class="p-4 overflow-auto" style="height: calc(100vh - 72px);">
