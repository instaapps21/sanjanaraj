<?php
require_once 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['referral_id'] = $user['referral_id'];

            if ($user['role'] === 'admin') {
                header("Location: /sanjanaraj/admin/dashboard.php");
            } else {
                header("Location: /sanjanaraj/user/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill all fields.";
    }
}
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom p-4">
                <div class="text-center mb-4">
                    <img src="logo.png" alt="Logo" style="max-height: 80px;" class="mb-3" onerror="this.style.display='none'">
                    <h2>Welcome Back!</h2>
                    <p class="text-muted">Login to access your dashboard</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary-custom">Login to Dashboard</button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <p class="mb-0">Don't have an account? <a href="register.php" class="text-decoration-none" style="color: var(--fin-orange); font-weight:600;">Join Now</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
