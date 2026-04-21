<?php
require_once '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Check current KYC status
$stmt = $pdo->prepare("SELECT * FROM kyc_details WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$current_kyc = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only allow submission if no KYC exists, or current is rejected
    if (!$current_kyc || $current_kyc['status'] === 'rejected') {
        $document_type = $_POST['document_type'] ?? '';
        $document_number = $_POST['document_number'] ?? '';
        $bank_name = $_POST['bank_name'] ?? '';
        $branch_name = $_POST['branch_name'] ?? '';
        $account_holder_name = $_POST['account_holder_name'] ?? '';
        $account_number = $_POST['account_number'] ?? '';
        $ifsc_code = $_POST['ifsc_code'] ?? '';
        
        $document_path = '';

        // Handle file upload
        if (isset($_FILES['document_proof']) && $_FILES['document_proof']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['document_proof']['tmp_name'];
            $file_name = basename($_FILES['document_proof']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];

            if (in_array($file_ext, $allowed_exts)) {
                if ($_FILES['document_proof']['size'] <= 2 * 1024 * 1024) { // 2MB limit
                    $new_filename = 'kyc_' . $user_id . '_' . time() . '.' . $file_ext;
                    $upload_dir = '../assets/images/kyc/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                        $document_path = $new_filename;
                    } else {
                        $message = "Failed to move uploaded file.";
                        $message_type = "danger";
                    }
                } else {
                    $message = "File size exceeds 2MB limit.";
                    $message_type = "danger";
                }
            } else {
                $message = "Invalid file format. Only JPG, PNG, and PDF are allowed.";
                $message_type = "danger";
            }
        } else {
            $message = "Please upload a valid document proof.";
            $message_type = "danger";
        }

        if ($document_path && !$message) {
            try {
                if ($current_kyc && $current_kyc['status'] === 'rejected') {
                    // Update existing
                    $stmt = $pdo->prepare("UPDATE kyc_details SET document_type = ?, document_number = ?, document_path = ?, bank_name = ?, branch_name = ?, account_holder_name = ?, account_number = ?, ifsc_code = ?, status = 'pending', submitted_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$document_type, $document_number, $document_path, $bank_name, $branch_name, $account_holder_name, $account_number, $ifsc_code, $current_kyc['id']]);
                } else {
                    // Insert new
                    $stmt = $pdo->prepare("INSERT INTO kyc_details (user_id, document_type, document_number, document_path, bank_name, branch_name, account_holder_name, account_number, ifsc_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $document_type, $document_number, $document_path, $bank_name, $branch_name, $account_holder_name, $account_number, $ifsc_code]);
                }
                $message = "KYC Details submitted successfully. Awaiting approval.";
                $message_type = "success";
                
                // Refresh current KYC
                $stmt = $pdo->prepare("SELECT * FROM kyc_details WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1");
                $stmt->execute([$user_id]);
                $current_kyc = $stmt->fetch();
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $message_type = "danger";
            }
        }
    }
}
?>

<?php include 'sidebar.php'; ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 fw-bold"><i class="fas fa-id-card text-primary me-2"></i> KYC Verification</h5>
    </div>
    <div class="card-body p-4">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($current_kyc && $current_kyc['status'] === 'approved'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> Your KYC has been <strong>Approved</strong>.
            </div>
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Document Type</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['document_type']); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Document Number</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['document_number']); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Bank Name</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['bank_name']); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Account Number</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['account_number']); ?></div>
                </div>
            </div>
        <?php elseif ($current_kyc && $current_kyc['status'] === 'pending'): ?>
            <div class="alert alert-warning">
                <i class="fas fa-clock me-2"></i> Your KYC is currently <strong>Pending</strong> approval.
            </div>
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Document Type</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['document_type']); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Document Number</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['document_number']); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Bank Name</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['bank_name']); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Account Number</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($current_kyc['account_number']); ?></div>
                </div>
            </div>
        <?php else: ?>
            <?php if ($current_kyc && $current_kyc['status'] === 'rejected'): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i> Your previous KYC was <strong>Rejected</strong>. 
                    <br>Reason: <?php echo htmlspecialchars($current_kyc['rejection_reason']); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Please submit your KYC and Bank details below.
                </div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <h6 class="fw-bold mb-3 mt-4 text-primary">Identity Document</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Document Type *</label>
                        <select name="document_type" class="form-select" required>
                            <option value="">Select Document</option>
                            <option value="Aadhaar Card">Aadhaar Card</option>
                            <option value="PAN Card">PAN Card</option>
                            <option value="Voter ID">Voter ID</option>
                            <option value="Passport">Passport</option>
                            <option value="Driving License">Driving License</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Document Number *</label>
                        <input type="text" name="document_number" class="form-control" required placeholder="Enter Document Number">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label">Upload Proof (Image/PDF, Max 2MB) *</label>
                        <input type="file" name="document_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 mt-2 text-primary">Bank Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account Holder Name *</label>
                        <input type="text" name="account_holder_name" class="form-control" required placeholder="Name as per Bank Account">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bank Name *</label>
                        <input type="text" name="bank_name" class="form-control" required placeholder="Enter Bank Name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account Number *</label>
                        <input type="text" name="account_number" class="form-control" required placeholder="Enter Account Number">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">IFSC Code *</label>
                        <input type="text" name="ifsc_code" class="form-control" required placeholder="Enter IFSC Code">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Branch Name *</label>
                        <input type="text" name="branch_name" class="form-control" required placeholder="Enter Branch Name">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2 mt-3 fw-bold">Submit KYC Details</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
