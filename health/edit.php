<?php
/**
 * Edit Health Record
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
    header('Location: ' . BASE_URL . 'health/index.php');
    exit;
}

$record = $db->fetchOne("SELECT * FROM health_records WHERE id = ?", [$id]);
if (!$record) {
    header('Location: ' . BASE_URL . 'health/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recordDate = $_POST['record_date'] ?? '';
    $recordType = $_POST['record_type'] ?? '';
    $diagnosis = Helper::sanitize($_POST['diagnosis'] ?? '');
    $treatment = Helper::sanitize($_POST['treatment'] ?? '');
    $medication = Helper::sanitize($_POST['medication'] ?? '');
    $dosage = Helper::sanitize($_POST['dosage'] ?? '');
    $vetName = Helper::sanitize($_POST['vet_name'] ?? '');
    $cost = $_POST['cost'] ?? null;
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    if (empty($recordDate) || empty($recordType)) {
        $error = 'Date and record type are required';
    } else {
        $sql = "UPDATE health_records SET record_date = ?, record_type = ?, diagnosis = ?, treatment = ?, medication = ?, dosage = ?, vet_name = ?, cost = ?, notes = ? WHERE id = ?";
        
        $result = $db->execute($sql, [
            $recordDate, $recordType, $diagnosis ?: null, $treatment ?: null,
            $medication ?: null, $dosage ?: null, $vetName ?: null, $cost ?: null, $notes ?: null, $id
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'health/view.php?id=' . $id . '&success=1');
            exit;
        } else {
            $error = 'Failed to update health record';
        }
    }
    
    $record = $db->fetchOne("SELECT * FROM health_records WHERE id = ?", [$id]);
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Edit Health Record';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Health Record</h2>
            <a href="<?php echo BASE_URL; ?>health/view.php?id=<?php echo $id; ?>" class="btn btn-outline">Back</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Cow</label>
                    <input type="text" class="form-control" value="<?php 
                        $cow = $db->fetchOne("SELECT tag_number, name FROM cows WHERE id = ?", [$record['cow_id']]);
                        echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : ''));
                    ?>" disabled>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="record_date">Record Date</label>
                        <input type="date" class="form-control" id="record_date" name="record_date" 
                               value="<?php echo $record['record_date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="record_type">Record Type</label>
                        <select class="form-control" id="record_type" name="record_type" required>
                            <option value="">Select Type</option>
                            <option value="checkup" <?php echo $record['record_type'] === 'checkup' ? 'selected' : ''; ?>>Checkup</option>
                            <option value="treatment" <?php echo $record['record_type'] === 'treatment' ? 'selected' : ''; ?>>Treatment</option>
                            <option value="surgery" <?php echo $record['record_type'] === 'surgery' ? 'selected' : ''; ?>>Surgery</option>
                            <option value="injury" <?php echo $record['record_type'] === 'injury' ? 'selected' : ''; ?>>Injury</option>
                            <option value="other" <?php echo $record['record_type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="diagnosis">Diagnosis</label>
                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"><?php echo htmlspecialchars($record['diagnosis'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="treatment">Treatment</label>
                    <textarea class="form-control" id="treatment" name="treatment" rows="3"><?php echo htmlspecialchars($record['treatment'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="medication">Medication</label>
                        <input type="text" class="form-control" id="medication" name="medication" 
                               value="<?php echo htmlspecialchars($record['medication'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="dosage">Dosage</label>
                        <input type="text" class="form-control" id="dosage" name="dosage" 
                               value="<?php echo htmlspecialchars($record['dosage'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="vet_name">Vet Name</label>
                        <input type="text" class="form-control" id="vet_name" name="vet_name" 
                               value="<?php echo htmlspecialchars($record['vet_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cost">Cost</label>
                        <input type="number" class="form-control" id="cost" name="cost" step="0.01" min="0" 
                               value="<?php echo $record['cost'] ?: ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($record['notes'] ?? ''); ?></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="<?php echo BASE_URL; ?>health/view.php?id=<?php echo $id; ?>" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

