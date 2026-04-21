<?php
require_once '../includes/db.php';
include 'sidebar.php';

// A simple recursive function to fetch downline
function getDownline($pdo, $parent_id, $depth = 0) {
    if ($depth > 3) return ""; // Limit depth for display
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE parent_id = ? ORDER BY id ASC");
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
            $html .= "</div>";
            $html .= getDownline($pdo, $child['id'], $depth + 1);
            $html .= "</li>";
        }
        $html .= "</ul>";
    }
    return $html;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<style>
/* Simple CSS Tree structure */
.tree ul { padding-top: 20px; position: relative; transition: all 0.5s; }
.tree li { float: left; text-align: center; list-style-type: none; position: relative; padding: 20px 5px 0 5px; transition: all 0.5s; }
.tree li::before, .tree li::after{ content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #ccc; width: 50%; height: 20px; }
.tree li::after{ right: auto; left: 50%; border-left: 2px solid #ccc; }
.tree li:only-child::after, .tree li:only-child::before { display: none; }
.tree li:only-child{ padding-top: 0;}
.tree li:first-child::before, .tree li:last-child::after{ border: 0 none; }
.tree li:last-child::before{ border-right: 2px solid #ccc; border-radius: 0 5px 0 0; }
.tree li:first-child::after{ border-radius: 5px 0 0 0; }
.tree ul ul::before{ content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #ccc; width: 0; height: 20px; }
.tree li .node { display: inline-block; padding: 10px; text-decoration: none; border-radius: 5px; transition: all 0.5s; }
.tree li .node:hover { background: #f8f9fa !important; border-color: var(--fin-orange) !important; transform: translateY(-3px); }
</style>

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <h4 class="fw-bold mb-3 border-bottom pb-2">Network Tree</h4>
    <p class="text-muted small mb-4">Visualize your downline growth up to 3 levels deep.</p>
    
    <div class="tree d-flex justify-content-center overflow-auto pb-4">
        <ul>
            <li>
                <div class="node bg-warning shadow-sm border rounded p-2 text-center" style="min-width:120px;">
                    <div class="fw-bold text-dark">YOU</div>
                    <div class="small text-dark"><?php echo htmlspecialchars($user['referral_id']); ?></div>
                </div>
                <?php echo getDownline($pdo, $_SESSION['user_id']); ?>
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
