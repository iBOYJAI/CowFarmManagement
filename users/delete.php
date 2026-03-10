<?php
/**
 * Delete User Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole([ROLE_ADMIN]);

$db = new DBHelper();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ' . BASE_URL . 'users/index.php');
    exit;
}

// Prevent self-deletion
if ($id == $_SESSION['user_id']) {
    header('Location: ' . BASE_URL . 'users/index.php?error=self_delete');
    exit;
}

$user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
if (!$user) {
    header('Location: ' . BASE_URL . 'users/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $result = $db->execute("DELETE FROM users WHERE id = ?", [$id]);
    if ($result) {
        header('Location: ' . BASE_URL . 'users/index.php?deleted=1');
        exit;
    }
}

$pageTitle = 'Delete User';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Delete User</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                Are you sure you want to delete user <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>?
                This action cannot be undone.
            </div>
            <form method="POST">
                <input type="hidden" name="confirm" value="1">
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    <a href="<?php echo BASE_URL; ?>users/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

