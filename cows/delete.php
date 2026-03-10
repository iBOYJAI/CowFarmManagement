<?php
/**
 * Delete Cow Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/FileUpload.php';

$auth = new Auth();
$auth->requireLogin();

// Only admin and manager can delete
$auth->requireRole([ROLE_ADMIN, ROLE_MANAGER]);

$db = new DBHelper();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ' . BASE_URL . 'cows/index.php');
    exit;
}

// Get cow data
$cow = $db->fetchOne("SELECT * FROM cows WHERE id = ?", [$id]);
if (!$cow) {
    header('Location: ' . BASE_URL . 'cows/index.php');
    exit;
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    // Delete photo if exists
    if ($cow['photo']) {
        FileUpload::deleteFile($cow['photo']);
    }
    
    // Delete cow (cascade will handle related records)
    $result = $db->execute("DELETE FROM cows WHERE id = ?", [$id]);
    
    if ($result) {
        header('Location: ' . BASE_URL . 'cows/index.php?deleted=1');
        exit;
    } else {
        $error = 'Failed to delete cow. Please try again.';
    }
}

$pageTitle = 'Delete Cow';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Delete Cow</h2>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="alert alert-warning">
                <strong>Warning!</strong> Are you sure you want to delete cow <strong><?php echo htmlspecialchars($cow['tag_number']); ?></strong>?
                This action cannot be undone. All related records (health, milk, breeding, etc.) will also be deleted.
            </div>
            
            <form method="POST">
                <input type="hidden" name="confirm" value="1">
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    <a href="<?php echo BASE_URL; ?>cows/view.php?id=<?php echo $cow['id']; ?>" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

