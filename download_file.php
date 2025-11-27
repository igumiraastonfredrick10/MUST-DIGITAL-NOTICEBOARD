<?php
require_once 'config/database.php';
require_once 'config/config.php';

if (!isLoggedIn()) {
    header('Location: auth/login.php');
    exit();
}

$conn = getDBConnection();
$file_id = intval($_GET['id'] ?? 0);

if (!$file_id) {
    header('Location: index.php');
    exit();
}

$stmt = $conn->prepare("SELECT a.*, n.author_id FROM notice_attachments a LEFT JOIN notices n ON a.notice_id = n.id WHERE a.id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$file = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$file || !file_exists($file['file_path'])) {
    header('Location: index.php');
    exit();
}

// Security: Ensure user can access (admin sees all, others see public/own)
if ($_SESSION['user_role'] !== 'admin' && $file['author_id'] != $_SESSION['user_id']) {
    // Add audience check if needed
    header('Location: index.php');
    exit();
}

// Download
header('Content-Type: ' . $file['file_type']);
header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
header('Content-Length: ' . $file['file_size']);
header('Cache-Control: private');

readfile($file['file_path']);

closeDBConnection($conn);
exit();
?>