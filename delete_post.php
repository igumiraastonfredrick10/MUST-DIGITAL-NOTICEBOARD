<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit();
}

$conn = getDBConnection();

// Check if user owns the post or is admin
$checkStmt = $conn->prepare("SELECT author_id FROM notices WHERE id = ?");
$checkStmt->bind_param("i", $post_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
    $checkStmt->close();
    closeDBConnection($conn);
    exit();
}

$post = $result->fetch_assoc();
$checkStmt->close();

if ($post['author_id'] != $user_id && $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    closeDBConnection($conn);
    exit();
}

// Delete the post
$deleteStmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
$deleteStmt->bind_param("i", $post_id);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting post']);
}

$deleteStmt->close();
closeDBConnection($conn);
?>