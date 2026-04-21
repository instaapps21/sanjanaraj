<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';
$qr_path = '../assets/images/qr-code.png';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['qr_image'])) {
    $file = $_FILES['qr_image'];
    
    // Check for upload errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (in_array($mime_type, $allowed_types)) {
            // Overwrite the existing QR code file
            if (move_uploaded_file($file['tmp_name'], $qr_path)) {
                $msg = "<div class='alert alert-success mt-3'><i class='fas fa-check-circle me-2'></i> Payment QR Code successfully updated!</div>";
            } else {
                $msg = "<div class='alert alert-danger mt-3'><i class='fas fa-exclamation-triangle me-2'></i> Failed to save the uploaded moving image. Check folder permissions.</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger mt-3'><i class='fas fa-exclamation-triangle me-2'></i> Invalid file type. Only JPG, PNG, and WebP are allowed.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger mt-3'><i class='fas fa-exclamation-triangle me-2'></i> Unknown error occurred during upload.</div>";
    }
}

// Get modification time for cache busting
$v = file_exists($qr_path) ? filemtime($qr_path) : time();
?>

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <h4 class="fw-bold mb-3 border-bottom pb-2">Payment Settings</h4>
    <p class="text-muted small mb-4">Update the common QR Code used for registration payments (Rs. 2500). This QR code is displayed to users directly during the sign-up process on the register page.</p>

    <?php if ($msg) echo $msg; ?>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <h6 class="fw-bold mb-3">Upload New QR Code</h6>
            <form action="" method="POST" enctype="multipart/form-data" class="card shadow-none border rounded p-4 bg-light">
                <div class="mb-3">
                    <label for="qr_image" class="form-label fw-semibold">Select Image (PNG, JPG, WebP)</label>
                    <input class="form-control" type="file" id="qr_image" name="qr_image" accept=".png,.jpg,.jpeg,.webp" required>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i> Update QR Code</button>
            </form>
        </div>

        <div class="col-md-6">
            <h6 class="fw-bold mb-3">Current Payment QR Code</h6>
            <div class="card shadow-none border rounded p-4 text-center">
                <?php if (file_exists($qr_path)): ?>
                    <img src="<?php echo $qr_path; ?>?v=<?php echo $v; ?>" alt="Current QR Code" class="img-fluid border p-2 rounded mx-auto" style="max-width: 250px;">
                    <p class="mt-3 text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Active and displayed on Registration Page</p>
                <?php else: ?>
                    <div class="py-5 text-muted">
                        <i class="fas fa-qrcode fa-3x mb-3 opacity-25"></i>
                        <p>No QR Code currently set.<br>Please upload an image.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
