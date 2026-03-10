<?php
/**
 * File Upload Handler Class
 * Handles secure file uploads with validation
 */

require_once __DIR__ . '/../config/config.php';

class FileUpload {
    /**
     * Upload cow photo
     * @param array $file $_FILES array element
     * @param string $tagNumber Cow tag number for filename
     * @return array
     */
    public static function uploadCowPhoto($file, $tagNumber) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error'];
        }
        
        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'File size exceeds maximum allowed (5MB)'];
        }
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            return ['success' => false, 'message' => 'Invalid file type. Only images are allowed.'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'cow_' . preg_replace('/[^a-zA-Z0-9]/', '_', $tagNumber) . '_' . time() . '.' . $extension;
        $filepath = COW_PHOTOS_DIR . $filename;
        
        // Create directory if it doesn't exist
        if (!file_exists(COW_PHOTOS_DIR)) {
            mkdir(COW_PHOTOS_DIR, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => 'uploads/cow_photos/' . $filename
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to save file'];
        }
    }
    
    /**
     * Delete file
     * @param string $filepath
     * @return bool
     */
    public static function deleteFile($filepath) {
        $fullPath = BASE_PATH . '/' . $filepath;
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}

