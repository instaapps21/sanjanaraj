<?php
require_once '../includes/db.php';
include 'sidebar.php';

// Stats
$total_users = $pdo->query("SELECT count(*) FROM users WHERE role = 'user'")->fetchColumn();
$pending_with = $pdo->query("SELECT count(*) FROM withdrawals WHERE status='pending'")->fetchColumn();
$total_out = $pdo->query("SELECT sum(amount) FROM withdrawals WHERE status='approved'")->fetchColumn();

// New Stats
$pending_kyc = $pdo->query("SELECT count(*) FROM kyc_details WHERE status='pending'")->fetchColumn();
$total_pv = $pdo->query("SELECT sum(pv) FROM users")->fetchColumn();
$total_bv = $pdo->query("SELECT sum(bv) FROM users")->fetchColumn();

// Recent signups
$recent_users = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-primary">
            <div class="card-body">
                <p class="text-muted mb-1 text-uppercase small fw-bold">Total Network Users</p>
                <h2 class="fw-bold text-dark"><?php echo $total_users; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-warning">
            <div class="card-body">
                <p class="text-muted mb-1 text-uppercase small fw-bold">Pending Withdrawals</p>
                <h2 class="fw-bold text-dark"><?php echo $pending_with; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-success">
            <div class="card-body">
                <p class="text-muted mb-1 text-uppercase small fw-bold">Total Payouts Done</p>
                <h2 class="fw-bold text-success">₹ <?php echo number_format($total_out ?: 0, 2); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase small fw-bold">Pending KYC Requests</p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo $pending_kyc; ?></h2>
                    </div>
                    <a href="kyc_requests.php" class="btn btn-sm btn-outline-info">Review</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-secondary">
            <div class="card-body">
                <p class="text-muted mb-1 text-uppercase small fw-bold">Total Network PV</p>
                <h2 class="fw-bold text-success mb-0"><?php echo number_format($total_pv ?: 0); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-dark">
            <div class="card-body">
                <p class="text-muted mb-1 text-uppercase small fw-bold">Total Network BV</p>
                <h2 class="fw-bold text-info mb-0"><?php echo number_format($total_bv ?: 0); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Recent Signups</h5>
        <a href="users.php" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Referral Code</th>
                    <th>Sponsor</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_users as $u): ?>
                    <tr>
                        <td class="text-muted">#<?php echo $u['id']; ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo $u['referral_id']; ?></span></td>
                        <td><?php echo htmlspecialchars($u['sponsor_id']); ?></td>
                        <td class="small text-muted"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($recent_users)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No users found</td></tr>
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
