<?php
/**
 * Add New Cow Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/FileUpload.php';
require_once __DIR__ . '/../classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tagNumber = Helper::sanitize($_POST['tag_number'] ?? '');
    $name = Helper::sanitize($_POST['name'] ?? '');
    $breed = Helper::sanitize($_POST['breed'] ?? '');
    $dateOfBirth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $weight = $_POST['weight'] ?? null;
    $color = Helper::sanitize($_POST['color'] ?? '');
    $sireTag = Helper::sanitize($_POST['sire_tag'] ?? '');
    $damTag = Helper::sanitize($_POST['dam_tag'] ?? '');
    $purchaseDate = $_POST['purchase_date'] ?? null;
    $purchasePrice = $_POST['purchase_price'] ?? null;
    $status = $_POST['status'] ?? 'active';
    $notes = Helper::sanitize($_POST['notes'] ?? '');
    
    // Validate required fields
    if (empty($tagNumber) || empty($gender)) {
        $error = 'Tag number and gender are required';
    } else {
        // Check if tag number already exists
        $existing = $db->fetchOne("SELECT id FROM cows WHERE tag_number = ?", [$tagNumber]);
        if ($existing) {
            $error = 'Tag number already exists';
        } else {
            // Handle photo upload
            $photoPath = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = FileUpload::uploadCowPhoto($_FILES['photo'], $tagNumber);
                if ($uploadResult['success']) {
                    $photoPath = $uploadResult['filepath'];
                } else {
                    $error = $uploadResult['message'];
                }
            }
            
            if (empty($error)) {
                // Insert cow
                $sql = "INSERT INTO cows (tag_number, name, breed, date_of_birth, gender, weight, color, photo, sire_tag, dam_tag, purchase_date, purchase_price, status, notes, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $result = $db->execute($sql, [
                    $tagNumber, $name ?: null, $breed ?: null, $dateOfBirth ?: null, $gender,
                    $weight ?: null, $color ?: null, $photoPath, $sireTag ?: null, $damTag ?: null,
                    $purchaseDate ?: null, $purchasePrice ?: null, $status, $notes ?: null, $_SESSION['user_id']
                ]);
                
                if ($result) {
                    header('Location: ' . BASE_URL . 'cows/index.php?success=1');
                    exit;
                } else {
                    $error = 'Failed to add cow. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Add New Cow';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add New Cow</h2>
            <a href="<?php echo BASE_URL; ?>cows/index.php" class="btn btn-outline">Back to List</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="tag_number">Tag Number</label>
                        <input type="text" class="form-control" id="tag_number" name="tag_number" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="breed">Breed</label>
                        <input type="text" class="form-control" id="breed" name="breed" placeholder="e.g., Holstein, Jersey">
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="date_of_birth">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="weight">Weight (kg)</label>
                        <input type="number" class="form-control" id="weight" name="weight" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="color">Color</label>
                        <input type="text" class="form-control" id="color" name="color">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="sold">Sold</option>
                            <option value="deceased">Deceased</option>
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="sire_tag">Sire Tag (Father)</label>
                        <input type="text" class="form-control" id="sire_tag" name="sire_tag">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="dam_tag">Dam Tag (Mother)</label>
                        <input type="text" class="form-control" id="dam_tag" name="dam_tag">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="purchase_date">Purchase Date</label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="purchase_price">Purchase Price</label>
                        <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="photo">Photo</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    <div class="form-help">Max file size: 5MB. Allowed types: JPG, PNG, GIF, WebP</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add Cow</button>
                    <a href="<?php echo BASE_URL; ?>cows/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

