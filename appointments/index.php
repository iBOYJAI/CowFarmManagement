<?php
/**
 * Appointments List Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$where = ["1=1"];
$params = [];

if (!empty($status)) {
    $where[] = "a.status = ?";
    $params[] = $status;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT a.*, c.tag_number, c.name as cow_name 
          FROM appointments a 
          LEFT JOIN cows c ON a.cow_id = c.id 
          WHERE $whereClause 
          ORDER BY a.appointment_date, a.appointment_time";

$result = $db->fetchPaginated($query, $params, $page);
$appointments = $result['data'];
$totalPages = $result['total_pages'];

$pageTitle = 'Appointments';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Vet Appointments</h2>
            <a href="<?php echo BASE_URL; ?>appointments/add.php" class="btn btn-primary">Schedule Appointment</a>
        </div>
        <div class="card-body">
            <div class="search-filter-bar">
                <select name="status" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="scheduled" <?php echo $status === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <form method="GET" style="display: contents;">
                </form>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Cow</th>
                            <th>Vet</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #999;">No appointments found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $apt): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($apt['appointment_date']); ?></td>
                                    <td><?php echo $apt['appointment_time'] ? date('H:i', strtotime($apt['appointment_time'])) : '-'; ?></td>
                                    <td><?php echo $apt['cow_name'] ? htmlspecialchars($apt['cow_name']) : ($apt['tag_number'] ? htmlspecialchars($apt['tag_number']) : '-'); ?></td>
                                    <td><?php echo htmlspecialchars($apt['vet_name'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($apt['purpose'] ?? '-'); ?></td>
                                    <td><?php echo Helper::getStatusBadge($apt['status']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>appointments/view.php?id=<?php echo $apt['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                        <a href="<?php echo BASE_URL; ?>appointments/edit.php?id=<?php echo $apt['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php echo Helper::generatePagination($page, $totalPages, BASE_URL . 'appointments/index.php?status=' . urlencode($status)); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

