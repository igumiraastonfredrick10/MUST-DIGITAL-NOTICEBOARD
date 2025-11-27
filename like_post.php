<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'Not logged in'));
    exit();
}

if (!isset($_POST['post_id'])) {
    echo json_encode(array('success' => false, 'message' => 'Post ID required'));
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = (int)$_POST['post_id'];

$conn = getDBConnection();

// Check if already liked
$checkStmt = $conn->prepare("SELECT id FROM notice_likes WHERE notice_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $post_id, $user_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    // Unlike - remove the like
    $deleteStmt = $conn->prepare("DELETE FROM notice_likes WHERE notice_id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $post_id, $user_id);
    $deleteStmt->execute();
    $deleteStmt->close();
    $liked = false;
} else {
    // Like - add the like
    $insertStmt = $conn->prepare("INSERT INTO notice_likes (notice_id, user_id, created_at) VALUES (?, ?, NOW())");
    $insertStmt->bind_param("ii", $post_id, $user_id);
    $insertStmt->execute();
    $insertStmt->close();
    $liked = true;
}

$checkStmt->close();

// Get total like count
$countStmt = $conn->prepare("SELECT COUNT(*) as count FROM notice_likes WHERE notice_id = ?");
$countStmt->bind_param("i", $post_id);
$countStmt->execute();
$countResult = $countStmt->get_result();
$countData = $countResult->fetch_assoc();
$like_count = (int)$countData['count'];
$countStmt->close();

closeDBConnection($conn);

echo json_encode(array(
    'success' => true,
    'liked' => $liked,
    'like_count' => $like_count
));
?>