<?php
/**
 * Add User Page
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Helper::sanitize($_POST['username'] ?? '');
    $email = Helper::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = Helper::sanitize($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? ROLE_STAFF;
    $phone = Helper::sanitize($_POST['phone'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        $error = 'All required fields must be filled';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if username or email exists
        $existing = $db->fetchOne("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
        if ($existing) {
            $error = 'Username or email already exists';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, full_name, role, phone, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $result = $db->execute($sql, [
                $username, $email, $hashedPassword, $fullName, $role, $phone ?: null, $status
            ]);
            
            if ($result) {
                header('Location: ' . BASE_URL . 'users/index.php?success=1');
                exit;
            } else {
                $error = 'Failed to add user';
            }
        }
    }
}

$pageTitle = 'Add User';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add New User</h2>
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
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <div class="form-help">Minimum 6 characters</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="manager">Manager</option>
                            <option value="vet">Vet</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add User</button>
                    <a href="<?php echo BASE_URL; ?>users/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

