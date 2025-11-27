<?php
/**
 * Database Configuration and Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error settings
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/../logs/error.log');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mustnn');

// File Upload Configuration
define('UPLOAD_DIR', dirname(__FILE__) . '/../uploads/');
define('MAX_FILE_SIZE', 10485760);

// Allowed file types
if (!defined('ALLOWED_FILE_TYPES')) {
    define('ALLOWED_FILE_TYPES', json_encode([
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg',
        'pdf', 'txt',
        'mp4', 'webm', 'ogg',
        'mp3', 'wav', 'm4a'
    ]));
}

if (!defined('ALLOWED_MIME_TYPES')) {
    define('ALLOWED_MIME_TYPES', json_encode([
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
        'image/webp', 'image/bmp', 'image/svg+xml',
        'application/pdf', 'text/plain',
        'video/mp4', 'video/webm', 'video/ogg',
        'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/x-m4a'
    ]));
}

// Helper functions
function getAllowedFileTypes() {
    return json_decode(ALLOWED_FILE_TYPES, true);
}

function getAllowedMimeTypes() {
    return json_decode(ALLOWED_MIME_TYPES, true);
}

function isFileTypeAllowed($filename, $mimeType = '') {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($ext, getAllowedFileTypes())) {
        return false;
    }
    if ($mimeType && !in_array($mimeType, getAllowedMimeTypes())) {
        return false;
    }
    return true;
}

function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Connection failed");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

function logActivity($conn, $user_id, $action, $entity_type = null, $entity_id = null) {
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("issi", $user_id, $action, $entity_type, $entity_id);
    $stmt->execute();
    $stmt->close();
}