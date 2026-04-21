<?php
require_once '../includes/db.php';
include 'sidebar.php';

$success = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amount'])) {
    $amount = (float)$_POST['amount'];
    if ($amount < 500) {
        $error = "Minimum withdrawal amount is ₹500.";
    } elseif ($amount > $user['wallet_balance']) {
        $error = "Insufficient wallet balance.";
    } else {
        // Mock deduction and request
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
            $stmt->execute([$amount, $_SESSION['user_id']]);
            
            $stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, amount) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $amount]);

            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'debit', 'Withdrawal Request')");
            $stmt->execute([$_SESSION['user_id'], $amount]);
            
            $pdo->commit();
            $success = "Withdrawal request of ₹$amount submitted successfully.";
            $user['wallet_balance'] -= $amount; // update local var for display
        } catch (\Exception $e) {
            $pdo->rollBack();
            $error = "Failed to process request.";
        }
    }
}

// Fetch transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();

// Fetch withdrawals
$stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE user_id = ? ORDER BY request_date DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$withdrawals = $stmt->fetchAll();
?>

<div class="row g-4">
    <div class="col-lg-5">
        <!-- Balance Card -->
        <div class="card border-0 shadow-sm rounded-4 text-white text-center p-5 mb-4" style="background: linear-gradient(135deg, var(--fin-blue) 0%, #00b4d8 100%);">
            <h5 class="opacity-75 mb-1">Available Balance</h5>
            <h1 class="display-4 fw-bold mb-0">₹ <?php echo number_format($user['wallet_balance'], 2); ?></h1>
        </div>

        <!-- Withdraw Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-3">Request Withdrawal</h5>
            <?php 
                if($success) echo "<div class='alert alert-success small py-2'>$success</div>";
                if($error) echo "<div class='alert alert-danger small py-2'>$error</div>";
            ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-muted small">Amount (Min ₹500)</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" class="form-control" name="amount" min="500" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 pb-2">Submit Request</button>
            </form>
        </div>
        
        <!-- Withdrawal History (Short) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Recent Requests</h6>
            <?php if(empty($withdrawals)): ?>
                <p class="text-muted small mb-0">No withdrawal history available.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach($withdrawals as $w): ?>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block fw-bold text-dark">₹<?php echo number_format($w['amount'],2); ?></span>
                                <small class="text-muted"><?php echo date('d M Y', strtotime($w['request_date'])); ?></small>
                            </div>
                            <span class="badge bg-<?php echo $w['status'] == 'pending' ? 'warning text-dark' : ($w['status'] == 'approved' ? 'success' : 'danger'); ?>">
                                <?php echo ucfirst($w['status']); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-7">
        <!-- Transaction Ledger -->
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-3">Wallet Transactions</h5>
            <?php if(empty($transactions)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-receipt fs-1 mb-3 opacity-25"></i>
                    <p>No transactions found. Build your network to earn.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($transactions as $t): ?>
                                <tr>
                                    <td class="text-muted small"><?php echo date('d M, H:i', strtotime($t['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($t['description']); ?></td>
                                    <td>
                                        <?php if($t['type'] == 'credit'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">Credit</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Debit</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold <?php echo $t['type'] == 'credit' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $t['type'] == 'credit' ? '+' : '-'; ?>₹<?php echo number_format($t['amount'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
