<?php
require_once '../includes/db.php';
include 'sidebar.php';

$msg = '';

// Handle POST insertion/update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $type = $_POST['type'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
        
        $stmt = $pdo->prepare("INSERT INTO news_events (type, title, content, event_date) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$type, $title, $content, $event_date])) {
            $msg = "<div class='alert alert-success mt-3 py-2'>Successfully published.</div>";
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM news_events WHERE id = ?");
    if ($stmt->execute([(int)$_GET['delete']])) {
        $msg = "<div class='alert alert-warning mt-3 py-2'>Item deleted.</div>";
    }
}

$items = $pdo->query("SELECT * FROM news_events ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <h4 class="fw-bold mb-0">News & Events Management</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal"><i class="fas fa-plus me-1"></i> Add New</button>
</div>

<?php echo $msg; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Date / Event Details</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $i): ?>
                    <tr>
                        <td>
                            <?php if($i['type'] == 'news'): ?>
                                <span class="badge bg-primary">News</span>
                            <?php elseif($i['type'] == 'announcement'): ?>
                                <span class="badge bg-warning text-dark">Announcement</span>
                            <?php else: ?>
                                <span class="badge bg-success">Event</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?php echo htmlspecialchars($i['title']); ?></td>
                        <td>
                            <?php if($i['type'] == 'event'): ?>
                                <span class="text-success fw-bold"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y', strtotime($i['event_date'])); ?></span>
                            <?php else: ?>
                                <span class="text-muted small">Posted: <?php echo date('d M Y', strtotime($i['created_at'])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="?delete=<?php echo $i['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($items)): ?>
                    <tr><td colspan="4" class="text-center py-5 text-muted">No news or events found. Click "Add New" to publish.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg border-0 shadow">
        <form method="POST" class="modal-content border-0">
            <input type="hidden" name="action" value="create">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Publish Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Type</label>
                    <select name="type" class="form-select" id="contentTypeCombobox" required>
                        <option value="news">Company News</option>
                        <option value="announcement">Announcement</option>
                        <option value="event">Upcoming Event</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <!-- Conditional Event Date -->
                <div class="mb-3" id="dateWrapper" style="display:none;">
                    <label class="form-label fw-bold small text-muted">Event Date</label>
                    <input type="date" name="event_date" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Description / Content</label>
                    <textarea name="content" class="form-control" rows="6" required></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Publish</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('contentTypeCombobox').addEventListener('change', function(){
        if(this.value === 'event') {
            document.getElementById('dateWrapper').style.display = 'block';
        } else {
            document.getElementById('dateWrapper').style.display = 'none';
        }
    });
</script>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
