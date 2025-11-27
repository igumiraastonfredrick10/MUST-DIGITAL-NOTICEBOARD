<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit();
}

try {
    $conn = getDBConnection();

    // Get post details with category info
    $stmt = $conn->prepare("
        SELECT n.*, c.name as category_name 
        FROM notices n 
        LEFT JOIN categories c ON n.category_id = c.id 
        WHERE n.id = ? AND n.author_id = ?
    ");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Post not found or unauthorized']);
        $stmt->close();
        closeDBConnection($conn);
        exit();
    }

    $post = $result->fetch_assoc();
    $stmt->close();

    // Check if within 1 hour edit window
    $created_time = strtotime($post['created_at']);
    $current_time = time();
    $time_diff = $current_time - $created_time;
    $one_hour = 3600; // 1 hour in seconds

    $can_edit = ($time_diff <= $one_hour) || ($_SESSION['user_role'] === 'admin');
    $remaining_time = $can_edit ? max(0, $one_hour - $time_diff) : 0;

    echo json_encode([
        'success' => true,
        'post' => [
            'id' => $post['id'],
            'title' => $post['title'],
            'content' => $post['content'],
            'category_name' => $post['category_name'],
            'priority' => $post['priority'],
            'is_urgent' => $post['priority'] === 'urgent',
            'created_at' => $post['created_at']
        ],
        'can_edit' => $can_edit,
        'remaining_time' => $remaining_time,
        'time_expired' => !$can_edit
    ]);

    closeDBConnection($conn);

} catch (Exception $e) {
    error_log("Get post for edit error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>