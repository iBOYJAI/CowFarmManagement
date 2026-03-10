<?php
/**
 * Edit Feed Inventory Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$error = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ' . BASE_URL . 'feed/index.php');
    exit;
}

$item = $db->fetchOne("SELECT fi.*, ft.name as feed_name FROM feed_inventory fi JOIN feed_types ft ON fi.feed_type_id = ft.id WHERE fi.id = ?", [$id]);
if (!$item) {
    header('Location: ' . BASE_URL . 'feed/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = $_POST['quantity'] ?? 0;
    $unitPrice = $_POST['unit_price'] ?? null;
    $purchaseDate = $_POST['purchase_date'] ?? null;
    $expiryDate = $_POST['expiry_date'] ?? null;
    $supplier = Helper::sanitize($_POST['supplier'] ?? '');
    $batchNumber = Helper::sanitize($_POST['batch_number'] ?? '');
    $status = $_POST['status'] ?? 'available';
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    if ($quantity <= 0) {
        $error = 'Quantity must be greater than 0';
    } else {
        $sql = "UPDATE feed_inventory SET quantity = ?, unit_price = ?, purchase_date = ?, expiry_date = ?, supplier = ?, batch_number = ?, status = ?, notes = ? WHERE id = ?";
        
        $result = $db->execute($sql, [
            $quantity, $unitPrice ?: null, $purchaseDate ?: null, $expiryDate ?: null,
            $supplier ?: null, $batchNumber ?: null, $status, $notes ?: null, $id
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'feed/index.php?success=1');
            exit;
        } else {
            $error = 'Failed to update feed inventory';
        }
    }
    
    $item = $db->fetchOne("SELECT fi.*, ft.name as feed_name FROM feed_inventory fi JOIN feed_types ft ON fi.feed_type_id = ft.id WHERE fi.id = ?", [$id]);
}

$feedTypes = $db->fetchAll("SELECT * FROM feed_types ORDER BY name");

$pageTitle = 'Edit Feed Inventory';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Feed Inventory</h2>
            <a href="<?php echo BASE_URL; ?>feed/index.php" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Feed Type</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($item['feed_name']); ?>" disabled>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0" 
                               value="<?php echo $item['quantity']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="unit_price">Unit Price</label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" min="0" 
                               value="<?php echo $item['unit_price'] ?: ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="purchase_date">Purchase Date</label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" 
                               value="<?php echo $item['purchase_date'] ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="expiry_date">Expiry Date</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                               value="<?php echo $item['expiry_date'] ?: ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="supplier">Supplier</label>
                        <input type="text" class="form-control" id="supplier" name="supplier" 
                               value="<?php echo htmlspecialchars($item['supplier'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="batch_number">Batch Number</label>
                        <input type="text" class="form-control" id="batch_number" name="batch_number" 
                               value="<?php echo htmlspecialchars($item['batch_number'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="available" <?php echo $item['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="low_stock" <?php echo $item['status'] === 'low_stock' ? 'selected' : ''; ?>>Low Stock</option>
                        <option value="out_of_stock" <?php echo $item['status'] === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                        <option value="expired" <?php echo $item['status'] === 'expired' ? 'selected' : ''; ?>>Expired</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($item['notes'] ?? ''); ?></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Inventory</button>
                    <a href="<?php echo BASE_URL; ?>feed/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

