<?php
/**
 * Health Records Report
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$fromDate = $_GET['from'] ?? date('Y-m-01');
$toDate = $_GET['to'] ?? date('Y-m-d');

$params = [];
$where = 'WHERE 1=1';

if (!empty($fromDate)) {
    $where .= ' AND hr.record_date >= ?';
    $params[] = $fromDate;
}
if (!empty($toDate)) {
    $where .= ' AND hr.record_date <= ?';
    $params[] = $toDate;
}

$records = $db->fetchAll("
    SELECT hr.*, c.tag_number, c.name AS cow_name
    FROM health_records hr
    JOIN cows c ON hr.cow_id = c.id
    $where
    ORDER BY hr.record_date DESC, c.tag_number
", $params);

$pageTitle = 'Health Records Report';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Health Records Report</h2>
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
                    <a href="<?php echo BASE_URL; ?>reports/export.php?type=health&from=<?php echo urlencode($fromDate); ?>&to=<?php echo urlencode($toDate); ?>" class="btn btn-outline">
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
                            <th>Type</th>
                            <th>Diagnosis</th>
                            <th>Treatment</th>
                            <th>Vet</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center; color:#999;">No records found for selected period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $row): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($row['record_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tag_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['cow_name'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($row['record_type'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>
                                    <td><?php echo htmlspecialchars($row['treatment']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vet_name']); ?></td>
                                    <td><?php echo number_format((float)$row['cost'], 2); ?></td>
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


