<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (!isset($_POST['post_id']) || !isset($_POST['content'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID and content required']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = (int)$_POST['post_id'];
$content = trim($_POST['content']);

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    exit();
}

$conn = getDBConnection();

// Insert comment
$stmt = $conn->prepare("INSERT INTO notice_comments (notice_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $post_id, $user_id, $content);

if ($stmt->execute()) {
    $comment_id = $stmt->insert_id;
    $stmt->close();
    closeDBConnection($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Comment added successfully',
        'comment_id' => $comment_id
    ]);
} else {
    $stmt->close();
    closeDBConnection($conn);
    
    echo json_encode([
        'success' => false,
        'message' => 'Error adding comment: ' . $conn->error
    ]);
}
?>