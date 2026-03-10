<?php
/**
 * Alerts & Due Lists Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

// Get upcoming vaccinations (next 30 days)
$upcomingVaccinations = $db->fetchAll("
    SELECT v.*, c.tag_number, c.name as cow_name,
           DATEDIFF(v.next_due_date, CURDATE()) as days_until
    FROM vaccinations v
    JOIN cows c ON v.cow_id = c.id
    WHERE v.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY v.next_due_date ASC
");

// Get upcoming calvings (next 30 days)
$upcomingCalvings = $db->fetchAll("
    SELECT br.*, c.tag_number, c.name as cow_name,
           DATEDIFF(br.expected_calving_date, CURDATE()) as days_until
    FROM breeding_records br
    JOIN cows c ON br.cow_id = c.id
    WHERE br.pregnancy_status = 'pregnant'
      AND br.expected_calving_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY br.expected_calving_date ASC
");

// Get overdue vaccinations
$overdueVaccinations = $db->fetchAll("
    SELECT v.*, c.tag_number, c.name as cow_name,
           DATEDIFF(CURDATE(), v.next_due_date) as days_overdue
    FROM vaccinations v
    JOIN cows c ON v.cow_id = c.id
    WHERE v.next_due_date < CURDATE()
    ORDER BY v.next_due_date ASC
");

$pageTitle = 'Alerts & Due Lists';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <?php if (!empty($overdueVaccinations)): ?>
        <div class="card" style="border-left: 4px solid var(--danger-color);">
            <div class="card-header">
                <h2 class="card-title" style="color: var(--danger-color);">⚠️ Overdue Vaccinations</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cow</th>
                                <th>Vaccine</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($overdueVaccinations as $vac): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($vac['tag_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($vac['vaccine_name']); ?></td>
                                    <td><?php echo Helper::formatDate($vac['next_due_date']); ?></td>
                                    <td><span class="badge badge-danger"><?php echo $vac['days_overdue']; ?> days</span></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>health/add_vaccination.php?cow_id=<?php echo $vac['cow_id']; ?>" class="btn btn-sm btn-primary">Vaccinate</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Upcoming Vaccinations (Next 30 Days)</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cow</th>
                            <th>Vaccine</th>
                            <th>Due Date</th>
                            <th>Days Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcomingVaccinations)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">No upcoming vaccinations</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcomingVaccinations as $vac): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($vac['tag_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($vac['vaccine_name']); ?></td>
                                    <td><?php echo Helper::formatDate($vac['next_due_date']); ?></td>
                                    <td>
                                        <?php if ($vac['days_until'] <= 7): ?>
                                            <span class="badge badge-warning"><?php echo $vac['days_until']; ?> days</span>
                                        <?php else: ?>
                                            <span class="badge badge-info"><?php echo $vac['days_until']; ?> days</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>health/add_vaccination.php?cow_id=<?php echo $vac['cow_id']; ?>" class="btn btn-sm btn-outline">Schedule</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Upcoming Calvings (Next 30 Days)</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cow</th>
                            <th>Breeding Date</th>
                            <th>Expected Calving</th>
                            <th>Days Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcomingCalvings)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">No upcoming calvings</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcomingCalvings as $calving): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($calving['tag_number']); ?></strong></td>
                                    <td><?php echo Helper::formatDate($calving['breeding_date']); ?></td>
                                    <td><?php echo Helper::formatDate($calving['expected_calving_date']); ?></td>
                                    <td>
                                        <?php if ($calving['days_until'] <= 7): ?>
                                            <span class="badge badge-warning"><?php echo $calving['days_until']; ?> days</span>
                                        <?php else: ?>
                                            <span class="badge badge-info"><?php echo $calving['days_until']; ?> days</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>cows/view.php?id=<?php echo $calving['cow_id']; ?>" class="btn btn-sm btn-outline">View Cow</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

