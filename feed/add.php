<?php
/**
 * Add Feed Inventory Page
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
    $feedTypeId = (int)($_POST['feed_type_id'] ?? 0);
    $quantity = $_POST['quantity'] ?? 0;
    $unitPrice = $_POST['unit_price'] ?? null;
    $purchaseDate = $_POST['purchase_date'] ?? null;
    $expiryDate = $_POST['expiry_date'] ?? null;
    $supplier = Helper::sanitize($_POST['supplier'] ?? '');
    $batchNumber = Helper::sanitize($_POST['batch_number'] ?? '');
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    // Determine status
    $status = 'available';
    if ($expiryDate && strtotime($expiryDate) < time()) {
        $status = 'expired';
    } elseif ($quantity < 100) {
        $status = 'low_stock';
    }
    
    if (empty($feedTypeId) || $quantity <= 0) {
        $error = 'Feed type and quantity are required';
    } else {
        $sql = "INSERT INTO feed_inventory (feed_type_id, quantity, unit_price, purchase_date, expiry_date, supplier, batch_number, status, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $feedTypeId, $quantity, $unitPrice ?: null, $purchaseDate ?: null, $expiryDate ?: null,
            $supplier ?: null, $batchNumber ?: null, $status, $notes ?: null, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'feed/index.php?success=1');
            exit;
        } else {
            $error = 'Failed to add feed inventory';
        }
    }
}

$feedTypes = $db->fetchAll("SELECT * FROM feed_types ORDER BY name");

$pageTitle = 'Add Feed Inventory';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add Feed Inventory</h2>
            <a href="<?php echo BASE_URL; ?>feed/index.php" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="feed_type_id">Feed Type</label>
                        <select class="form-control" id="feed_type_id" name="feed_type_id" required>
                            <option value="">Select Feed Type</option>
                            <?php foreach ($feedTypes as $type): ?>
                                <option value="<?php echo $type['id']; ?>">
                                    <?php echo htmlspecialchars($type['name'] . ' (' . $type['category'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="unit_price">Unit Price</label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="purchase_date">Purchase Date</label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="expiry_date">Expiry Date</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="supplier">Supplier</label>
                        <input type="text" class="form-control" id="supplier" name="supplier">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="batch_number">Batch Number</label>
                    <input type="text" class="form-control" id="batch_number" name="batch_number">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Inventory</button>
                    <a href="<?php echo BASE_URL; ?>feed/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

