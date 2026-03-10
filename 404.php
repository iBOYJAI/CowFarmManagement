<?php
/**
 * 404 Error Page
 */

require_once __DIR__ . '/config/config.php';
$pageTitle = 'Page Not Found';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 20px;">
        <div>
            <h1 style="font-size: 6rem; color: var(--primary-color); margin-bottom: 20px;">404</h1>
            <h2 style="margin-bottom: 20px;">Page Not Found</h2>
            <p style="color: var(--gray-600); margin-bottom: 30px;">The page you are looking for does not exist.</p>
            <a href="<?php echo BASE_URL; ?>dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>

