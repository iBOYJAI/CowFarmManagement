<?php
/**
 * Add Appointment Page
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
        $sql = "INSERT INTO appointments (appointment_date, appointment_time, cow_id, vet_name, purpose, status, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $appointmentDate, $appointmentTime ?: null, $cowId, $vetName, $purpose, $status, $notes ?: null, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'appointments/index.php?success=1');
            exit;
        } else {
            $error = 'Failed to schedule appointment';
        }
    }
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Schedule Appointment';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Schedule Appointment</h2>
            <a href="<?php echo BASE_URL; ?>appointments/index.php" class="btn btn-outline">Back</a>
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
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="appointment_time">Time</label>
                        <input type="time" class="form-control" id="appointment_time" name="appointment_time">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="cow_id">Cow (Optional)</label>
                        <select class="form-control" id="cow_id" name="cow_id">
                            <option value="">Select Cow (Optional)</option>
                            <?php foreach ($cows as $cow): ?>
                                <option value="<?php echo $cow['id']; ?>">
                                    <?php echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="vet_name">Vet Name</label>
                        <input type="text" class="form-control" id="vet_name" name="vet_name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required" for="purpose">Purpose</label>
                    <input type="text" class="form-control" id="purpose" name="purpose" required 
                           placeholder="e.g., Pregnancy Check, Vaccination, General Checkup">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="scheduled">Scheduled</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                    <a href="<?php echo BASE_URL; ?>appointments/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

