<?php
/**
 * Milk Production Report
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

// Filters
$fromDate = $_GET['from'] ?? date('Y-m-01');
$toDate = $_GET['to'] ?? date('Y-m-d');

$params = [];
$where = 'WHERE 1=1';

if (!empty($fromDate)) {
    $where .= ' AND mp.production_date >= ?';
    $params[] = $fromDate;
}
if (!empty($toDate)) {
    $where .= ' AND mp.production_date <= ?';
    $params[] = $toDate;
}

$records = $db->fetchAll("
    SELECT mp.*, c.tag_number, c.name AS cow_name
    FROM milk_production mp
    JOIN cows c ON mp.cow_id = c.id
    $where
    ORDER BY mp.production_date DESC, c.tag_number
", $params);

$pageTitle = 'Milk Production Report';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Milk Production Report</h2>
        </div>
        <div class="card-body">
            <form method="get" class="form-inline" style="margin-bottom: 16px; display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
                <div>
                    <label class="form-label">From</label>
                    <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($fromDate); ?>">
                </div>
                <div>
                    <label class="form-label">To</label>
                    <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($toDate); ?>">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                <div style="margin-left:auto;">
                    <a href="<?php echo BASE_URL; ?>reports/export.php?type=milk&from=<?php echo urlencode($fromDate); ?>&to=<?php echo urlencode($toDate); ?>" class="btn btn-outline">
                        Export CSV
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Cow Tag</th>
                            <th>Name</th>
                            <th>Session</th>
                            <th>Morning (L)</th>
                            <th>Evening (L)</th>
                            <th>Total (L)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; color:#999;">No records found for selected period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $row): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($row['production_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tag_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['cow_name'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($row['session'])); ?></td>
                                    <td><?php echo number_format($row['morning_yield'], 2); ?></td>
                                    <td><?php echo number_format($row['evening_yield'], 2); ?></td>
                                    <td><?php echo number_format($row['total_yield'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


