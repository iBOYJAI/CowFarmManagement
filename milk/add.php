<?php
/**
 * Add Milk Production Record Page
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
    $productionDate = $_POST['production_date'] ?? date('Y-m-d');
    $session = $_POST['session'] ?? 'both';
    $morningYield = $_POST['morning_yield'] ?? null;
    $eveningYield = $_POST['evening_yield'] ?? null;
    $qualityGrade = Helper::sanitize($_POST['quality_grade'] ?? '');
    $temperature = $_POST['temperature'] ?? null;
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    // Calculate total yield
    $totalYield = 0;
    if ($session === 'morning' || $session === 'both') {
        $totalYield += (float)$morningYield;
    }
    if ($session === 'evening' || $session === 'both') {
        $totalYield += (float)$eveningYield;
    }
    
    if (empty($cowId) || $totalYield <= 0) {
        $error = 'Cow and at least one yield value are required';
    } else {
        $sql = "INSERT INTO milk_production (cow_id, production_date, session, morning_yield, evening_yield, total_yield, quality_grade, temperature, notes, recorded_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->execute($sql, [
            $cowId, $productionDate, $session, 
            $morningYield ?: null, $eveningYield ?: null, $totalYield,
            $qualityGrade ?: null, $temperature ?: null, $notes ?: null, $_SESSION['user_id']
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'milk/index.php?success=1');
            exit;
        } else {
            $error = 'Failed to add milk production record';
        }
    }
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' AND gender = 'female' ORDER BY tag_number");

$pageTitle = 'Add Milk Production Record';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add Milk Production Record</h2>
            <a href="<?php echo BASE_URL; ?>milk/index.php" class="btn btn-outline">Back to List</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" id="milkForm">
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
                        <label class="form-label required" for="production_date">Production Date</label>
                        <input type="date" class="form-control" id="production_date" name="production_date" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required" for="session">Session</label>
                    <select class="form-control" id="session" name="session" required onchange="toggleYields()">
                        <option value="both">Both (Morning & Evening)</option>
                        <option value="morning">Morning Only</option>
                        <option value="evening">Evening Only</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group" id="morningGroup">
                        <label class="form-label" for="morning_yield">Morning Yield (Liters)</label>
                        <input type="number" class="form-control" id="morning_yield" name="morning_yield" 
                               step="0.01" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="form-group" id="eveningGroup">
                        <label class="form-label" for="evening_yield">Evening Yield (Liters)</label>
                        <input type="number" class="form-control" id="evening_yield" name="evening_yield" 
                               step="0.01" min="0" oninput="calculateTotal()">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="quality_grade">Quality Grade</label>
                        <input type="text" class="form-control" id="quality_grade" name="quality_grade" 
                               placeholder="e.g., A, B, Premium">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="temperature">Temperature (°C)</label>
                        <input type="number" class="form-control" id="temperature" name="temperature" 
                               step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Record</button>
                    <a href="<?php echo BASE_URL; ?>milk/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleYields() {
    const session = document.getElementById('session').value;
    const morningGroup = document.getElementById('morningGroup');
    const eveningGroup = document.getElementById('eveningGroup');
    
    if (session === 'morning') {
        eveningGroup.style.display = 'none';
        morningGroup.querySelector('input').required = true;
        eveningGroup.querySelector('input').required = false;
    } else if (session === 'evening') {
        morningGroup.style.display = 'none';
        eveningGroup.querySelector('input').required = true;
        morningGroup.querySelector('input').required = false;
    } else {
        morningGroup.style.display = 'block';
        eveningGroup.style.display = 'block';
        morningGroup.querySelector('input').required = false;
        eveningGroup.querySelector('input').required = false;
    }
}

function calculateTotal() {
    const morning = parseFloat(document.getElementById('morning_yield').value) || 0;
    const evening = parseFloat(document.getElementById('evening_yield').value) || 0;
    // Total is calculated server-side, but we can show a preview
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

