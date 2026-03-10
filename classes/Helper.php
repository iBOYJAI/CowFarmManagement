<?php
/**
 * Helper Functions Class
 * Contains utility functions used throughout the application
 */

class Helper {
    /**
     * Sanitize input data
     * @param mixed $data
     * @return mixed
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Format date for display
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function formatDate($date, $format = 'Y-m-d') {
        if (empty($date) || $date === '0000-00-00') {
            return '-';
        }
        return date($format, strtotime($date));
    }
    
    /**
     * Format currency
     * @param float $amount
     * @return string
     */
    public static function formatCurrency($amount) {
        return number_format($amount, 2);
    }
    
    /**
     * Get status badge HTML
     * @param string $status
     * @return string
     */
    public static function getStatusBadge($status) {
        $badges = [
            'active' => '<span class="badge badge-success">Active</span>',
            'inactive' => '<span class="badge badge-danger">Inactive</span>',
            'sold' => '<span class="badge badge-warning">Sold</span>',
            'deceased' => '<span class="badge badge-dark">Deceased</span>',
            'pregnant' => '<span class="badge badge-info">Pregnant</span>',
            'not_pregnant' => '<span class="badge badge-secondary">Not Pregnant</span>',
            'scheduled' => '<span class="badge badge-primary">Scheduled</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
            'paid' => '<span class="badge badge-success">Paid</span>',
            'pending' => '<span class="badge badge-warning">Pending</span>',
        ];
        return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }
    
    /**
     * Redirect to URL
     * @param string $url
     */
    public static function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Generate pagination HTML
     * @param int $currentPage
     * @param int $totalPages
     * @param string $baseUrl
     * @return string
     */
    public static function generatePagination($currentPage, $totalPages, $baseUrl) {
        if ($totalPages <= 1) {
            return '';
        }
        
        $html = '<div class="pagination">';
        $html .= '<a href="' . $baseUrl . '&page=' . max(1, $currentPage - 1) . '" class="btn-pagination' . ($currentPage <= 1 ? ' disabled' : '') . '">Previous</a>';
        
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)) {
                $html .= '<a href="' . $baseUrl . '&page=' . $i . '" class="btn-pagination' . ($i == $currentPage ? ' active' : '') . '">' . $i . '</a>';
            } elseif ($i == $currentPage - 3 || $i == $currentPage + 3) {
                $html .= '<span class="pagination-ellipsis">...</span>';
            }
        }
        
        $html .= '<a href="' . $baseUrl . '&page=' . min($totalPages, $currentPage + 1) . '" class="btn-pagination' . ($currentPage >= $totalPages ? ' disabled' : '') . '">Next</a>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Calculate days until date
     * @param string $date
     * @return int
     */
    public static function daysUntil($date) {
        if (empty($date) || $date === '0000-00-00') {
            return null;
        }
        $today = new DateTime();
        $target = new DateTime($date);
        $diff = $today->diff($target);
        return $diff->days * ($target > $today ? 1 : -1);
    }
}

