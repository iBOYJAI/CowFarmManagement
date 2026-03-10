<?php
/**
 * Financial Report (Expenses & Sales)
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

$paramsExpenses = [];
$paramsSales = [];
$whereExpenses = 'WHERE 1=1';
$whereSales = 'WHERE 1=1';

if (!empty($fromDate)) {
    $whereExpenses .= ' AND expense_date >= ?';
    $whereSales .= ' AND sale_date >= ?';
    $paramsExpenses[] = $fromDate;
    $paramsSales[] = $fromDate;
}
if (!empty($toDate)) {
    $whereExpenses .= ' AND expense_date <= ?';
    $whereSales .= ' AND sale_date <= ?';
    $paramsExpenses[] = $toDate;
    $paramsSales[] = $toDate;
}

$expenses = $db->fetchAll("
    SELECT *
    FROM expenses
    $whereExpenses
    ORDER BY expense_date DESC
", $paramsExpenses);

$sales = $db->fetchAll("
    SELECT *
    FROM sales
    $whereSales
    ORDER BY sale_date DESC
", $paramsSales);

$totalExpenses = 0;
foreach ($expenses as $e) {
    $totalExpenses += (float)$e['amount'];
}

$totalSales = 0;
foreach ($sales as $s) {
    $totalSales += (float)$s['total_amount'];
}

$profit = $totalSales - $totalExpenses;

$pageTitle = 'Financial Report';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Financial Report</h2>
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
                <div style="margin-left:auto; display:flex; gap:8px;">
                    <a href="<?php echo BASE_URL; ?>reports/export.php?type=expenses&from=<?php echo urlencode($fromDate); ?>&to=<?php echo urlencode($toDate); ?>" class="btn btn-outline">
                        Export Expenses CSV
                    </a>
                    <a href="<?php echo BASE_URL; ?>reports/export.php?type=sales&from=<?php echo urlencode($fromDate); ?>&to=<?php echo urlencode($toDate); ?>" class="btn btn-outline">
                        Export Sales CSV
                    </a>
                </div>
            </form>

            <div class="dashboard-grid" style="margin-bottom: 20px;">
                <div class="dashboard-card">
                    <div class="dashboard-card-title">Total Sales</div>
                    <div class="dashboard-card-value">₹<?php echo number_format($totalSales, 2); ?></div>
                </div>
                <div class="dashboard-card">
                    <div class="dashboard-card-title">Total Expenses</div>
                    <div class="dashboard-card-value">₹<?php echo number_format($totalExpenses, 2); ?></div>
                </div>
                <div class="dashboard-card">
                    <div class="dashboard-card-title"><?php echo $profit >= 0 ? 'Profit' : 'Loss'; ?></div>
                    <div class="dashboard-card-value" style="color: <?php echo $profit >= 0 ? '#16a34a' : '#dc2626'; ?>">
                        ₹<?php echo number_format($profit, 2); ?>
                    </div>
                </div>
            </div>

            <h3>Expenses</h3>
            <div class="table-responsive" style="margin-bottom: 24px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Vendor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center; color:#999;">No expenses in selected period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $row): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($row['expense_date']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($row['category'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo number_format((float)$row['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['vendor']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h3>Sales</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Quantity (L)</th>
                            <th>Unit Price</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color:#999;">No sales in selected period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $row): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($row['sale_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo number_format((float)$row['milk_quantity'], 2); ?></td>
                                    <td><?php echo number_format((float)$row['unit_price'], 2); ?></td>
                                    <td><?php echo number_format((float)$row['total_amount'], 2); ?></td>
                                    <td><?php echo Helper::getStatusBadge($row['payment_status']); ?></td>
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


