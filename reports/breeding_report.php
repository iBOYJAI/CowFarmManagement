<?php
/**
 * Breeding & Pregnancy Report
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
    $where .= ' AND br.breeding_date >= ?';
    $params[] = $fromDate;
}
if (!empty($toDate)) {
    $where .= ' AND br.breeding_date <= ?';
    $params[] = $toDate;
}

$records = $db->fetchAll("
    SELECT br.*, c.tag_number, c.name AS cow_name
    FROM breeding_records br
    JOIN cows c ON br.cow_id = c.id
    $where
    ORDER BY br.breeding_date DESC, c.tag_number
", $params);

$pageTitle = 'Breeding Report';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Breeding & Pregnancy Report</h2>
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
                    <a href="<?php echo BASE_URL; ?>reports/export.php?type=breeding&from=<?php echo urlencode($fromDate); ?>&to=<?php echo urlencode($toDate); ?>" class="btn btn-outline">
                        Export CSV
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Breeding Date</th>
                            <th>Cow Tag</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Bull Tag</th>
                            <th>Expected Calving</th>
                            <th>Actual Calving</th>
                            <th>Status</th>
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
                                    <td><?php echo Helper::formatDate($row['breeding_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tag_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['cow_name'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['breeding_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['bull_tag']); ?></td>
                                    <td><?php echo $row['expected_calving_date'] ? Helper::formatDate($row['expected_calving_date']) : '-'; ?></td>
                                    <td><?php echo $row['actual_calving_date'] ? Helper::formatDate($row['actual_calving_date']) : '-'; ?></td>
                                    <td><?php echo Helper::getStatusBadge($row['pregnancy_status']); ?></td>
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


