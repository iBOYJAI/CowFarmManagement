<?php
/**
 * Add Breeding Record Page
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
    $cowId = (int)($_POST['cow_id'] ?? 0);
    $breedingType = $_POST['breeding_type'] ?? '';
    $breedingDate = $_POST['breeding_date'] ?? date('Y-m-d');
    $bullTag = Helper::sanitize($_POST['bull_tag'] ?? '');
    $aiTechnician = Helper::sanitize($_POST['ai_technician'] ?? '');
    $pregnancyStatus = $_POST['pregnancy_status'] ?? 'pregnant';
    
    // Calculate expected calving date (approximately 280 days)
    $expectedCalving = null;
    if ($pregnancyStatus === 'pregnant' && $breedingDate) {
        $expectedCalving = date('Y-m-d', strtotime($breedingDate . ' +280 days'));
    }
    
    if (empty($cowId) || empty($breedingType)) {
        $error = 'Cow and breeding type are required';
    } else {
        $sql = "INSERT INTO breeding_records (cow_id, breeding_type, breeding_date, bull_tag, ai_technician, expected_calving_date, pregnancy_status, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $cowId, $breedingType, $breedingDate, $bullTag ?: null, $aiTechnician ?: null,
            $expectedCalving, $pregnancyStatus, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'breeding/index.php?success=1');
            exit;
        } else {
            $error = 'Failed to add breeding record';
        }
    }
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' AND gender = 'female' ORDER BY tag_number");

$pageTitle = 'Add Breeding Record';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add Breeding Record</h2>
            <a href="<?php echo BASE_URL; ?>breeding/index.php" class="btn btn-outline">Back</a>
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
                                <option value="<?php echo $cow['id']; ?>">
                                    <?php echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="breeding_date">Breeding Date</label>
                        <input type="date" class="form-control" id="breeding_date" name="breeding_date" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="breeding_type">Breeding Type</label>
                        <select class="form-control" id="breeding_type" name="breeding_type" required>
                            <option value="">Select Type</option>
                            <option value="AI">Artificial Insemination (AI)</option>
                            <option value="natural">Natural</option>
                            <option value="embryo">Embryo Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="pregnancy_status">Pregnancy Status</label>
                        <select class="form-control" id="pregnancy_status" name="pregnancy_status">
                            <option value="pregnant">Pregnant</option>
                            <option value="not_pregnant">Not Pregnant</option>
                            <option value="aborted">Aborted</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="bull_tag">Bull Tag</label>
                        <input type="text" class="form-control" id="bull_tag" name="bull_tag">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="ai_technician">AI Technician</label>
                        <input type="text" class="form-control" id="ai_technician" name="ai_technician">
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Record</button>
                    <a href="<?php echo BASE_URL; ?>breeding/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

