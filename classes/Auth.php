<?php
/**
 * Authentication Class
 * Handles user authentication, login, logout, and session management
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    
    /**
     * Login user
     * @param string $username
     * @param string $password
     * @return array
     */
    public function login($username, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, email, password, full_name, role, status FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user) {
                $isValidPassword = password_verify($password, $user['password']);

                // Handle legacy default admin password from schema.sql (hash of "password")
                // but README/UI say default is "admin123". If the admin is still using the
                // old default hash and tries "admin123", transparently upgrade the password.
                if (!$isValidPassword) {
                    $legacyDefaultHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
                    if (
                        $user['username'] === 'admin' &&
                        $password === 'admin123' &&
                        $user['password'] === $legacyDefaultHash
                    ) {
                        // Upgrade stored password hash to "admin123"
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $updateStmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $updateStmt->execute([$newHash, $user['id']]);

                        $isValidPassword = true;
                    }
                }

                if ($isValidPassword) {
                    if ($user['status'] === 'inactive') {
                        return ['success' => false, 'message' => 'Your account is inactive. Please contact administrator.'];
                    }
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['logged_in'] = true;
                    
                    // Log activity
                    $this->logActivity($user['id'], 'login', 'Authentication', 'User logged in');
                    
                    return ['success' => true, 'message' => 'Login successful', 'role' => $user['role']];
                }
            }

            return ['success' => false, 'message' => 'Invalid username or password'];
        } catch(PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Register new user
     * @param array $data
     * @return array
     */
    public function register($data) {
        try {
            // Check if username or email already exists
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$data['username'], $data['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, full_name, role, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['full_name'],
                $data['role'] ?? ROLE_STAFF,
                $data['phone'] ?? null
            ]);
            
            $userId = $this->conn->lastInsertId();
            $this->logActivity($userId, 'register', 'Authentication', 'New user registered');
            
            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
        } catch(PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Check if user has required role
     * @param array $allowedRoles
     * @return bool
     */
    public function hasRole($allowedRoles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        return in_array($_SESSION['role'], $allowedRoles);
    }
    
    /**
     * Require login - redirect if not logged in
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }
    }
    
    /**
     * Require role - redirect if user doesn't have required role
     * @param array $allowedRoles
     */
    public function requireRole($allowedRoles) {
        $this->requireLogin();
        if (!$this->hasRole($allowedRoles)) {
            header('Location: ' . BASE_URL . 'dashboard.php');
            exit;
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logActivity($_SESSION['user_id'], 'logout', 'Authentication', 'User logged out');
        }
        
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
    
    /**
     * Get current user data
     * @return array|null
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->conn->prepare("SELECT id, username, email, full_name, role, phone, status FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Log user activity
     * @param int $userId
     * @param string $action
     * @param string $module
     * @param string $description
     */
    private function logActivity($userId, $action, $module, $description) {
        try {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $stmt = $this->conn->prepare("INSERT INTO activity_logs (user_id, action, module, description, ip_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $action, $module, $description, $ipAddress]);
        } catch(PDOException $e) {
            error_log("Activity Log Error: " . $e->getMessage());
        }
    }
}

