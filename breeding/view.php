<?php
/**
 * View Breeding Record
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
    header('Location: ' . BASE_URL . 'breeding/index.php');
    exit;
}

$record = $db->fetchOne("SELECT br.*, c.tag_number, c.name as cow_name FROM breeding_records br JOIN cows c ON br.cow_id = c.id WHERE br.id = ?", [$id]);

if (!$record) {
    header('Location: ' . BASE_URL . 'breeding/index.php');
    exit;
}

$pageTitle = 'Breeding Record Details';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Breeding Record Details</h2>
            <div>
                <a href="<?php echo BASE_URL; ?>breeding/edit.php?id=<?php echo $record['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="<?php echo BASE_URL; ?>breeding/index.php" class="btn btn-outline">Back</a>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; width: 200px;">Cow:</td>
                    <td style="padding: 8px 0;"><strong><?php echo htmlspecialchars($record['tag_number']); ?></strong></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Breeding Date:</td>
                    <td style="padding: 8px 0;"><?php echo Helper::formatDate($record['breeding_date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Breeding Type:</td>
                    <td style="padding: 8px 0;"><?php echo strtoupper($record['breeding_type']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Bull Tag:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($record['bull_tag'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">AI Technician:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($record['ai_technician'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Expected Calving:</td>
                    <td style="padding: 8px 0;"><?php echo $record['expected_calving_date'] ? Helper::formatDate($record['expected_calving_date']) : '-'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Actual Calving:</td>
                    <td style="padding: 8px 0;"><?php echo $record['actual_calving_date'] ? Helper::formatDate($record['actual_calving_date']) : '-'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Pregnancy Status:</td>
                    <td style="padding: 8px 0;"><?php echo Helper::getStatusBadge($record['pregnancy_status']); ?></td>
                </tr>
                <?php if ($record['calving_notes']): ?>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Calving Notes:</td>
                    <td style="padding: 8px 0;"><?php echo nl2br(htmlspecialchars($record['calving_notes'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

