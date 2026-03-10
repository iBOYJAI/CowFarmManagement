<?php
/**
 * Cows List Page
 * Displays all cows with search and filter options
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$conn = $db->getConnection();

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$breed = $_GET['breed'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Build query
$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(tag_number LIKE ? OR name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    $where[] = "status = ?";
    $params[] = $status;
}

if (!empty($breed)) {
    $where[] = "breed = ?";
    $params[] = $breed;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT * FROM cows WHERE $whereClause ORDER BY created_at DESC";

// Get paginated results
$result = $db->fetchPaginated($query, $params, $page);
$cows = $result['data'];
$totalPages = $result['total_pages'];

// Get unique breeds for filter
$breeds = $db->fetchAll("SELECT DISTINCT breed FROM cows WHERE breed IS NOT NULL AND breed != '' ORDER BY breed");

$pageTitle = 'Cow Profiles';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Cow Profiles</h2>
            <a href="<?php echo BASE_URL; ?>cows/add.php" class="btn btn-primary">
                <svg class="btn-icon" viewBox="0 0 24 24">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Add New Cow
            </a>
        </div>
        <div class="card-body">
            <!-- Search and Filters -->
            <form method="GET" class="search-filter-bar">
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" name="search" class="form-control" placeholder="Search by tag number or name..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <select name="status" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="sold" <?php echo $status === 'sold' ? 'selected' : ''; ?>>Sold</option>
                    <option value="deceased" <?php echo $status === 'deceased' ? 'selected' : ''; ?>>Deceased</option>
                </select>
                <select name="breed" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">All Breeds</option>
                    <?php foreach ($breeds as $b): ?>
                        <option value="<?php echo htmlspecialchars($b['breed']); ?>" <?php echo $breed === $b['breed'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($b['breed']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline" style="padding: 0 15px;">Filter</button>
            </form>

            <!-- Cows Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th data-sort="text">Tag Number</th>
                            <th data-sort="text">Name</th>
                            <th data-sort="text">Breed</th>
                            <th data-sort="date">Date of Birth</th>
                            <th data-sort="number">Weight (kg)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cows)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                    No cows found. <a href="<?php echo BASE_URL; ?>cows/add.php">Add your first cow</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cows as $cow): ?>
                                <tr>
                                    <td>
                                        <?php if ($cow['photo']): ?>
                                            <img src="<?php echo BASE_URL . $cow['photo']; ?>" 
                                                 alt="<?php echo htmlspecialchars($cow['tag_number']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #e9ecef; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                🐄
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($cow['tag_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($cow['name'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($cow['breed'] ?? '-'); ?></td>
                                    <td><?php echo Helper::formatDate($cow['date_of_birth']); ?></td>
                                    <td><?php echo $cow['weight'] ? number_format($cow['weight'], 2) : '-'; ?></td>
                                    <td><?php echo Helper::getStatusBadge($cow['status']); ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="<?php echo BASE_URL; ?>cows/view.php?id=<?php echo $cow['id']; ?>" 
                                               class="btn btn-sm btn-outline" title="View">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>cows/edit.php?id=<?php echo $cow['id']; ?>" 
                                               class="btn btn-sm btn-outline" title="Edit">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                </svg>
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>cows/delete.php?id=<?php echo $cow['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               data-confirm="Are you sure you want to delete this cow?"
                                               title="Delete">
                                                <svg class="btn-icon" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <?php echo Helper::generatePagination($page, $totalPages, BASE_URL . 'cows/index.php?search=' . urlencode($search) . '&status=' . urlencode($status) . '&breed=' . urlencode($breed)); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

