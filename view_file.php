<?php
/**
 * View File in Browser (with download option)
 */

require_once '../config/database.php';
require_once '../config/config.php';

if (!isLoggedIn()) {
    die('Access denied. Please login.');
}

$file_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$file_id) {
    die('Invalid file ID');
}

$conn = getDBConnection();

// Get file details
$stmt = $conn->prepare("SELECT na.*, n.author_id 
                        FROM notice_attachments na 
                        LEFT JOIN notices n ON na.notice_id = n.id 
                        WHERE na.id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$file = $stmt->get_result()->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

if (!$file) {
    die('File record not found in database');
}

// Adjust path for files in api subdirectory and check multiple possible locations
$possible_paths = [
    '../' . $file['file_path'],                    // Standard path: ../uploads/filename
    '../uploads/notices/' . basename($file['file_path']), // Check notices subdirectory
    $file['file_path']                             // Try original path (in case it's absolute)
];

$file_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $file_path = $path;
        break;
    }
}

if (!$file_path) {
    // Log the missing file for cleanup
    error_log("Missing file - ID: $file_id, Name: " . $file['file_name'] . ", Path: " . $file['file_path']);
    
    // Return a user-friendly error
    header('HTTP/1.0 404 Not Found');
    die('Sorry, this file is no longer available. It may have been moved or deleted.');
}

// Security check - allow access if user is admin, author, or if post is visible to user
if ($_SESSION['user_role'] !== 'admin' && $file['author_id'] != $_SESSION['user_id']) {
    // For now, allow access to all logged-in users
    // TODO: Add proper audience checking here
}

// Debug info (remove in production)
error_log("File access attempt - ID: $file_id, User: " . $_SESSION['user_id'] . ", File: " . $file['file_name']);

// Determine if download or inline display
$download = isset($_GET['download']) && $_GET['download'] == '1';

// Set appropriate headers
header('Content-Type: ' . $file['file_type']);
header('Content-Length: ' . $file['file_size']);

if ($download) {
    header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
} else {
    // Display inline (in browser)
    header('Content-Disposition: inline; filename="' . $file['file_name'] . '"');
}

header('Cache-Control: private, max-age=3600');
header('Pragma: private');

// Output file
readfile($file_path);
exit();
?>