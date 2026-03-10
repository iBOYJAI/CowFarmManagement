<?php
/**
 * View Health Record
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
    header('Location: ' . BASE_URL . 'health/index.php');
    exit;
}

$record = $db->fetchOne("SELECT hr.*, c.tag_number, c.name as cow_name FROM health_records hr JOIN cows c ON hr.cow_id = c.id WHERE hr.id = ?", [$id]);

if (!$record) {
    header('Location: ' . BASE_URL . 'health/index.php');
    exit;
}

$pageTitle = 'Health Record Details';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Health Record Details</h2>
            <div>
                <a href="<?php echo BASE_URL; ?>health/edit.php?id=<?php echo $record['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="<?php echo BASE_URL; ?>health/index.php" class="btn btn-outline">Back</a>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; width: 200px;">Cow:</td>
                    <td style="padding: 8px 0;"><strong><?php echo htmlspecialchars($record['tag_number']); ?></strong></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Date:</td>
                    <td style="padding: 8px 0;"><?php echo Helper::formatDate($record['record_date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Type:</td>
                    <td style="padding: 8px 0;"><?php echo ucfirst(str_replace('_', ' ', $record['record_type'])); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Diagnosis:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($record['diagnosis'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Treatment:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($record['treatment'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Medication:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($record['medication'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Dosage:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($record['dosage'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Vet Name:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($record['vet_name'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Cost:</td>
                    <td style="padding: 8px 0;"><?php echo $record['cost'] ? '₹' . number_format($record['cost'], 2) : '-'; ?></td>
                </tr>
                <?php if ($record['notes']): ?>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Notes:</td>
                    <td style="padding: 8px 0;"><?php echo nl2br(htmlspecialchars($record['notes'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

