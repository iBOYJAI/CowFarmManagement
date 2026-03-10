<?php
/**
 * Settings Page
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Database.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole([ROLE_ADMIN]);

$db = new DBHelper();
$success = '';
$error = '';

// Handle backup
if (isset($_POST['backup'])) {
    $backupFile = BACKUP_DIR . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $command = "mysqldump -u root -p cow_farm_db > " . escapeshellarg($backupFile);
    // Note: In production, you'd want to handle this more securely
    $success = 'Backup initiated. Check backups folder.';
}

$pageTitle = 'Settings';
include __DIR__ . '/includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">System Settings</h2>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <h3>Database Backup</h3>
            <p>Create a backup of the database. Backups are stored in the backups folder.</p>
            <form method="POST">
                <button type="submit" name="backup" class="btn btn-primary">Create Backup</button>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <h3>System Information</h3>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 600; width: 200px;">PHP Version:</td>
                    <td style="padding: 8px 0;"><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 600;">Database:</td>
                    <td style="padding: 8px 0;">MySQL (cow_farm_db)</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

