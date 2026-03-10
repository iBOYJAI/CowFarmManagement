<?php
/**
 * Add Sale Page
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
    $saleDate = $_POST['sale_date'] ?? date('Y-m-d');
    $customerName = Helper::sanitize($_POST['customer_name'] ?? '');
    $milkQuantity = $_POST['milk_quantity'] ?? 0;
    $unitPrice = $_POST['unit_price'] ?? 0;
    $paymentStatus = $_POST['payment_status'] ?? 'paid';
    $paymentMethod = $_POST['payment_method'] ?? 'cash';
    $invoiceNumber = Helper::sanitize($_POST['invoice_number'] ?? '');
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    $totalAmount = $milkQuantity * $unitPrice;
    
    if (empty($customerName) || $milkQuantity <= 0 || $unitPrice <= 0) {
        $error = 'Customer name, quantity, and unit price are required';
    } else {
        $sql = "INSERT INTO sales (sale_date, customer_name, milk_quantity, unit_price, total_amount, payment_status, payment_method, invoice_number, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $saleDate, $customerName, $milkQuantity, $unitPrice, $totalAmount,
            $paymentStatus, $paymentMethod, $invoiceNumber ?: null, $notes ?: null, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'expenses/index.php?tab=sales&success=1');
            exit;
        } else {
            $error = 'Failed to add sale';
        }
    }
}

$pageTitle = 'Add Sale';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add Sale</h2>
            <a href="<?php echo BASE_URL; ?>expenses/index.php?tab=sales" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" id="saleForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="sale_date">Sale Date</label>
                        <input type="date" class="form-control" id="sale_date" name="sale_date" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="customer_name">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="milk_quantity">Milk Quantity (Liters)</label>
                        <input type="number" class="form-control" id="milk_quantity" name="milk_quantity" 
                               step="0.01" min="0" required oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="unit_price">Unit Price</label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price" 
                               step="0.01" min="0" required oninput="calculateTotal()">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Total Amount</label>
                    <input type="text" class="form-control" id="total_amount" value="0.00" disabled>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="payment_status">Payment Status</label>
                        <select class="form-control" id="payment_status" name="payment_status">
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="check">Check</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="invoice_number">Invoice Number</label>
                    <input type="text" class="form-control" id="invoice_number" name="invoice_number">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Sale</button>
                    <a href="<?php echo BASE_URL; ?>expenses/index.php?tab=sales" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const quantity = parseFloat(document.getElementById('milk_quantity').value) || 0;
    const price = parseFloat(document.getElementById('unit_price').value) || 0;
    document.getElementById('total_amount').value = (quantity * price).toFixed(2);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

