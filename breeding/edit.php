<?php
/**
 * Edit Breeding Record
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
    header('Location: ' . BASE_URL . 'breeding/index.php');
    exit;
}

$record = $db->fetchOne("SELECT * FROM breeding_records WHERE id = ?", [$id]);
if (!$record) {
    header('Location: ' . BASE_URL . 'breeding/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $breedingType = $_POST['breeding_type'] ?? '';
    $breedingDate = $_POST['breeding_date'] ?? '';
    $bullTag = Helper::sanitize($_POST['bull_tag'] ?? '');
    $aiTechnician = Helper::sanitize($_POST['ai_technician'] ?? '');
    $expectedCalving = $_POST['expected_calving_date'] ?? null;
    $actualCalving = $_POST['actual_calving_date'] ?? null;
    $pregnancyStatus = $_POST['pregnancy_status'] ?? 'pregnant';
    $calvingNotes = Helper::sanitize($_POST['calving_notes'] ?? '');
    
    if (empty($breedingType) || empty($breedingDate)) {
        $error = 'Breeding type and date are required';
    } else {
        $sql = "UPDATE breeding_records SET breeding_type = ?, breeding_date = ?, bull_tag = ?, ai_technician = ?, expected_calving_date = ?, actual_calving_date = ?, pregnancy_status = ?, calving_notes = ? WHERE id = ?";
        
        $result = $db->execute($sql, [
            $breedingType, $breedingDate, $bullTag ?: null, $aiTechnician ?: null,
            $expectedCalving ?: null, $actualCalving ?: null, $pregnancyStatus, $calvingNotes ?: null, $id
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'breeding/view.php?id=' . $id . '&success=1');
            exit;
        } else {
            $error = 'Failed to update breeding record';
        }
    }
    
    $record = $db->fetchOne("SELECT * FROM breeding_records WHERE id = ?", [$id]);
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' AND gender = 'female' ORDER BY tag_number");

$pageTitle = 'Edit Breeding Record';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Breeding Record</h2>
            <a href="<?php echo BASE_URL; ?>breeding/view.php?id=<?php echo $id; ?>" class="btn btn-outline">Back</a>
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
                        <label class="form-label required" for="breeding_date">Breeding Date</label>
                        <input type="date" class="form-control" id="breeding_date" name="breeding_date" 
                               value="<?php echo $record['breeding_date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="breeding_type">Breeding Type</label>
                        <select class="form-control" id="breeding_type" name="breeding_type" required>
                            <option value="AI" <?php echo $record['breeding_type'] === 'AI' ? 'selected' : ''; ?>>AI</option>
                            <option value="natural" <?php echo $record['breeding_type'] === 'natural' ? 'selected' : ''; ?>>Natural</option>
                            <option value="embryo" <?php echo $record['breeding_type'] === 'embryo' ? 'selected' : ''; ?>>Embryo</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="bull_tag">Bull Tag</label>
                        <input type="text" class="form-control" id="bull_tag" name="bull_tag" 
                               value="<?php echo htmlspecialchars($record['bull_tag'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="ai_technician">AI Technician</label>
                        <input type="text" class="form-control" id="ai_technician" name="ai_technician" 
                               value="<?php echo htmlspecialchars($record['ai_technician'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="expected_calving_date">Expected Calving Date</label>
                        <input type="date" class="form-control" id="expected_calving_date" name="expected_calving_date" 
                               value="<?php echo $record['expected_calving_date'] ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="actual_calving_date">Actual Calving Date</label>
                        <input type="date" class="form-control" id="actual_calving_date" name="actual_calving_date" 
                               value="<?php echo $record['actual_calving_date'] ?: ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="pregnancy_status">Pregnancy Status</label>
                    <select class="form-control" id="pregnancy_status" name="pregnancy_status">
                        <option value="pregnant" <?php echo $record['pregnancy_status'] === 'pregnant' ? 'selected' : ''; ?>>Pregnant</option>
                        <option value="not_pregnant" <?php echo $record['pregnancy_status'] === 'not_pregnant' ? 'selected' : ''; ?>>Not Pregnant</option>
                        <option value="aborted" <?php echo $record['pregnancy_status'] === 'aborted' ? 'selected' : ''; ?>>Aborted</option>
                        <option value="delivered" <?php echo $record['pregnancy_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="calving_notes">Calving Notes</label>
                    <textarea class="form-control" id="calving_notes" name="calving_notes" rows="3"><?php echo htmlspecialchars($record['calving_notes'] ?? ''); ?></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="<?php echo BASE_URL; ?>breeding/view.php?id=<?php echo $id; ?>" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

