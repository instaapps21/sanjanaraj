<?php
require_once '../includes/db.php';
include 'sidebar.php';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch direct referrals count (using sponsor_id to match 2-leg limitation logic)
$stmt = $pdo->prepare("SELECT count(*) FROM users WHERE sponsor_id = ?");
$stmt->execute([$user['referral_id']]);
$direct_referrals = $stmt->fetchColumn();

// Fetch Recent Activity
$activityStmt = $pdo->prepare("SELECT name, created_at FROM users WHERE sponsor_id = ? ORDER BY created_at DESC LIMIT 5");
$activityStmt->execute([$user['referral_id']]);
$recentActivities = $activityStmt->fetchAll();

// Referral Link Generator
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$referralLink = $protocol.$domainName."/sanjanaraj/register.php?ref=".$user['referral_id'];

// Fetch KYC Status
$kycStmt = $pdo->prepare("SELECT status FROM kyc_details WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1");
$kycStmt->execute([$_SESSION['user_id']]);
$kycStatus = $kycStmt->fetchColumn() ?: 'not_submitted';

$kycBadge = '';
$kycLabel = '';
if ($kycStatus === 'approved') {
    $kycBadge = 'bg-success';
    $kycLabel = 'Approved';
} elseif ($kycStatus === 'pending') {
    $kycBadge = 'bg-warning text-dark';
    $kycLabel = 'Pending';
} elseif ($kycStatus === 'rejected') {
    $kycBadge = 'bg-danger';
    $kycLabel = 'Rejected';
} else {
    $kycBadge = 'bg-secondary';
    $kycLabel = 'Not Submitted';
}
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid var(--fin-orange) !important;">
            <div class="card-body">
                <p class="text-muted mb-1 fw-semibold">Wallet Balance</p>
                <h3 class="fw-bold mb-0">₹ <?php echo number_format($user['wallet_balance'], 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid var(--fin-blue) !important;">
            <div class="card-body">
                <p class="text-muted mb-1 fw-semibold">Total Earned</p>
                <h3 class="fw-bold mb-0 text-success">₹ <?php echo number_format($user['total_earned'], 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 fw-semibold">Direct Referrals</p>
                <h3 class="fw-bold mb-0"><?php echo $direct_referrals; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white text-center d-flex align-items-center justify-content-center">
            <div class="card-body py-2">
                <p class="mb-1 opacity-75">Current Rank</p>
                <h4 class="fw-bold mb-0">Silver Exec</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #6c757d !important;">
            <div class="card-body">
                <p class="text-muted mb-1 fw-semibold">KYC Verification</p>
                <h4 class="fw-bold mb-0 mt-2"><span class="badge <?php echo $kycBadge; ?>"><?php echo $kycLabel; ?></span></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #198754 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 fw-semibold">Personal Volume (PV)</p>
                <h3 class="fw-bold mb-0 text-success"><?php echo number_format($user['pv'] ?? 0); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 fw-semibold">Business Volume (BV)</p>
                <h3 class="fw-bold mb-0 text-info"><?php echo number_format($user['bv'] ?? 0); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4 border-bottom pb-2">Your Referral Link</h5>
            <?php if ($direct_referrals >= 2): ?>
                <div class="alert alert-warning border-0" style="background-color: #fff3cd; color: #856404; border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Limit Reached:</strong> You have successfully filled your direct two legs. Your referral link is now disabled for new signups.
                </div>
                <div class="input-group mb-3 opacity-50">
                    <input type="text" class="form-control" id="refLink" value="<?php echo htmlspecialchars($referralLink); ?>" readonly disabled>
                    <button class="btn btn-secondary" disabled><i class="fas fa-lock"></i> Locked</button>
                </div>
            <?php else: ?>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="refLink" value="<?php echo htmlspecialchars($referralLink); ?>" readonly>
                    <button class="btn btn-primary" onclick="copyRef()"><i class="fas fa-copy"></i> Copy</button>
                </div>
                <p class="text-muted small">Share this link directly on social media to build your downline automatically. <?php echo (2 - $direct_referrals); ?> spots remaining.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Recent Activity</h5>
            <?php if (count($recentActivities) > 0): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentActivities as $act): ?>
                        <li class="list-group-item px-0 border-0 pb-2 pt-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 150px; font-size: 0.95rem;"><?php echo htmlspecialchars($act['name']); ?></div>
                                    <div class="small text-muted" style="font-size: 0.8rem;">Joined your downline</div>
                                </div>
                                <span class="badge bg-light text-secondary border rounded-pill shadow-sm" style="font-size: 0.75rem;"><?php echo date('M d', strtotime($act['created_at'])); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="text-center mt-4">
                    <i class="fas fa-history fa-2x text-light mb-2"></i>
                    <p class="text-muted small fst-italic">No new activity this week.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyRef() {
    var copyText = document.getElementById("refLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Referral link copied to clipboard!");
}
</script>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
