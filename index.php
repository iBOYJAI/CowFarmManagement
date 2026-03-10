<?php
/**
 * Index Page - Redirects to login or dashboard
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();

if ($auth->isLoggedIn()) {
    header('Location: ' . BASE_URL . 'dashboard.php');
} else {
    header('Location: ' . BASE_URL . 'login.php');
}
exit;

