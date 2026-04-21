<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subject'])) {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    $stmt = $pdo->prepare("INSERT INTO complaints (user_id, subject, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$_SESSION['user_id'], $subject, $message])) {
        $msg = "<div class='alert alert-success mt-3 py-2'>Ticket created successfully. The admin will reply soon.</div>";
    }
}

$tickets = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$tickets->execute([$_SESSION['user_id']]);
$tickets = $tickets->fetchAll();
?>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Submit Support Ticket</h5>
            <?php echo $msg; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="E.g. Issue with payout" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Message</label>
                    <textarea name="message" class="form-control" rows="6" placeholder="Describe your issue in detail..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Ticket</button>
            </form>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2">Your Ticket History</h5>
            
            <?php if(empty($tickets)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-check-circle fs-1 mb-2 opacity-25"></i>
                    <p>No support tickets found.</p>
                </div>
            <?php else: ?>
                <div class="accordion" id="ticketsAccordion">
                    <?php foreach($tickets as $index => $t): ?>
                        <div class="accordion-item mb-2 border rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed <?php echo $t['status'] == 'resolved' ? 'bg-light' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#ticket<?php echo $t['id']; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($t['subject']); ?></span>
                                        <?php if($t['status'] == 'open'): ?>
                                            <span class="badge bg-warning text-dark border border-warning">Open</span>
                                        <?php else: ?>
                                            <span class="badge bg-success border border-success">Resolved</span>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="ticket<?php echo $t['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#ticketsAccordion">
                                <div class="accordion-body bg-white">
                                    <p class="text-muted small border-bottom pb-2 mb-2">Created: <?php echo date('d M Y, H:i', strtotime($t['created_at'])); ?></p>
                                    <div class="mb-3">
                                        <strong>You wrote:</strong><br>
                                        <span class="text-secondary"><?php echo nl2br(htmlspecialchars($t['message'])); ?></span>
                                    </div>
                                    <div class="bg-light p-3 rounded border">
                                        <strong>Admin Reply:</strong><br>
                                        <?php if(empty($t['admin_reply'])): ?>
                                            <span class="text-muted fst-italic">Waiting for reply...</span>
                                        <?php else: ?>
                                            <span class="text-dark"><?php echo nl2br(htmlspecialchars($t['admin_reply'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
