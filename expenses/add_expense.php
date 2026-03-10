<?php
/**
 * Add Expense Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');
    $category = $_POST['category'] ?? '';
    $description = Helper::sanitize($_POST['description'] ?? '');
    $amount = $_POST['amount'] ?? 0;
    $vendor = Helper::sanitize($_POST['vendor'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cash';
    $receiptNumber = Helper::sanitize($_POST['receipt_number'] ?? '');
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    if (empty($category) || empty($description) || $amount <= 0) {
        $error = 'Category, description, and amount are required';
    } else {
        $sql = "INSERT INTO expenses (expense_date, category, description, amount, vendor, payment_method, receipt_number, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $expenseDate, $category, $description, $amount, $vendor ?: null,
            $paymentMethod, $receiptNumber ?: null, $notes ?: null, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'expenses/index.php?tab=expenses&success=1');
            exit;
        } else {
            $error = 'Failed to add expense';
        }
    }
}

$pageTitle = 'Add Expense';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add Expense</h2>
            <a href="<?php echo BASE_URL; ?>expenses/index.php?tab=expenses" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="expense_date">Date</label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="feed">Feed</option>
                            <option value="medicine">Medicine</option>
                            <option value="equipment">Equipment</option>
                            <option value="labor">Labor</option>
                            <option value="utilities">Utilities</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required" for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="vendor">Vendor</label>
                        <input type="text" class="form-control" id="vendor" name="vendor">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="check">Check</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="receipt_number">Receipt Number</label>
                        <input type="text" class="form-control" id="receipt_number" name="receipt_number">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Expense</button>
                    <a href="<?php echo BASE_URL; ?>expenses/index.php?tab=expenses" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

