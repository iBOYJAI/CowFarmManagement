<?php
/**
 * Health Records List Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$search = $_GET['search'] ?? '';
$cowId = $_GET['cow_id'] ?? '';
$type = $_GET['type'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(c.tag_number LIKE ? OR c.name LIKE ? OR hr.diagnosis LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($cowId)) {
    $where[] = "hr.cow_id = ?";
    $params[] = $cowId;
}

if (!empty($type)) {
    $where[] = "hr.record_type = ?";
    $params[] = $type;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT hr.*, c.tag_number, c.name as cow_name 
          FROM health_records hr 
          JOIN cows c ON hr.cow_id = c.id 
          WHERE $whereClause 
          ORDER BY hr.record_date DESC";

$result = $db->fetchPaginated($query, $params, $page);
$records = $result['data'];
$totalPages = $result['total_pages'];

// Get cows for filter
$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Health Records';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Health Records</h2>
            <a href="<?php echo BASE_URL; ?>health/add.php" class="btn btn-primary">Add Health Record</a>
        </div>
        <div class="card-body">
            <div class="search-filter-bar">
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" class="form-control" placeholder="Search..." 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           onkeyup="if(event.key==='Enter') this.form.submit()">
                </div>
                <select name="cow_id" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">All Cows</option>
                    <?php foreach ($cows as $cow): ?>
                        <option value="<?php echo $cow['id']; ?>" <?php echo $cowId == $cow['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="type" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="checkup" <?php echo $type === 'checkup' ? 'selected' : ''; ?>>Checkup</option>
                    <option value="treatment" <?php echo $type === 'treatment' ? 'selected' : ''; ?>>Treatment</option>
                    <option value="surgery" <?php echo $type === 'surgery' ? 'selected' : ''; ?>>Surgery</option>
                    <option value="injury" <?php echo $type === 'injury' ? 'selected' : ''; ?>>Injury</option>
                </select>
                <form method="GET" style="display: contents;">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="cow_id" value="<?php echo htmlspecialchars($cowId); ?>">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                </form>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Cow</th>
                            <th>Type</th>
                            <th>Diagnosis</th>
                            <th>Treatment</th>
                            <th>Vet</th>
                            <th>Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #999;">No health records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($record['record_date']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($record['tag_number']); ?></strong></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $record['record_type'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['diagnosis'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($record['treatment'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($record['vet_name'] ?? '-'); ?></td>
                                    <td><?php echo $record['cost'] ? '₹' . number_format($record['cost'], 2) : '-'; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>health/view.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                        <a href="<?php echo BASE_URL; ?>health/edit.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php echo Helper::generatePagination($page, $totalPages, BASE_URL . 'health/index.php?search=' . urlencode($search) . '&cow_id=' . urlencode($cowId) . '&type=' . urlencode($type)); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

