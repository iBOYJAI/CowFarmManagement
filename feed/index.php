<?php
/**
 * Feed & Inventory List Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$query = "SELECT fi.*, ft.name as feed_name, ft.category, ft.unit 
          FROM feed_inventory fi 
          JOIN feed_types ft ON fi.feed_type_id = ft.id 
          ORDER BY fi.created_at DESC";

$result = $db->fetchPaginated($query, [], $page);
$inventory = $result['data'];
$totalPages = $result['total_pages'];

// Get statistics
$totalStock = $db->fetchOne("SELECT SUM(quantity) as total FROM feed_inventory WHERE status = 'available'")['total'] ?? 0;
$lowStock = $db->fetchOne("SELECT COUNT(*) as count FROM feed_inventory WHERE status = 'low_stock'")['count'] ?? 0;

$pageTitle = 'Feed & Inventory';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="dashboard-grid" style="margin-bottom: 20px;">
        <div class="dashboard-card">
            <div class="dashboard-card-title">Total Stock</div>
            <div class="dashboard-card-value"><?php echo number_format($totalStock, 2); ?> kg</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-title">Low Stock Items</div>
            <div class="dashboard-card-value"><?php echo $lowStock; ?></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Feed Inventory</h2>
            <a href="<?php echo BASE_URL; ?>feed/add.php" class="btn btn-primary">Add Stock</a>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Feed Type</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventory)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #999;">No inventory records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventory as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['feed_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['category'] ?? '-'); ?></td>
                                    <td><?php echo number_format($item['quantity'], 2) . ' ' . htmlspecialchars($item['unit']); ?></td>
                                    <td>$<?php echo $item['unit_price'] ? number_format($item['unit_price'], 2) : '-'; ?></td>
                                    <td>$<?php echo number_format($item['quantity'] * ($item['unit_price'] ?? 0), 2); ?></td>
                                    <td><?php echo Helper::getStatusBadge($item['status']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>feed/edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php echo Helper::generatePagination($page, $totalPages, BASE_URL . 'feed/index.php'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

