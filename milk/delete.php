<?php
/**
 * Delete Milk Production Record
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ' . BASE_URL . 'milk/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $result = $db->execute("DELETE FROM milk_production WHERE id = ?", [$id]);
    if ($result) {
        header('Location: ' . BASE_URL . 'milk/index.php?deleted=1');
        exit;
    }
}

$record = $db->fetchOne("SELECT * FROM milk_production WHERE id = ?", [$id]);
if (!$record) {
    header('Location: ' . BASE_URL . 'milk/index.php');
    exit;
}

$pageTitle = 'Delete Milk Record';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Delete Milk Production Record</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                Are you sure you want to delete this milk production record? This action cannot be undone.
            </div>
            <form method="POST">
                <input type="hidden" name="confirm" value="1">
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    <a href="<?php echo BASE_URL; ?>milk/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

