<?php
/**
 * View Cow Details Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ' . BASE_URL . 'cows/index.php');
    exit;
}

// Get cow data
$cow = $db->fetchOne("SELECT c.*, u.full_name as created_by_name FROM cows c LEFT JOIN users u ON c.created_by = u.id WHERE c.id = ?", [$id]);

if (!$cow) {
    header('Location: ' . BASE_URL . 'cows/index.php');
    exit;
}

// Get related data
$healthRecords = $db->fetchAll("SELECT * FROM health_records WHERE cow_id = ? ORDER BY record_date DESC LIMIT 5", [$id]);
$vaccinations = $db->fetchAll("SELECT * FROM vaccinations WHERE cow_id = ? ORDER BY vaccination_date DESC LIMIT 5", [$id]);
$breedingRecords = $db->fetchAll("SELECT * FROM breeding_records WHERE cow_id = ? ORDER BY breeding_date DESC LIMIT 5", [$id]);
$milkProduction = $db->fetchAll("SELECT * FROM milk_production WHERE cow_id = ? ORDER BY production_date DESC LIMIT 10", [$id]);

$pageTitle = 'Cow Details: ' . $cow['tag_number'];
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Cow updated successfully!</div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Cow Details: <?php echo htmlspecialchars($cow['tag_number']); ?></h2>
            <div>
                <a href="<?php echo BASE_URL; ?>cows/edit.php?id=<?php echo $cow['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="<?php echo BASE_URL; ?>cows/index.php" class="btn btn-outline">Back to List</a>
            </div>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 30px; margin-bottom: 30px;">
                <div>
                    <?php if ($cow['photo']): ?>
                        <img src="<?php echo BASE_URL . $cow['photo']; ?>" 
                             alt="<?php echo htmlspecialchars($cow['tag_number']); ?>" 
                             style="width: 100%; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div style="width: 100%; aspect-ratio: 1; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 64px;">
                            🐄
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <h3><?php echo htmlspecialchars($cow['name'] ?: $cow['tag_number']); ?></h3>
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600; width: 150px;">Tag Number:</td>
                            <td style="padding: 8px 0;"><?php echo htmlspecialchars($cow['tag_number']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Breed:</td>
                            <td style="padding: 8px 0;"><?php echo htmlspecialchars($cow['breed'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Gender:</td>
                            <td style="padding: 8px 0;"><?php echo ucfirst($cow['gender']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Date of Birth:</td>
                            <td style="padding: 8px 0;"><?php echo Helper::formatDate($cow['date_of_birth']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Weight:</td>
                            <td style="padding: 8px 0;"><?php echo $cow['weight'] ? number_format($cow['weight'], 2) . ' kg' : '-'; ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Color:</td>
                            <td style="padding: 8px 0;"><?php echo htmlspecialchars($cow['color'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Status:</td>
                            <td style="padding: 8px 0;"><?php echo Helper::getStatusBadge($cow['status']); ?></td>
                        </tr>
                        <?php if ($cow['sire_tag']): ?>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Sire (Father):</td>
                            <td style="padding: 8px 0;"><?php echo htmlspecialchars($cow['sire_tag']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($cow['dam_tag']): ?>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Dam (Mother):</td>
                            <td style="padding: 8px 0;"><?php echo htmlspecialchars($cow['dam_tag']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($cow['purchase_date']): ?>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Purchase Date:</td>
                            <td style="padding: 8px 0;"><?php echo Helper::formatDate($cow['purchase_date']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($cow['purchase_price']): ?>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Purchase Price:</td>
                            <td style="padding: 8px 0;">$<?php echo number_format($cow['purchase_price'], 2); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($cow['notes']): ?>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Notes:</td>
                            <td style="padding: 8px 0;"><?php echo nl2br(htmlspecialchars($cow['notes'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Milk Production -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Milk Production</h3>
            <a href="<?php echo BASE_URL; ?>milk/add.php?cow_id=<?php echo $cow['id']; ?>" class="btn btn-sm btn-primary">Add Record</a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Morning (L)</th>
                        <th>Evening (L)</th>
                        <th>Total (L)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($milkProduction)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999;">No milk production records</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($milkProduction as $milk): ?>
                            <tr>
                                <td><?php echo Helper::formatDate($milk['production_date']); ?></td>
                                <td><?php echo $milk['morning_yield'] ? number_format($milk['morning_yield'], 2) : '-'; ?></td>
                                <td><?php echo $milk['evening_yield'] ? number_format($milk['evening_yield'], 2) : '-'; ?></td>
                                <td><strong><?php echo number_format($milk['total_yield'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Health Records -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Health Records</h3>
            <a href="<?php echo BASE_URL; ?>health/add.php?cow_id=<?php echo $cow['id']; ?>" class="btn btn-sm btn-primary">Add Record</a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Diagnosis</th>
                        <th>Treatment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($healthRecords)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999;">No health records</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($healthRecords as $health): ?>
                            <tr>
                                <td><?php echo Helper::formatDate($health['record_date']); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $health['record_type'])); ?></td>
                                <td><?php echo htmlspecialchars($health['diagnosis'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($health['treatment'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

