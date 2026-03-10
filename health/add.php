<?php
/**
 * Add Health Record Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$error = '';
$cowId = $_GET['cow_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cowId = (int)($_POST['cow_id'] ?? 0);
    $recordDate = $_POST['record_date'] ?? date('Y-m-d');
    $recordType = $_POST['record_type'] ?? '';
    $diagnosis = Helper::sanitize($_POST['diagnosis'] ?? '');
    $treatment = Helper::sanitize($_POST['treatment'] ?? '');
    $medication = Helper::sanitize($_POST['medication'] ?? '');
    $dosage = Helper::sanitize($_POST['dosage'] ?? '');
    $vetName = Helper::sanitize($_POST['vet_name'] ?? '');
    $cost = $_POST['cost'] ?? null;
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    if (empty($cowId) || empty($recordType)) {
        $error = 'Cow and record type are required';
    } else {
        $sql = "INSERT INTO health_records (cow_id, record_date, record_type, diagnosis, treatment, medication, dosage, vet_name, cost, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $cowId, $recordDate, $recordType, $diagnosis ?: null, $treatment ?: null,
            $medication ?: null, $dosage ?: null, $vetName ?: null, $cost ?: null, $notes ?: null, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'health/index.php?success=1');
            exit;
        } else {
            $error = 'Failed to add health record';
        }
    }
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Add Health Record';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add Health Record</h2>
            <a href="<?php echo BASE_URL; ?>health/index.php" class="btn btn-outline">Back to List</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="cow_id">Cow</label>
                        <select class="form-control" id="cow_id" name="cow_id" required>
                            <option value="">Select Cow</option>
                            <?php foreach ($cows as $cow): ?>
                                <option value="<?php echo $cow['id']; ?>" <?php echo $cowId == $cow['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="record_date">Record Date</label>
                        <input type="date" class="form-control" id="record_date" name="record_date" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="record_type">Record Type</label>
                        <select class="form-control" id="record_type" name="record_type" required>
                            <option value="">Select Type</option>
                            <option value="checkup">Checkup</option>
                            <option value="treatment">Treatment</option>
                            <option value="surgery">Surgery</option>
                            <option value="injury">Injury</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="vet_name">Vet Name</label>
                        <input type="text" class="form-control" id="vet_name" name="vet_name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="diagnosis">Diagnosis</label>
                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="treatment">Treatment</label>
                    <textarea class="form-control" id="treatment" name="treatment" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="medication">Medication</label>
                        <input type="text" class="form-control" id="medication" name="medication">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="dosage">Dosage</label>
                        <input type="text" class="form-control" id="dosage" name="dosage">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="cost">Cost</label>
                        <input type="number" class="form-control" id="cost" name="cost" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Record</button>
                    <a href="<?php echo BASE_URL; ?>health/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

