<?php
/**
 * Add Vaccination Record Page
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
    $vaccineName = Helper::sanitize($_POST['vaccine_name'] ?? '');
    $vaccinationDate = $_POST['vaccination_date'] ?? date('Y-m-d');
    $nextDueDate = $_POST['next_due_date'] ?? null;
    $batchNumber = Helper::sanitize($_POST['batch_number'] ?? '');
    $administeredBy = Helper::sanitize($_POST['administered_by'] ?? '');
    $cost = $_POST['cost'] ?? null;
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    if (empty($cowId) || empty($vaccineName)) {
        $error = 'Cow and vaccine name are required';
    } else {
        $sql = "INSERT INTO vaccinations (cow_id, vaccine_name, vaccination_date, next_due_date, batch_number, administered_by, cost, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $cowId, $vaccineName, $vaccinationDate, $nextDueDate ?: null, $batchNumber ?: null,
            $administeredBy ?: null, $cost ?: null, $notes ?: null, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'health/vaccinations.php?success=1');
            exit;
        } else {
            $error = 'Failed to add vaccination record';
        }
    }
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Add Vaccination';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add Vaccination Record</h2>
            <a href="<?php echo BASE_URL; ?>health/vaccinations.php" class="btn btn-outline">Back</a>
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
                        <label class="form-label required" for="vaccine_name">Vaccine Name</label>
                        <input type="text" class="form-control" id="vaccine_name" name="vaccine_name" required 
                               placeholder="e.g., FMD Vaccine, Brucellosis">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="vaccination_date">Vaccination Date</label>
                        <input type="date" class="form-control" id="vaccination_date" name="vaccination_date" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="next_due_date">Next Due Date</label>
                        <input type="date" class="form-control" id="next_due_date" name="next_due_date">
                        <div class="form-help">Leave empty if one-time vaccination</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="batch_number">Batch Number</label>
                        <input type="text" class="form-control" id="batch_number" name="batch_number">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="administered_by">Administered By</label>
                        <input type="text" class="form-control" id="administered_by" name="administered_by" 
                               placeholder="Vet name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="cost">Cost</label>
                    <input type="number" class="form-control" id="cost" name="cost" step="0.01" min="0">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Vaccination</button>
                    <a href="<?php echo BASE_URL; ?>health/vaccinations.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

