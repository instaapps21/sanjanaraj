<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $reply = trim($_POST['admin_reply']);
    $status = $_POST['status']; // 'open' or 'resolved'
    
    $stmt = $pdo->prepare("UPDATE complaints SET admin_reply = ?, status = ? WHERE id = ?");
    if ($stmt->execute([$reply, $status, $ticket_id])) {
        $msg = "<div class='alert alert-success mt-3 py-2'>Ticket #$ticket_id updated.</div>";
    }
}

// Fetch all complaints with user info
$complaints = $pdo->query("
    SELECT c.*, u.name, u.referral_id 
    FROM complaints c 
    JOIN users u ON c.user_id = u.id 
    ORDER BY c.status ASC, c.created_at DESC
")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <h4 class="fw-bold mb-0">Complaint Management</h4>
</div>

<?php echo $msg; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Ticket ID</th>
                    <th>User</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($complaints as $c): ?>
                    <tr>
                        <td class="text-muted">#<?php echo $c['id']; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($c['name']); ?></div>
                            <small class="text-muted"><?php echo htmlspecialchars($c['referral_id']); ?></small>
                        </td>
                        <td class="fw-bold"><?php echo htmlspecialchars($c['subject']); ?></td>
                        <td>
                            <?php if($c['status'] == 'open'): ?>
                                <span class="badge bg-warning text-dark border border-warning">Open</span>
                            <?php else: ?>
                                <span class="badge bg-success border border-success">Resolved</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?php echo date('d M Y, H:i', strtotime($c['created_at'])); ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $c['id']; ?>">
                                <?php echo $c['status'] == 'open' ? 'Reply' : 'View'; ?>
                            </button>
                        </td>
                    </tr>

                    <!-- Reply Modal -->
                    <div class="modal fade" id="replyModal<?php echo $c['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg border-0 shadow">
                            <form method="POST" class="modal-content border-0">
                                <input type="hidden" name="ticket_id" value="<?php echo $c['id']; ?>">
                                <div class="modal-header bg-light border-0">
                                    <h5 class="modal-title fw-bold">Ticket #<?php echo $c['id']; ?> - <?php echo htmlspecialchars($c['subject']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-4 bg-light p-3 rounded border">
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                            <span class="fw-bold text-primary"><?php echo htmlspecialchars($c['name']); ?> wrote:</span>
                                            <span class="small text-muted"><?php echo date('d M Y, H:i', strtotime($c['created_at'])); ?></span>
                                        </div>
                                        <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($c['message'])); ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Your Reply:</label>
                                        <textarea name="admin_reply" class="form-control" rows="5" required><?php echo htmlspecialchars($c['admin_reply'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Ticket Status:</label>
                                        <select name="status" class="form-select">
                                            <option value="open" <?php echo $c['status'] == 'open' ? 'selected' : ''; ?>>Open</option>
                                            <option value="resolved" <?php echo $c['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved (Close Ticket)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save & Reply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($complaints)): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">No complaints found.</td></tr>
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
