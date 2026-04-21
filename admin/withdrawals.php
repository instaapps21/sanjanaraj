<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['action'];
    
    if (in_array($status, ['approved', 'rejected'])) {
        $stmt = $pdo->prepare("UPDATE withdrawals SET status = ?, process_date = NOW() WHERE id = ? AND status = 'pending'");
        if ($stmt->execute([$status, $id])) {
            // If rejected, refund the wallet
            if ($status === 'rejected') {
                $w = $pdo->query("SELECT user_id, amount FROM withdrawals WHERE id = $id")->fetch();
                if ($w) {
                    $pdo->query("UPDATE users SET wallet_balance = wallet_balance + {$w['amount']} WHERE id = {$w['user_id']}");
                    $pdo->query("INSERT INTO transactions (user_id, amount, type, description) VALUES ({$w['user_id']}, {$w['amount']}, 'credit', 'Refund - Withdrawal Rejected')");
                }
            }
            $msg = "<div class='alert alert-success mt-3 py-2'>Request marked as $status.</div>";
        }
    }
}

$withdrawals = $pdo->query("
    SELECT w.*, u.name, u.referral_id 
    FROM withdrawals w 
    JOIN users u ON w.user_id = u.id 
    ORDER BY w.request_date DESC
")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <h4 class="fw-bold mb-0">Withdrawal Requests</h4>
    <button class="btn btn-outline-primary"><i class="fas fa-file-excel me-2"></i> Export Report</button>
</div>

<?php echo $msg; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Req ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($withdrawals as $w): ?>
                    <tr>
                        <td class="text-muted">#<?php echo $w['id']; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($w['name']); ?></div>
                            <small class="text-muted"><?php echo htmlspecialchars($w['referral_id']); ?></small>
                        </td>
                        <td class="fw-bold">₹<?php echo number_format($w['amount'], 2); ?></td>
                        <td class="small text-muted"><?php echo date('d M Y, H:i', strtotime($w['request_date'])); ?></td>
                        <td>
                            <?php if($w['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php elseif($w['status'] == 'approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if($w['status'] == 'pending'): ?>
                                <a href="?action=approved&id=<?php echo $w['id']; ?>" class="btn btn-sm btn-success me-1" title="Approve"><i class="fas fa-check"></i></a>
                                <a href="?action=rejected&id=<?php echo $w['id']; ?>" class="btn btn-sm btn-danger" title="Reject"><i class="fas fa-times"></i></a>
                            <?php else: ?>
                                <span class="text-muted small">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($withdrawals)): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">No withdrawal requests.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
