<?php
/**
 * Breeding Records List Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(c.tag_number LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    $where[] = "br.pregnancy_status = ?";
    $params[] = $status;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT br.*, c.tag_number, c.name as cow_name 
          FROM breeding_records br 
          JOIN cows c ON br.cow_id = c.id 
          WHERE $whereClause 
          ORDER BY br.breeding_date DESC";

$result = $db->fetchPaginated($query, $params, $page);
$records = $result['data'];
$totalPages = $result['total_pages'];

$pageTitle = 'Breeding & Pregnancy';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Breeding & Pregnancy Records</h2>
            <a href="<?php echo BASE_URL; ?>breeding/add.php" class="btn btn-primary">Add Breeding Record</a>
        </div>
        <div class="card-body">
            <div class="search-filter-bar">
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Search..." 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           onkeyup="if(event.key==='Enter') this.form.submit()">
                </div>
                <select name="status" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pregnant" <?php echo $status === 'pregnant' ? 'selected' : ''; ?>>Pregnant</option>
                    <option value="not_pregnant" <?php echo $status === 'not_pregnant' ? 'selected' : ''; ?>>Not Pregnant</option>
                    <option value="delivered" <?php echo $status === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                </select>
                <form method="GET" style="display: contents;">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Breeding Date</th>
                            <th>Cow</th>
                            <th>Type</th>
                            <th>Expected Calving</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">No breeding records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($record['breeding_date']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($record['tag_number']); ?></strong></td>
                                    <td><?php echo strtoupper($record['breeding_type']); ?></td>
                                    <td><?php echo $record['expected_calving_date'] ? Helper::formatDate($record['expected_calving_date']) : '-'; ?></td>
                                    <td><?php echo Helper::getStatusBadge($record['pregnancy_status']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>breeding/view.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                        <a href="<?php echo BASE_URL; ?>breeding/edit.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php echo Helper::generatePagination($page, $totalPages, BASE_URL . 'breeding/index.php?search=' . urlencode($search) . '&status=' . urlencode($status)); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

