<?php
/**
 * Application Configuration
 * Contains all system-wide settings and constants
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Base URL (for offline use, use relative paths)
define('BASE_URL', '/CowFarmManagement/');
define('BASE_PATH', dirname(__DIR__));

// Upload directories
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
define('COW_PHOTOS_DIR', UPLOAD_DIR . 'cow_photos/');
define('REPORTS_DIR', BASE_PATH . '/reports/');
define('BACKUP_DIR', BASE_PATH . '/backups/');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_VET', 'vet');
define('ROLE_MANAGER', 'manager');
define('ROLE_STAFF', 'staff');

// Developer secret for hidden maintenance tools (change this value!)
define('DEVELOPER_SECRET', 'CHANGE_ME_TO_A_LONG_RANDOM_STRING');

// Create necessary directories if they don't exist
$directories = [UPLOAD_DIR, COW_PHOTOS_DIR, REPORTS_DIR, BACKUP_DIR];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Include database connection
require_once BASE_PATH . '/config/database.php';

