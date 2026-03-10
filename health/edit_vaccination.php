<?php
/**
 * Edit Vaccination Record
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
    header('Location: ' . BASE_URL . 'health/vaccinations.php');
    exit;
}

$record = $db->fetchOne("SELECT * FROM vaccinations WHERE id = ?", [$id]);
if (!$record) {
    header('Location: ' . BASE_URL . 'health/vaccinations.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vaccineName = Helper::sanitize($_POST['vaccine_name'] ?? '');
    $vaccinationDate = $_POST['vaccination_date'] ?? '';
    $nextDueDate = $_POST['next_due_date'] ?? null;
    $batchNumber = Helper::sanitize($_POST['batch_number'] ?? '');
    $administeredBy = Helper::sanitize($_POST['administered_by'] ?? '');
    $cost = $_POST['cost'] ?? null;
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    if (empty($vaccineName) || empty($vaccinationDate)) {
        $error = 'Vaccine name and date are required';
    } else {
        $sql = "UPDATE vaccinations SET vaccine_name = ?, vaccination_date = ?, next_due_date = ?, batch_number = ?, administered_by = ?, cost = ?, notes = ? WHERE id = ?";
        
        $result = $db->execute($sql, [
            $vaccineName, $vaccinationDate, $nextDueDate ?: null, $batchNumber ?: null,
            $administeredBy ?: null, $cost ?: null, $notes ?: null, $id
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'health/vaccinations.php?success=1');
            exit;
        } else {
            $error = 'Failed to update vaccination record';
        }
    }
    
    $record = $db->fetchOne("SELECT * FROM vaccinations WHERE id = ?", [$id]);
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Edit Vaccination';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Vaccination Record</h2>
            <a href="<?php echo BASE_URL; ?>health/vaccinations.php" class="btn btn-outline">Back</a>
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
                        <label class="form-label required" for="vaccine_name">Vaccine Name</label>
                        <input type="text" class="form-control" id="vaccine_name" name="vaccine_name" 
                               value="<?php echo htmlspecialchars($record['vaccine_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="vaccination_date">Vaccination Date</label>
                        <input type="date" class="form-control" id="vaccination_date" name="vaccination_date" 
                               value="<?php echo $record['vaccination_date']; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="next_due_date">Next Due Date</label>
                    <input type="date" class="form-control" id="next_due_date" name="next_due_date" 
                           value="<?php echo $record['next_due_date'] ?: ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="batch_number">Batch Number</label>
                        <input type="text" class="form-control" id="batch_number" name="batch_number" 
                               value="<?php echo htmlspecialchars($record['batch_number'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="administered_by">Administered By</label>
                        <input type="text" class="form-control" id="administered_by" name="administered_by" 
                               value="<?php echo htmlspecialchars($record['administered_by'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="cost">Cost</label>
                    <input type="number" class="form-control" id="cost" name="cost" step="0.01" min="0" 
                           value="<?php echo $record['cost'] ?: ''; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($record['notes'] ?? ''); ?></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Vaccination</button>
                    <a href="<?php echo BASE_URL; ?>health/vaccinations.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

