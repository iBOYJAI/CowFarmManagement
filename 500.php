<?php
/**
 * 500 Error Page
 */

require_once __DIR__ . '/config/config.php';
$pageTitle = 'Server Error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 20px;">
        <div>
            <h1 style="font-size: 6rem; color: var(--danger-color); margin-bottom: 20px;">500</h1>
            <h2 style="margin-bottom: 20px;">Server Error</h2>
            <p style="color: var(--gray-600); margin-bottom: 30px;">An internal server error occurred. Please try again later.</p>
            <a href="<?php echo BASE_URL; ?>dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>

