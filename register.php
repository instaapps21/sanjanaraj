<?php
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sponsor_id = trim($_POST['sponsor_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $aadhaar_no = trim($_POST['aadhaar_no'] ?? '');
    $pan_no = trim($_POST['pan_no'] ?? '');
    $utrn_no = trim($_POST['utrn_no'] ?? '');
    // $package = $_POST['package'] ?? 0;

    // Validate sponsor
    $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_id = ? LIMIT 1");
    $stmt->execute([$sponsor_id]);
    $sponsor = $stmt->fetch();

    if (!$sponsor && $sponsor_id !== '') {
        $error = "Invalid Sponsor ID.";
    } elseif ($sponsor) {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE sponsor_id = ?");
        $countStmt->execute([$sponsor_id]);
        if ($countStmt->fetchColumn() >= 2) {
            $error = "This sponsor has already filled their direct two legs. Please enter a new Sponsor ID.";
        }
    }

    if (empty($error) && (empty($name) || empty($email) || empty($phone) || empty($password))) {
        $error = "Please fill all required fields.";
    } elseif (empty($error)) {
        // Generate unique referral ID for the new user
        $new_referral_id = 'FIN' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (sponsor_id, referral_id, name, email, phone, password_hash, aadhaar_no, pan_no, utrn_no, parent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sponsor_id, $new_referral_id, $name, $email, $phone, $hash, $aadhaar_no, $pan_no, $utrn_no, $sponsor ? $sponsor['id'] : null]);
            $success = "Registration successful! Your Referral ID is: <strong>$new_referral_id</strong>. <br><a href='login.php'>Click here to login</a>.";
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Email or Phone already exists.";
            } else {
                $error = "An error occurred during registration.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-custom p-4">
                <div class="text-center mb-4">
                    <h2>Join SANJANARAJ</h2>
                    <p class="text-muted">Start your entrepreneurial journey today</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php else: ?>

                    <form method="POST" action="">
                        <h5 class="mb-3 border-bottom pb-2">Sponsor Details</h5>
                        <div class="mb-3">
                            <label for="sponsor_id" class="form-label fw-bold">Sponsor ID <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sponsor_id" name="sponsor_id"
                                placeholder="e.g. FIN_ADMIN" required
                                value="<?php echo htmlspecialchars($_GET['ref'] ?? ''); ?>">
                            <small class="text-muted">Enter the ID of the person who referred you.</small>
                        </div>

                        <h5 class="mb-3 mt-4 border-bottom pb-2">Personal Details</h5>
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-bold">Mobile Number <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <h5 class="mb-3 mt-4 border-bottom pb-2">KYC Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="aadhaar_no" class="form-label fw-bold">Aadhaar Card Number</label>
                                <input type="text" class="form-control" id="aadhaar_no" name="aadhaar_no">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pan_no" class="form-label fw-bold">PAN Card Number</label>
                                <input type="text" class="form-control" id="pan_no" name="pan_no">
                            </div>
                        </div>

                        <h5 class="mb-3 mt-4 border-bottom pb-2">Payment Details (Rs. 2500)</h5>
                        <div class="mb-4 text-center">
                            <!-- Replace src with actual QR code path if different -->
                            <img src="assets/images/qr-code.png?v=<?php echo file_exists('assets/images/qr-code.png') ? filemtime('assets/images/qr-code.png') : time(); ?>" alt="Payment QR Code for Rs. 2500"
                                class="img-fluid border p-2 rounded bg-white" style="max-width: 250px;">
                            <p class="mt-2 text-muted fw-bold">Scan to pay Rs. 2500</p>
                        </div>

                        <div class="mb-4">
                            <label for="utrn_no" class="form-label fw-bold">UTRN Number <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" class="form-control" id="utrn_no" name="utrn_no">
                            <small class="text-muted">Enter the 12-digit UTR/Reference number after successful payment of
                                Rs. 2500.</small>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">I agree to the <a href="terms.php">Terms &
                                    Conditions</a> and <a href="privacy.php">Privacy Policy</a>.</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-secondary-custom btn-lg">Create Account & Proceed to
                                Pay</button>
                        </div>
                    </form>

                <?php endif; ?>

                <div class="mt-4 text-center">
                    <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none"
                            style="color: var(--fin-blue); font-weight:600;">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>