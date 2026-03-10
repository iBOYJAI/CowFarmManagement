<?php
/**
 * Edit Appointment
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
    header('Location: ' . BASE_URL . 'appointments/index.php');
    exit;
}

$appointment = $db->fetchOne("SELECT * FROM appointments WHERE id = ?", [$id]);
if (!$appointment) {
    header('Location: ' . BASE_URL . 'appointments/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentDate = $_POST['appointment_date'] ?? '';
    $appointmentTime = $_POST['appointment_time'] ?? '';
    $cowId = $_POST['cow_id'] ? (int)$_POST['cow_id'] : null;
    $vetName = Helper::sanitize($_POST['vet_name'] ?? '');
    $purpose = Helper::sanitize($_POST['purpose'] ?? '');
    $status = $_POST['status'] ?? 'scheduled';
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    if (empty($appointmentDate) || empty($vetName) || empty($purpose)) {
        $error = 'Date, vet name, and purpose are required';
    } else {
        $sql = "UPDATE appointments SET appointment_date = ?, appointment_time = ?, cow_id = ?, vet_name = ?, purpose = ?, status = ?, notes = ? WHERE id = ?";
        
        $result = $db->execute($sql, [
            $appointmentDate, $appointmentTime ?: null, $cowId, $vetName, $purpose, $status, $notes ?: null, $id
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'appointments/view.php?id=' . $id . '&success=1');
            exit;
        } else {
            $error = 'Failed to update appointment';
        }
    }
    
    $appointment = $db->fetchOne("SELECT * FROM appointments WHERE id = ?", [$id]);
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Edit Appointment';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Appointment</h2>
            <a href="<?php echo BASE_URL; ?>appointments/view.php?id=<?php echo $id; ?>" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="appointment_date">Appointment Date</label>
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                               value="<?php echo $appointment['appointment_date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="appointment_time">Time</label>
                        <input type="time" class="form-control" id="appointment_time" name="appointment_time" 
                               value="<?php echo $appointment['appointment_time'] ?: ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="cow_id">Cow (Optional)</label>
                        <select class="form-control" id="cow_id" name="cow_id">
                            <option value="">Select Cow (Optional)</option>
                            <?php foreach ($cows as $cow): ?>
                                <option value="<?php echo $cow['id']; ?>" <?php echo $appointment['cow_id'] == $cow['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="vet_name">Vet Name</label>
                        <input type="text" class="form-control" id="vet_name" name="vet_name" 
                               value="<?php echo htmlspecialchars($appointment['vet_name']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required" for="purpose">Purpose</label>
                    <input type="text" class="form-control" id="purpose" name="purpose" 
                           value="<?php echo htmlspecialchars($appointment['purpose']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="scheduled" <?php echo $appointment['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                        <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="rescheduled" <?php echo $appointment['status'] === 'rescheduled' ? 'selected' : ''; ?>>Rescheduled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($appointment['notes'] ?? ''); ?></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Appointment</button>
                    <a href="<?php echo BASE_URL; ?>appointments/view.php?id=<?php echo $id; ?>" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

