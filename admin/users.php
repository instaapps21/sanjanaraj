<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'block') {
        $pdo->query("UPDATE users SET package_id = 0 WHERE id = $id AND role='user'"); // Mock block/inactive
        $msg = "<div class='alert alert-info mt-3 py-2'>User marked as inactive.</div>";
    } elseif ($_GET['action'] == 'reset_password') {
        // Generate a random temporary password
        $tempPassword = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$"), 0, 10);
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        if ($stmt->execute([$hashedPassword, $id])) {
            $msg = "<div class='alert alert-success mt-3 py-3 fw-bold shadow-sm border-0' style='border-radius: 12px;'>
                        <div class='d-flex align-items-center'>
                            <div class='me-3 bg-success rounded-circle d-flex align-items-center justify-content-center' style='width: 40px; height: 40px;'>
                                <i class='fas fa-check text-white'></i>
                            </div>
                            <div>
                                <h6 class='mb-1 text-success'>Password Reset Successful!</h6>
                                Provide this temporary password to the user: <span class='text-danger fs-5 ms-1' style='font-family: monospace;'>" . htmlspecialchars($tempPassword) . "</span>
                                <div class='text-muted small mt-1 fw-normal'>Please advise them to change it immediately after login.</div>
                            </div>
                            </div>
                        </div>
                    </div>";
        }
    } elseif ($_GET['action'] == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        if ($stmt->execute([$id])) {
            $msg = "<div class='alert alert-success mt-3 py-2 fw-bold'>User successfully deleted.</div>";
        } else {
            $msg = "<div class='alert alert-danger mt-3 py-2'>Failed to delete user. Please ensure there are no dependent records preventing deletion.</div>";
        }
    }
}

// Handle role filter
$role_filter = isset($_GET['role']) && $_GET['role'] === 'customer' ? 'customer' : 'user';

// Fetch users based on filter
$users = $pdo->prepare("SELECT * FROM users WHERE role = ? ORDER BY id DESC");
$users->execute([$role_filter]);
$users = $users->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="btn-group shadow-sm">
        <a href="?role=user" class="btn <?php echo $role_filter == 'user' ? 'btn-primary' : 'btn-light border'; ?>">Distributors (SALES)</a>
        <a href="?role=customer" class="btn <?php echo $role_filter == 'customer' ? 'btn-primary' : 'btn-light border'; ?>">Retail Customers</a>
    </div>
</div>

<?php if(!empty($msg)) echo $msg; ?>

<div class="card border-0 shadow-sm rounded-4 mt-3">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><?php echo $role_filter == 'customer' ? 'Retail Customers' : 'Network Distributors'; ?></h5>
        <input type="text" class="form-control w-25" placeholder="Search users...">
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Name / Details</th>
                    <th>Referral Code</th>
                    <th>Sponsor</th>
                    <th>Wallet Balance</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                    <tr>
                        <td class="text-muted">#<?php echo $u['id']; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['name']); ?></div>
                            <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?> | <?php echo htmlspecialchars($u['phone']); ?></small>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo $u['referral_id']; ?></span></td>
                        <td><?php echo htmlspecialchars($u['sponsor_id']); ?></td>
                        <td class="fw-bold text-success">₹<?php echo number_format($u['wallet_balance'], 2); ?></td>
                        <td>
                            <?php if($u['package_id'] > 0): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">Actions <i class="fas fa-chevron-down ms-1 text-muted" style="font-size:10px;"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item" href="tree.php?id=<?php echo $u['id']; ?>"><i class="fas fa-eye me-2 text-primary"></i> View Tree</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2" href="users.php?action=reset_password&id=<?php echo $u['id']; ?>&role=<?php echo $role_filter; ?>" onclick="return confirm('Generate a new random password for this user?');"><i class="fas fa-key me-2 text-warning"></i> Reset Password</a></li>
                                    <li><a class="dropdown-item py-2 text-secondary" href="users.php?action=block&id=<?php echo $u['id']; ?>&role=<?php echo $role_filter; ?>"><i class="fas fa-ban me-2"></i> Mark Inactive</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger py-2 fw-bold" href="users.php?action=delete&id=<?php echo $u['id']; ?>&role=<?php echo $role_filter; ?>" onclick="return confirm('WARNING: Are you absolutely sure you want to permanently delete this user? This action cannot be undone.');"><i class="fas fa-trash-alt me-2"></i> Delete User</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">No users found.</td></tr>
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
