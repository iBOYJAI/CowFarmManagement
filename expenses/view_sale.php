<?php
/**
 * View Sale Details
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ' . BASE_URL . 'expenses/index.php?tab=sales');
    exit;
}

$sale = $db->fetchOne("SELECT * FROM sales WHERE id = ?", [$id]);
if (!$sale) {
    header('Location: ' . BASE_URL . 'expenses/index.php?tab=sales');
    exit;
}

$pageTitle = 'Sale Details';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Sale Details</h2>
            <a href="<?php echo BASE_URL; ?>expenses/index.php?tab=sales" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; width: 200px;">Sale Date:</td>
                    <td style="padding: 8px 0;"><?php echo Helper::formatDate($sale['sale_date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Customer:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($sale['customer_name'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Milk Quantity:</td>
                    <td style="padding: 8px 0;"><?php echo number_format($sale['milk_quantity'], 2); ?> Liters</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Unit Price:</td>
                    <td style="padding: 8px 0;">$<?php echo number_format($sale['unit_price'], 2); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Total Amount:</td>
                    <td style="padding: 8px 0;"><strong>$<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Payment Status:</td>
                    <td style="padding: 8px 0;"><?php echo Helper::getStatusBadge($sale['payment_status']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Payment Method:</td>
                    <td style="padding: 8px 0;"><?php echo ucfirst($sale['payment_method']); ?></td>
                </tr>
                <?php if ($sale['invoice_number']): ?>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Invoice Number:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($sale['invoice_number']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($sale['notes']): ?>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Notes:</td>
                    <td style="padding: 8px 0;"><?php echo nl2br(htmlspecialchars($sale['notes'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

