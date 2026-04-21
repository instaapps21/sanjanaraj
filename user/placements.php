<?php
require_once '../includes/db.php';
include 'sidebar.php';

$user_ref = $_SESSION['referral_id'];
$user_id = $_SESSION['user_id'];
$msg = '';

// Handle Placement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_user'])) {
    $target_id = (int)$_POST['target_id'];
    $position = $_POST['position'];

    if ($position === 'left' || $position === 'right') {
        // Check if the current user already has someone at this exact position
        $check = $pdo->prepare("SELECT id FROM users WHERE parent_id = ? AND binary_position = ?");
        $check->execute([$user_id, $position]);
        if ($check->rowCount() > 0) {
            $msg = "<div class='alert alert-danger'>You already have a direct member on the " . ucfirst($position) . " side. Spilling over to lower levels is required for further additions.</div>";
        } else {
            // Update the user
            $stmt = $pdo->prepare("UPDATE users SET parent_id = ?, binary_position = ? WHERE id = ? AND sponsor_id = ? AND binary_position IS NULL");
            if ($stmt->execute([$user_id, $position, $target_id, $user_ref])) {
                $msg = "<div class='alert alert-success'>User successfully placed on your " . ucfirst($position) . " side!</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Failed to place user. They may have already been placed.</div>";
            }
        }
    }
}

// Fetch unplaced direct referrals
// Uses sponsor_id instead of parent_id, because parent_id can be reassigned during placement
$stmt = $pdo->prepare("SELECT * FROM users WHERE sponsor_id = ? AND binary_position IS NULL ORDER BY created_at DESC");
$stmt->execute([$user_ref]);
$unplaced_users = $stmt->fetchAll();
?>

<style>
    .welcome-banner { background: linear-gradient(135deg, var(--fin-blue), #0a5a8a); color: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; }
</style>

<div class="welcome-banner shadow-sm">
    <div class="d-flex align-items-center">
        <div class="me-4 bg-white bg-opacity-25 rounded-circle d-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
            <i class="fas fa-handshake fa-2x"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1">Welcome New Joiners!</h3>
            <p class="mb-0 text-white-50">Manage your new direct referrals. Assign them to your binary tree to start earning team bonuses.</p>
        </div>
    </div>
</div>

<?php if ($msg) echo $msg; ?>

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <h5 class="fw-bold mb-3 border-bottom pb-2">Pending Placements (Holding Tank)</h5>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>New Member</th>
                    <th>Contact Details</th>
                    <th>Joined On</th>
                    <th class="text-end pe-4">Binary Assignment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($unplaced_users as $u): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['name']); ?></div>
                            <small class="text-muted">ID: <?php echo htmlspecialchars($u['referral_id']); ?></small>
                        </td>
                        <td>
                            <div><i class="fas fa-envelope text-muted me-1 small"></i> <?php echo htmlspecialchars($u['email']); ?></div>
                            <div><i class="fas fa-phone text-muted me-1 small"></i> <?php echo htmlspecialchars($u['phone']); ?></div>
                        </td>
                        <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td class="text-end pe-4">
                            <form method="POST" class="d-inline-block">
                                <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                <div class="btn-group shadow-sm">
                                    <button type="submit" name="place_user" class="btn btn-sm btn-outline-primary fw-bold" onclick="document.getElementById('pos_<?php echo $u['id']; ?>').value='left';"><i class="fas fa-arrow-left me-1"></i> Left Leg</button>
                                    <button type="submit" name="place_user" class="btn btn-sm btn-outline-primary fw-bold" onclick="document.getElementById('pos_<?php echo $u['id']; ?>').value='right';">Right Leg <i class="fas fa-arrow-right ms-1"></i></button>
                                </div>
                                <input type="hidden" name="position" id="pos_<?php echo $u['id']; ?>" value="">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($unplaced_users)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                            <h6 class="fw-bold text-secondary">No Pending Placements</h6>
                            <p class="small">You have placed all your direct referrals in the binary tree.</p>
                        </td>
                    </tr>
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
