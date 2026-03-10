<?php
/**
 * Milk Production List Page
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
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(c.tag_number LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($cowId)) {
    $where[] = "mp.cow_id = ?";
    $params[] = $cowId;
}

if (!empty($dateFrom)) {
    $where[] = "mp.production_date >= ?";
    $params[] = $dateFrom;
}

if (!empty($dateTo)) {
    $where[] = "mp.production_date <= ?";
    $params[] = $dateTo;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT mp.*, c.tag_number, c.name as cow_name 
          FROM milk_production mp 
          JOIN cows c ON mp.cow_id = c.id 
          WHERE $whereClause 
          ORDER BY mp.production_date DESC, mp.created_at DESC";

$result = $db->fetchPaginated($query, $params, $page);
$records = $result['data'];
$totalPages = $result['total_pages'];

// Get statistics
$todayTotal = $db->fetchOne("SELECT SUM(total_yield) as total FROM milk_production WHERE production_date = CURDATE()")['total'] ?? 0;
$monthTotal = $db->fetchOne("SELECT SUM(total_yield) as total FROM milk_production WHERE MONTH(production_date) = MONTH(CURDATE()) AND YEAR(production_date) = YEAR(CURDATE())")['total'] ?? 0;

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' AND gender = 'female' ORDER BY tag_number");

$pageTitle = 'Milk Production';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <!-- Statistics Cards -->
    <div class="dashboard-grid" style="margin-bottom: 20px;">
        <div class="dashboard-card">
            <div class="dashboard-card-title">Today's Production</div>
            <div class="dashboard-card-value"><?php echo number_format($todayTotal, 2); ?> L</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-title">This Month</div>
            <div class="dashboard-card-value"><?php echo number_format($monthTotal, 2); ?> L</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Milk Production Records</h2>
            <a href="<?php echo BASE_URL; ?>milk/add.php" class="btn btn-primary">Add Record</a>
        </div>
        <div class="card-body">
            <form method="GET" class="search-filter-bar">
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" name="search" class="form-control" placeholder="Search..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <select name="cow_id" class="form-control" style="width: 200px;">
                    <option value="">All Cows</option>
                    <?php foreach ($cows as $cow): ?>
                        <option value="<?php echo $cow['id']; ?>" <?php echo $cowId == $cow['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="date_from" class="form-control" style="width: 150px;" 
                       value="<?php echo htmlspecialchars($dateFrom); ?>" placeholder="From Date">
                <input type="date" name="date_to" class="form-control" style="width: 150px;" 
                       value="<?php echo htmlspecialchars($dateTo); ?>" placeholder="To Date">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?php echo BASE_URL; ?>milk/index.php" class="btn btn-outline">Reset</a>
            </form>

            <div class="table-container" style="margin-top: 20px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Cow</th>
                            <th>Morning (L)</th>
                            <th>Evening (L)</th>
                            <th>Total (L)</th>
                            <th>Quality</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #999;">No milk production records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($record['production_date']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($record['tag_number']); ?></strong></td>
                                    <td><?php echo $record['morning_yield'] ? number_format($record['morning_yield'], 2) : '-'; ?></td>
                                    <td><?php echo $record['evening_yield'] ? number_format($record['evening_yield'], 2) : '-'; ?></td>
                                    <td><strong><?php echo number_format($record['total_yield'], 2); ?></strong></td>
                                    <td><?php echo htmlspecialchars($record['quality_grade'] ?? '-'); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>milk/edit.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                        <a href="<?php echo BASE_URL; ?>milk/delete.php?id=<?php echo $record['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           data-confirm="Are you sure you want to delete this record?">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php 
                $baseUrl = BASE_URL . 'milk/index.php?search=' . urlencode($search) . '&cow_id=' . urlencode($cowId) . '&date_from=' . urlencode($dateFrom) . '&date_to=' . urlencode($dateTo);
                echo Helper::generatePagination($page, $totalPages, $baseUrl); 
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

