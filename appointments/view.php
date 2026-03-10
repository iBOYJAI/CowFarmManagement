<?php
/**
 * View Appointment
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
    header('Location: ' . BASE_URL . 'appointments/index.php');
    exit;
}

$appointment = $db->fetchOne("SELECT a.*, c.tag_number, c.name as cow_name FROM appointments a LEFT JOIN cows c ON a.cow_id = c.id WHERE a.id = ?", [$id]);

if (!$appointment) {
    header('Location: ' . BASE_URL . 'appointments/index.php');
    exit;
}

$pageTitle = 'Appointment Details';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Appointment Details</h2>
            <div>
                <a href="<?php echo BASE_URL; ?>appointments/edit.php?id=<?php echo $appointment['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="<?php echo BASE_URL; ?>appointments/index.php" class="btn btn-outline">Back</a>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; width: 200px;">Date:</td>
                    <td style="padding: 8px 0;"><?php echo Helper::formatDate($appointment['appointment_date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Time:</td>
                    <td style="padding: 8px 0;"><?php echo $appointment['appointment_time'] ? date('H:i', strtotime($appointment['appointment_time'])) : '-'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Cow:</td>
                    <td style="padding: 8px 0;"><?php echo $appointment['cow_name'] ? htmlspecialchars($appointment['cow_name']) : ($appointment['tag_number'] ? htmlspecialchars($appointment['tag_number']) : '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Vet Name:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($appointment['vet_name'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Purpose:</td>
                    <td style="padding: 8px 0;"><?php echo htmlspecialchars($appointment['purpose'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Status:</td>
                    <td style="padding: 8px 0;"><?php echo Helper::getStatusBadge($appointment['status']); ?></td>
                </tr>
                <?php if ($appointment['notes']): ?>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Notes:</td>
                    <td style="padding: 8px 0;"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

