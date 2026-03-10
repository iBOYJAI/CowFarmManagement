<?php
/**
 * Users Management Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole([ROLE_ADMIN, ROLE_MANAGER]);

$db = new DBHelper();

$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role)) {
    $where[] = "role = ?";
    $params[] = $role;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT * FROM users WHERE $whereClause ORDER BY created_at DESC";

$result = $db->fetchPaginated($query, $params, $page);
$users = $result['data'];
$totalPages = $result['total_pages'];

$pageTitle = 'User Management';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">User Management</h2>
            <a href="<?php echo BASE_URL; ?>users/add.php" class="btn btn-primary">Add User</a>
        </div>
        <div class="card-body">
            <div class="search-filter-bar">
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Search..." 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           onkeyup="if(event.key==='Enter') this.form.submit()">
                </div>
                <select name="role" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="vet" <?php echo $role === 'vet' ? 'selected' : ''; ?>>Vet</option>
                    <option value="manager" <?php echo $role === 'manager' ? 'selected' : ''; ?>>Manager</option>
                    <option value="staff" <?php echo $role === 'staff' ? 'selected' : ''; ?>>Staff</option>
                </select>
                <form method="GET" style="display: contents;">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo ucfirst($user['role']); ?></td>
                                    <td><?php echo Helper::getStatusBadge($user['status']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>users/edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="<?php echo BASE_URL; ?>users/delete.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               data-confirm="Are you sure?">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php echo Helper::generatePagination($page, $totalPages, BASE_URL . 'users/index.php?search=' . urlencode($search) . '&role=' . urlencode($role)); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

