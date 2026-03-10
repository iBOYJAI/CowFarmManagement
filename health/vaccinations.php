<?php
/**
 * Vaccinations List Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$search = $_GET['search'] ?? '';
$cowId = $_GET['cow_id'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(c.tag_number LIKE ? OR c.name LIKE ? OR v.vaccine_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($cowId)) {
    $where[] = "v.cow_id = ?";
    $params[] = $cowId;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT v.*, c.tag_number, c.name as cow_name 
          FROM vaccinations v 
          JOIN cows c ON v.cow_id = c.id 
          WHERE $whereClause 
          ORDER BY v.vaccination_date DESC";

$result = $db->fetchPaginated($query, $params, $page);
$vaccinations = $result['data'];
$totalPages = $result['total_pages'];

$cows = $db->fetchAll("SELECT id, tag_number, name FROM cows WHERE status = 'active' ORDER BY tag_number");

$pageTitle = 'Vaccinations';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Vaccination Records</h2>
            <a href="<?php echo BASE_URL; ?>health/add_vaccination.php" class="btn btn-primary">Add Vaccination</a>
        </div>
        <div class="card-body">
            <div class="search-filter-bar">
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Search..." 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           onkeyup="if(event.key==='Enter') this.form.submit()">
                </div>
                <select name="cow_id" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">All Cows</option>
                    <?php foreach ($cows as $cow): ?>
                        <option value="<?php echo $cow['id']; ?>" <?php echo $cowId == $cow['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cow['tag_number'] . ($cow['name'] ? ' - ' . $cow['name'] : '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <form method="GET" style="display: contents;">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Cow</th>
                            <th>Vaccine</th>
                            <th>Next Due</th>
                            <th>Administered By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vaccinations)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">No vaccination records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vaccinations as $vac): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($vac['vaccination_date']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($vac['tag_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($vac['vaccine_name']); ?></td>
                                    <td><?php echo $vac['next_due_date'] ? Helper::formatDate($vac['next_due_date']) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($vac['administered_by'] ?? '-'); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>health/edit_vaccination.php?id=<?php echo $vac['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php echo Helper::generatePagination($page, $totalPages, BASE_URL . 'health/vaccinations.php?search=' . urlencode($search) . '&cow_id=' . urlencode($cowId)); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

