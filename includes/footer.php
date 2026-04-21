<!-- Footer -->
<footer class="footer-custom mt-auto">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6 text-center text-md-start">
                <img src="/sanjanaraj/assets/images/logo.png" alt="SANJANARAJ Logo"
                    style="height: 60px;" class="mb-3"
                    onerror="this.src='https://placehold.co/200x60?text=SANJANARAJ'">
                <p class="mb-3">Empowering India through unmatched network opportunities. Start your journey towards
                    financial freedom today with SANJANARAJ Private Limited</p>
                <div class="d-flex justify-content-center justify-content-md-start">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="mb-3 text-white">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/sanjanaraj/index.php">Home</a></li>
                    <li class="mb-2"><a href="/sanjanaraj/about.php">About Us</a></li>
                    <li class="mb-2"><a href="/sanjanaraj/opportunity.php">Business Plan</a></li>
                    <li class="mb-2"><a href="/sanjanaraj/products.php">Products</a></li>
                    <li class="mb-2"><a href="/sanjanaraj/contact.php">Contact Us</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-6">
                <h5 class="mb-3 text-white">Legal & Compliance</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/sanjanaraj/terms.php">Terms & Conditions</a></li>
                    <li class="mb-2"><a href="/sanjanaraj/privacy.php">Privacy & Refund Policy</a></li>
                    <li class="mb-2"><a href="/sanjanaraj/contact.php">Grievance Redressal</a></li>
                    <li class="mb-2"><a href="#">Nodal Officer</a></li>
                    <li class="mb-2"><a href="#">De-Listed Direct Sellers</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="mb-3 text-white">Contact Us</h5>
                <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-warning"></i>No. 5067, Kurinji Nagar,
                    Kalaiyarkovil, Somanathamangalam, Sivaganga, Tamil Nadu - 630551</p>
                <p class="mb-2"><i class="fas fa-phone-alt me-2 text-warning"></i>+91 98765 43210</p>
                <p class="mb-2"><i class="fas fa-envelope me-2 text-warning"></i>support@sanjanaraj.in</p>
            </div>
        </div>

        <hr class="mt-4 mb-3 border-light opacity-25">
        <div class="row text-center text-md-start">
            <div class="col-md-6">
                <p class="mb-0 small">&copy; <?php echo date('Y'); ?> SANJANARAJ Private Limited All Rights
                    Reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 small">Designed with ❤️ for India</p>
            </div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav fixed-bottom d-lg-none d-flex justify-content-around align-items-center bg-white border-top"
    style="box-shadow: 0 -4px 12px rgba(0,0,0,0.05); z-index: 1030; height: 75px;">
    <a href="/sanjanaraj/index.php" class="nav-item text-center text-decoration-none"
        style="color: var(--fin-blue); flex: 1;">
        <i class="fas fa-home fs-4"></i>
        <span class="d-block mt-1" style="font-size: 0.8rem; font-weight: 600;">Home</span>
    </a>
    <a href="/sanjanaraj/products.php" class="nav-item text-center text-decoration-none"
        style="color: var(--fin-blue); flex: 1;">
        <i class="fas fa-box-open fs-4"></i>
        <span class="d-block mt-1" style="font-size: 0.8rem; font-weight: 600;">Products</span>
    </a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/sanjanaraj/user/dashboard.php" class="nav-item text-center text-decoration-none"
            style="color: var(--fin-blue); flex: 1;">
            <i class="fas fa-user-circle fs-4"></i>
            <span class="d-block mt-1" style="font-size: 0.8rem; font-weight: 600;">Dashboard</span>
        </a>
    <?php else: ?>
        <a href="/sanjanaraj/login.php" class="nav-item text-center text-decoration-none"
            style="color: var(--fin-blue); flex: 1;">
            <i class="fas fa-sign-in-alt fs-4"></i>
            <span class="d-block mt-1" style="font-size: 0.8rem; font-weight: 600;">Login</span>
        </a>
    <?php endif; ?>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS Animation Library JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>
</body>

</html>