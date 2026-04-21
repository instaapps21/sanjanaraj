<?php
require_once '../includes/db.php';
include 'sidebar.php';

// Fetch target user 
$target_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$target_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>User not found. Return to <a href='users.php'>Users Management</a>.</div></div>";
    exit;
}

// A simple recursive function to fetch downline
function getAdminDownline($pdo, $parent_id, $depth = 0) {
    if ($depth > 5) return ""; // Limit depth for display
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE parent_id = ? ORDER BY binary_position ASC, id ASC");
    $stmt->execute([$parent_id]);
    $children = $stmt->fetchAll();
    
    $html = "";
    if (count($children) > 0) {
        $html .= "<ul>";
        foreach ($children as $child) {
            $html .= "<li>";
            $html .= "<div class='node bg-white shadow-sm border rounded p-2 text-center' style='min-width:120px;'>";
            $html .= "<div class='fw-bold text-primary'>" . htmlspecialchars($child['name']) . "</div>";
            $html .= "<div class='small text-muted'>" . htmlspecialchars($child['referral_id']) . "</div>";
            $positionLabel = $child['binary_position'] ? "<span class='badge bg-light text-dark border'>".ucfirst($child['binary_position'])."</span>" : "<span class='badge bg-warning'>Unplaced</span>";
            $html .= "<div class='mt-1'>$positionLabel</div>";
            $html .= "</div>";
            $html .= getAdminDownline($pdo, $child['id'], $depth + 1);
            $html .= "</li>";
        }
        $html .= "</ul>";
    }
    return $html;
}
?>
<style>
/* Simple CSS Tree structure */
.tree ul { padding-top: 20px; position: relative; transition: all 0.5s; display: flex; justify-content: center; padding-left: 0; }
.tree li { float: left; text-align: center; list-style-type: none; position: relative; padding: 20px 5px 0 5px; transition: all 0.5s; }
.tree li::before, .tree li::after{ content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #ccc; width: 50%; height: 20px; }
.tree li::after{ right: auto; left: 50%; border-left: 2px solid #ccc; }
.tree li:only-child::after, .tree li:only-child::before { display: none; }
.tree li:only-child{ padding-top: 0;}
.tree li:first-child::before, .tree li:last-child::after{ border: 0 none; }
.tree li:last-child::before{ border-right: 2px solid #ccc; border-radius: 0 5px 0 0; }
.tree li:first-child::after{ border-radius: 5px 0 0 0; }
.tree ul ul::before{ content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #ccc; width: 0; height: 20px; }
.tree li .node { display: inline-block; padding: 10px; text-decoration: none; border-radius: 5px; transition: all 0.5s; position: relative; z-index: 1;}
.tree li .node:hover { background: #f8f9fa !important; border-color: var(--fin-orange) !important; transform: translateY(-3px); }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Network Tree for: <span class="text-primary"><?php echo htmlspecialchars($user['name']); ?></span></h4>
    <a href="users.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to Users</a>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="min-height: 500px;">
    <p class="text-muted small mb-4 text-center">Viewing up to 5 levels of the downline structure.</p>
    
    <div class="tree d-flex justify-content-center overflow-auto pb-4 w-100">
        <ul>
            <li>
                <div class="node bg-warning shadow-sm border rounded p-2 text-center" style="min-width:140px; border-color: #e0a800 !important;">
                    <div class="fw-bold text-dark text-truncate" style="max-width: 140px;" title="<?php echo htmlspecialchars($user['name']); ?>"><?php echo htmlspecialchars($user['name']); ?></div>
                    <div class="small text-dark fw-bold"><?php echo htmlspecialchars($user['referral_id']); ?></div>
                    <div class="mt-1"><span class="badge bg-dark text-white">ROOT</span></div>
                </div>
                <?php echo getAdminDownline($pdo, $user['id']); ?>
            </li>
        </ul>
    </div>
</div>

        </div> <!-- End Main Content padding -->
    </div> <!-- End Main Content -->
</div> <!-- End flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
