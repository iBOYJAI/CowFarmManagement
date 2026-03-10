<?php
/**
 * Database Helper Class
 * Provides common database operations
 */

require_once __DIR__ . '/../config/database.php';

class DBHelper {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    
    /**
     * Get connection
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Execute a query and return all results
     * @param string $query
     * @param array $params
     * @return array
     */
    public function fetchAll($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Execute a query and return single result
     * @param string $query
     * @param array $params
     * @return array|null
     */
    public function fetchOne($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch() ?: null;
        } catch(PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Execute an insert/update/delete query
     * @param string $query
     * @param array $params
     * @return bool|int
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute($params);
            if (stripos($query, 'INSERT') === 0) {
                return $this->conn->lastInsertId();
            }
            return $result;
        } catch(PDOException $e) {
            error_log("Execute Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get paginated results
     * @param string $query
     * @param array $params
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function fetchPaginated($query, $params = [], $page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        $countQuery = "SELECT COUNT(*) as total FROM ($query) as count_query";
        $total = $this->fetchOne($countQuery, $params)['total'] ?? 0;
        
        $paginatedQuery = "$query LIMIT $perPage OFFSET $offset";
        $data = $this->fetchAll($paginatedQuery, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}

