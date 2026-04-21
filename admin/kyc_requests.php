<?php
require_once '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$message_type = '';

// Handle Actions (Approve / Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['kyc_id'])) {
    $kyc_id = (int)$_POST['kyc_id'];
    $action = $_POST['action'];
    $reason = $_POST['rejection_reason'] ?? '';

    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE kyc_details SET status = 'approved', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$kyc_id]);
            $message = "KYC request approved successfully.";
            $message_type = "success";
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE kyc_details SET status = 'rejected', rejection_reason = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$reason, $kyc_id]);
            $message = "KYC request rejected.";
            $message_type = "success";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Fetch all KYC requests
$status_filter = $_GET['status'] ?? 'pending';
$allowed_statuses = ['pending', 'approved', 'rejected'];
if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = 'pending';
}

$stmt = $pdo->prepare("
    SELECT k.*, u.referral_id, u.name as user_name 
    FROM kyc_details k 
    JOIN users u ON k.user_id = u.id 
    WHERE k.status = ? 
    ORDER BY k.submitted_at DESC
");
$stmt->execute([$status_filter]);
$requests = $stmt->fetchAll();
?>

<?php include 'sidebar.php'; ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-id-card text-primary me-2"></i> KYC Requests</h5>
        <div class="btn-group">
            <a href="?status=pending" class="btn btn-outline-warning <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="?status=approved" class="btn btn-outline-success <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">Approved</a>
            <a href="?status=rejected" class="btn btn-outline-danger <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">Rejected</a>
        </div>
    </div>
    <div class="card-body p-4">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Document Info</th>
                        <th>Bank Info</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <?php if ($status_filter === 'pending'): ?>
                        <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($requests) > 0): ?>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td><?php echo date('d M Y, h:i A', strtotime($req['submitted_at'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($req['user_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($req['referral_id']); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($req['document_type']); ?></span><br>
                                    <small><?php echo htmlspecialchars($req['document_number']); ?></small>
                                </td>
                                <td>
                                    <strong>A/C:</strong> <?php echo htmlspecialchars($req['account_number']); ?><br>
                                    <small class="text-muted"><strong>IFSC:</strong> <?php echo htmlspecialchars($req['ifsc_code']); ?></small><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($req['bank_name']); ?></small>
                                </td>
                                <td>
                                    <a href="../assets/images/kyc/<?php echo htmlspecialchars($req['document_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                                <td>
                                    <?php if ($req['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif ($req['status'] === 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($status_filter === 'pending'): ?>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form method="post" onsubmit="return confirm('Approve this KYC request?');">
                                            <input type="hidden" name="kyc_id" value="<?php echo $req['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fas fa-check"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $req['id']; ?>" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal<?php echo $req['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject KYC Request</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="kyc_id" value="<?php echo $req['id']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <div class="mb-3">
                                                            <label class="form-label">Reason for Rejection</label>
                                                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Blurry document, name mismatch..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No KYC requests found in this category.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
