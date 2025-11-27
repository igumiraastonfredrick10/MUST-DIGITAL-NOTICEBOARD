<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

require_once '../config/database.php';
require_once '../config/config.php';

$user_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit();
}

try {
    $conn = getDBConnection();

    // Check if user owns the post and if it's within edit time limit (1 hour)
    $checkStmt = $conn->prepare("SELECT author_id, created_at, title FROM notices WHERE id = ?");
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

    // Check ownership
    if ($post['author_id'] != $user_id && $_SESSION['user_role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        closeDBConnection($conn);
        exit();
    }

    // Check if within 1 hour edit window
    $created_time = strtotime($post['created_at']);
    $current_time = time();
    $time_diff = $current_time - $created_time;
    $one_hour = 3600; // 1 hour in seconds

    if ($time_diff > $one_hour && $_SESSION['user_role'] !== 'admin') {
        echo json_encode([
            'success' => false, 
            'message' => 'Edit time expired. Posts can only be edited within 1 hour of creation. You can delete this post instead.',
            'can_delete' => true
        ]);
        closeDBConnection($conn);
        exit();
    }

    // Get the data to update
    $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
    $content = isset($_POST['content']) ? sanitize($_POST['content']) : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    $is_urgent = isset($_POST['is_urgent']) ? (int)$_POST['is_urgent'] : 0;

    // Validation
    if (empty($title) || empty($content) || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        closeDBConnection($conn);
        exit();
    }

    // Get category ID
    $categoryStmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $categoryStmt->bind_param("s", $category);
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->get_result();

    if ($categoryResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        $categoryStmt->close();
        closeDBConnection($conn);
        exit();
    }

    $category_id = $categoryResult->fetch_assoc()['id'];
    $categoryStmt->close();

    // Update the post
    $priority = $is_urgent ? 'urgent' : 'normal';
    
    $updateStmt = $conn->prepare("UPDATE notices SET title = ?, content = ?, category_id = ?, priority = ?, updated_at = NOW() WHERE id = ?");
    $updateStmt->bind_param("ssisi", $title, $content, $category_id, $priority, $post_id);

    if ($updateStmt->execute()) {
        // Log activity
        logActivity($conn, $user_id, 'Updated post: ' . $title, 'notice', $post_id);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Post updated successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating post']);
    }

    $updateStmt->close();
    closeDBConnection($conn);

} catch (Exception $e) {
    error_log("Edit post error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>