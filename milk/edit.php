<?php
/**
 * Edit Milk Production Record
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
    header('Location: ' . BASE_URL . 'milk/index.php');
    exit;
}

$record = $db->fetchOne("SELECT * FROM milk_production WHERE id = ?", [$id]);
if (!$record) {
    header('Location: ' . BASE_URL . 'milk/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productionDate = $_POST['production_date'] ?? '';
    $session = $_POST['session'] ?? 'both';
    $morningYield = $_POST['morning_yield'] ?? null;
    $eveningYield = $_POST['evening_yield'] ?? null;
    $qualityGrade = Helper::sanitize($_POST['quality_grade'] ?? '');
    $temperature = $_POST['temperature'] ?? null;
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    $totalYield = 0;
    if ($session === 'morning' || $session === 'both') {
        $totalYield += (float)$morningYield;
    }
    if ($session === 'evening' || $session === 'both') {
        $totalYield += (float)$eveningYield;
    }
    
    if (empty($productionDate) || $totalYield <= 0) {
        $error = 'Date and at least one yield value are required';
    } else {
        $sql = "UPDATE milk_production SET production_date = ?, session = ?, morning_yield = ?, evening_yield = ?, total_yield = ?, quality_grade = ?, temperature = ?, notes = ? WHERE id = ?";
        
        $result = $db->execute($sql, [
            $productionDate, $session, $morningYield ?: null, $eveningYield ?: null, $totalYield,
            $qualityGrade ?: null, $temperature ?: null, $notes ?: null, $id
        ]);
        
        if ($result) {
            header('Location: ' . BASE_URL . 'milk/index.php?success=1');
            exit;
        } else {
            $error = 'Failed to update record';
        }
    }
    
    $record = $db->fetchOne("SELECT * FROM milk_production WHERE id = ?", [$id]);
}

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' AND gender = 'female' ORDER BY tag_number");

$pageTitle = 'Edit Milk Production Record';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Milk Production Record</h2>
            <a href="<?php echo BASE_URL; ?>milk/index.php" class="btn btn-outline">Back</a>
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
                        <label class="form-label required" for="production_date">Production Date</label>
                        <input type="date" class="form-control" id="production_date" name="production_date" 
                               value="<?php echo $record['production_date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="session">Session</label>
                        <select class="form-control" id="session" name="session" required>
                            <option value="both" <?php echo $record['session'] === 'both' ? 'selected' : ''; ?>>Both</option>
                            <option value="morning" <?php echo $record['session'] === 'morning' ? 'selected' : ''; ?>>Morning Only</option>
                            <option value="evening" <?php echo $record['session'] === 'evening' ? 'selected' : ''; ?>>Evening Only</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="morning_yield">Morning Yield (L)</label>
                        <input type="number" class="form-control" id="morning_yield" name="morning_yield" 
                               step="0.01" min="0" value="<?php echo $record['morning_yield'] ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="evening_yield">Evening Yield (L)</label>
                        <input type="number" class="form-control" id="evening_yield" name="evening_yield" 
                               step="0.01" min="0" value="<?php echo $record['evening_yield'] ?: ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="quality_grade">Quality Grade</label>
                        <input type="text" class="form-control" id="quality_grade" name="quality_grade" 
                               value="<?php echo htmlspecialchars($record['quality_grade'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="temperature">Temperature (°C)</label>
                        <input type="number" class="form-control" id="temperature" name="temperature" 
                               step="0.01" value="<?php echo $record['temperature'] ?: ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($record['notes'] ?? ''); ?></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="<?php echo BASE_URL; ?>milk/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

