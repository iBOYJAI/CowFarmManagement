<?php
/**
 * Edit User Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole([ROLE_ADMIN, ROLE_MANAGER]);

$db = new DBHelper();
$error = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ' . BASE_URL . 'users/index.php');
    exit;
}

$user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
if (!$user) {
    header('Location: ' . BASE_URL . 'users/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Helper::sanitize($_POST['username'] ?? '');
    $email = Helper::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = Helper::sanitize($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? ROLE_STAFF;
    $phone = Helper::sanitize($_POST['phone'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    if (empty($username) || empty($email) || empty($fullName)) {
        $error = 'All required fields must be filled';
    } else {
        // Check if username or email exists (excluding current user)
        $existing = $db->fetchOne("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?", [$username, $email, $id]);
        if ($existing) {
            $error = 'Username or email already exists';
        } else {
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET username = ?, email = ?, password = ?, full_name = ?, role = ?, phone = ?, status = ? WHERE id = ?";
                    $params = [$username, $email, $hashedPassword, $fullName, $role, $phone ?: null, $status, $id];
                }
            } else {
                $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, role = ?, phone = ?, status = ? WHERE id = ?";
                $params = [$username, $email, $fullName, $role, $phone ?: null, $status, $id];
            }
            
            if (empty($error)) {
                $result = $db->execute($sql, $params);
                if ($result) {
                    header('Location: ' . BASE_URL . 'users/index.php?success=1');
                    exit;
                } else {
                    $error = 'Failed to update user';
                }
            }
        }
    }
    
    $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
}

$pageTitle = 'Edit User';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit User</h2>
            <a href="<?php echo BASE_URL; ?>users/index.php" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6">
                        <div class="form-help">Leave empty to keep current password</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                            <option value="manager" <?php echo $user['role'] === 'manager' ? 'selected' : ''; ?>>Manager</option>
                            <option value="vet" <?php echo $user['role'] === 'vet' ? 'selected' : ''; ?>>Vet</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="<?php echo BASE_URL; ?>users/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

