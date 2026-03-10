<?php
/**
 * Expenses & Sales List Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$tab = $_GET['tab'] ?? 'expenses';

if ($tab === 'expenses') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $category = $_GET['category'] ?? '';
    
    $where = ["1=1"];
    $params = [];
    
    if (!empty($category)) {
        $where[] = "category = ?";
        $params[] = $category;
    }
    
    $whereClause = implode(' AND ', $where);
    $query = "SELECT * FROM expenses WHERE $whereClause ORDER BY expense_date DESC";
    
    $result = $db->fetchPaginated($query, $params, $page);
    $expenses = $result['data'];
    $totalPages = $result['total_pages'];
    
    // Total of all matching expenses (same filters as list)
    $totalExpenses = $db->fetchOne("SELECT SUM(amount) as total FROM expenses WHERE $whereClause", $params)['total'] ?? 0;
} else {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    $query = "SELECT * FROM sales ORDER BY sale_date DESC";
    $result = $db->fetchPaginated($query, [], $page);
    $sales = $result['data'];
    $totalPages = $result['total_pages'];
    
    // Total of all sales
    $totalSales = $db->fetchOne("SELECT SUM(total_amount) as total FROM sales")['total'] ?? 0;
}

$pageTitle = 'Expenses & Sales';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <a href="?tab=expenses" class="btn <?php echo $tab === 'expenses' ? 'btn-primary' : 'btn-outline'; ?>">Expenses</a>
        <a href="?tab=sales" class="btn <?php echo $tab === 'sales' ? 'btn-primary' : 'btn-outline'; ?>">Sales</a>
    </div>

    <?php if ($tab === 'expenses'): ?>
        <div class="dashboard-grid" style="margin-bottom: 20px;">
            <div class="dashboard-card">
                <div class="dashboard-card-title">Monthly Expenses</div>
                <div class="dashboard-card-value">₹<?php echo number_format($totalExpenses, 2); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Expenses</h2>
                <div style="display:flex; gap:8px;">
                    <a href="<?php echo BASE_URL; ?>reports/export.php?type=expenses" class="btn btn-outline">Export CSV</a>
                    <a href="<?php echo BASE_URL; ?>expenses/add_expense.php" class="btn btn-primary">Add Expense</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Vendor</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($expenses)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #999;">No expenses found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($expenses as $expense): ?>
                                    <tr>
                                        <td><?php echo Helper::formatDate($expense['expense_date']); ?></td>
                                        <td><?php echo ucfirst($expense['category']); ?></td>
                                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                        <td><strong>₹<?php echo number_format($expense['amount'], 2); ?></strong></td>
                                        <td><?php echo htmlspecialchars($expense['vendor'] ?? '-'); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>expenses/edit_expense.php?id=<?php echo $expense['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="dashboard-grid" style="margin-bottom: 20px;">
            <div class="dashboard-card">
                <div class="dashboard-card-title">Monthly Sales</div>
                <div class="dashboard-card-value">₹<?php echo number_format($totalSales, 2); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Sales</h2>
                <div style="display:flex; gap:8px;">
                    <a href="<?php echo BASE_URL; ?>reports/export.php?type=sales" class="btn btn-outline">Export CSV</a>
                    <a href="<?php echo BASE_URL; ?>expenses/add_sale.php" class="btn btn-primary">Add Sale</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Quantity (L)</th>
                                <th>Unit Price</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sales)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #999;">No sales found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?php echo Helper::formatDate($sale['sale_date']); ?></td>
                                        <td><?php echo htmlspecialchars($sale['customer_name'] ?? '-'); ?></td>
                                        <td><?php echo number_format($sale['milk_quantity'], 2); ?></td>
                                        <td>₹<?php echo number_format($sale['unit_price'], 2); ?></td>
                                        <td><strong>₹<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
                                        <td><?php echo Helper::getStatusBadge($sale['payment_status']); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>expenses/view_sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

