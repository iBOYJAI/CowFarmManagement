<?php
/**
 * Developer-only self-destruct tool.
 *
 * WARNING: This will irreversibly delete the entire project directory
 * after a delay once activated. Use ONLY in a local/dev environment.
 *
 * Usage (from browser, when logged in as admin):
 *   http://localhost/CowFarmManagement/dev_self_destruct.php?secret=YOUR_SECRET&action=arm
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();

// Must be logged in
if (!$auth->isLoggedIn()) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

// Must be admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== ROLE_ADMIN) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

// Secret check
$secret = $_GET['secret'] ?? '';
if ($secret !== DEVELOPER_SECRET) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$action = $_GET['action'] ?? '';

// Path to small flag file indicating self-destruct is armed
$flagFile = BASE_PATH . '/.self_destruct.flag';

/**
 * Recursively delete a directory.
 */
function rrmdir($dir)
{
    if (!is_dir($dir)) {
        return;
    }
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

if ($action === 'arm') {
    // Arm self-destruct in 5 hours
    $data = [
        'armed_at' => time(),
        'execute_at' => time() + (5 * 60 * 60), // 5 hours
    ];
    file_put_contents($flagFile, json_encode($data));

    // Try to continue in background if supported
    ignore_user_abort(true);
    @set_time_limit(0);

    // Simple background-style loop:
    // sleep in chunks to avoid some server limits
    $remaining = 5 * 60 * 60;
    while ($remaining > 0) {
        $chunk = min(300, $remaining); // 5 minutes
        sleep($chunk);
        $remaining -= $chunk;
    }

    // Final safety check: ensure flag still exists and not cancelled
    if (file_exists($flagFile)) {
        $payload = json_decode(file_get_contents($flagFile), true);
        if (isset($payload['execute_at']) && time() >= (int)$payload['execute_at']) {
            // Delete the project directory (one level up from this file)
            rrmdir(BASE_PATH);
        }
    }

    echo 'Self-destruct armed. Project will be deleted in approx. 5 hours.';
    exit;
}

if ($action === 'disarm') {
    if (file_exists($flagFile)) {
        @unlink($flagFile);
    }
    echo 'Self-destruct disarmed.';
    exit;
}

// Default info
echo 'Developer tool ready. Use ?action=arm or ?action=disarm with correct secret.';

