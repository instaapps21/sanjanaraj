<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
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
    <title>Admin Dashboard - SANJANARAJ</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --fin-blue: #023047; --fin-orange: #fb8500; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .sidebar { background-color: var(--fin-blue); color: white; min-height: 100vh; }
        .sidebar a { color: rgba(255,255,255,0.7); text-decoration: none; display: block; padding: 15px 20px; border-left: 4px solid transparent;}
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.05); color: white; border-color: var(--fin-orange); }
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
        <div class="p-4 text-center border-bottom border-secondary mb-3 bg-white">
            <img src="/sanjanaraj/assets/images/logo.png" alt="SANJANARAJ Logo" style="max-height: 50px;">
            <div class="mt-2"><small class="text-warning fw-bold" style="background-color: var(--fin-blue); padding: 2px 8px; border-radius: 4px;">Admin Portal</small></div>
        </div>
        <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home me-2"></i> Dashboard</a>
        <a href="users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>"><i class="fas fa-users me-2"></i> User Network</a>
        <a href="kyc_requests.php" class="<?php echo $current_page == 'kyc_requests.php' ? 'active' : ''; ?>"><i class="fas fa-id-card me-2"></i> KYC Requests</a>
        <a href="withdrawals.php" class="<?php echo $current_page == 'withdrawals.php' ? 'active' : ''; ?>"><i class="fas fa-money-bill-wave me-2"></i> Withdrawals</a>
        <a href="products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>"><i class="fas fa-box me-2"></i> Products</a>
        <a href="cms.php" class="<?php echo $current_page == 'cms.php' ? 'active' : ''; ?>"><i class="fas fa-file-alt me-2"></i> CMS Pages</a>
        <a href="news_events.php" class="<?php echo $current_page == 'news_events.php' ? 'active' : ''; ?>"><i class="fas fa-bullhorn me-2"></i> News & Events</a>
        <a href="complaints.php" class="<?php echo $current_page == 'complaints.php' ? 'active' : ''; ?>"><i class="fas fa-headset me-2"></i> Complaints</a>
        <a href="payment_settings.php" class="<?php echo $current_page == 'payment_settings.php' ? 'active' : ''; ?>"><i class="fas fa-qrcode me-2"></i> Payment Settings</a>
        <a href="/sanjanaraj/index.php" target="_blank"><i class="fas fa-eye me-2"></i> View Site</a>
        <a href="/sanjanaraj/logout.php" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 w-100 overflow-hidden">
        <nav class="navbar navbar-light bg-white border-bottom px-4 py-3 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-md-none me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h5 class="fw-bold mb-0 d-none d-sm-block">System Administration</h5>
            </div>
            <div class="fw-bold text-muted">Admin User</div>
        </nav>
        <div class="p-4 overflow-auto" style="height: calc(100vh - 66px);">
