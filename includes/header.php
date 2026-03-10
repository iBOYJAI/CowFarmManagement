<?php
/**
 * Header Component
 * Includes the top navigation bar
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();
$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Cow Farm Management</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/icons.css">
</head>
<body>
    <?php if ($auth->isLoggedIn()): ?>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <svg class="sidebar-logo-icon" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                </svg>
                <span class="sidebar-menu-text">Cow Farm</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                </svg>
            </button>
        </div>
        <nav>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>dashboard.php" class="sidebar-menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>cows/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'cows') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M4 9l4-4h8l4 4v9a2 2 0 0 1-2 2h-2v-6H8v6H6a2 2 0 0 1-2-2V9z"/>
                            <path d="M10 21v-4h4v4"/>
                        </svg>
                        <span class="sidebar-menu-text">Cows</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>health/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'health') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M19 3H5A2 2 0 0 0 3 5v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-3 8h-3v3h-2v-3H8V9h3V6h2v3h3z"/>
                        </svg>
                        <span class="sidebar-menu-text">Health Records</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>health/vaccinations.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'vaccinations') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M21 5.5l-2.79-2.8-3.22 3.22-1.42-1.42L16.79 1.3 15.5 0 9.79 5.71l1.42 1.42L5 13.36V17h3.64l6.21-6.21 1.42 1.42L21 5.5z"/>
                            <path d="M5 19h14v2H5z"/>
                        </svg>
                        <span class="sidebar-menu-text">Vaccinations</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>breeding/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'breeding') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M16.5 3a4.5 4.5 0 0 0-3.89 2.25A4.5 4.5 0 1 0 8.5 15H11v-2H8.5A2.5 2.5 0 1 1 10.7 9l.8 1.39.8-1.39A2.5 2.5 0 1 1 15.5 13H13v2h2.5A4.5 4.5 0 0 0 16.5 3z"/>
                        </svg>
                        <span class="sidebar-menu-text">Breeding & Pregnancy</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>milk/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'milk') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M7 2l2 3v3.5C9 11.43 10.57 13 12.5 13S16 11.43 16 8.5V5l2-3H7z"/>
                            <path d="M8 14h9v5a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-5z"/>
                        </svg>
                        <span class="sidebar-menu-text">Milk Production</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>feed/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'feed') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M4 4h16v2H4z"/>
                            <path d="M6 8h12v3H6z"/>
                            <path d="M8 13h8v3H8z"/>
                            <path d="M10 18h4v2h-4z"/>
                        </svg>
                        <span class="sidebar-menu-text">Feed & Inventory</span>
                    </a>
                </li>
                <?php if ($auth->hasRole([ROLE_ADMIN, ROLE_MANAGER])): ?>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>users/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                        <span class="sidebar-menu-text">Users</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>expenses/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'expenses') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                        <span class="sidebar-menu-text">Expenses & Sales</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>appointments/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'appointments') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                        </svg>
                        <span class="sidebar-menu-text">Appointments</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>alerts/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'alerts') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <span class="sidebar-menu-text">Alerts & Due Lists</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>reports/index.php" class="sidebar-menu-link <?php echo strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                        </svg>
                        <span class="sidebar-menu-text">Reports</span>
                    </a>
                </li>
                <?php if ($auth->hasRole([ROLE_ADMIN])): ?>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>settings.php" class="sidebar-menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                        <svg class="sidebar-menu-icon" viewBox="0 0 24 24">
                            <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94L14.4 2.81c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                        </svg>
                        <span class="sidebar-menu-text">Settings</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header Bar -->
        <header class="header-bar">
            <div>
                <button class="header-btn mobile-menu-toggle" style="display: none;">
                    <svg class="header-icon" viewBox="0 0 24 24">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                    </svg>
                </button>
                <h1 class="header-title"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
            </div>
            <div class="header-actions">
                <button class="header-btn" data-theme-toggle title="Toggle Dark Mode">
                    <svg class="header-icon" viewBox="0 0 24 24">
                        <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                    </svg>
                </button>
                <div class="user-menu">
                    <div class="user-avatar"><?php echo strtoupper(substr($currentUser['full_name'], 0, 1)); ?></div>
                    <span><?php echo htmlspecialchars($currentUser['full_name']); ?></span>
                    <div style="margin-left: 8px;">
                        <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-sm btn-outline">Logout</a>
                    </div>
                </div>
            </div>
        </header>
    <?php endif; ?>

