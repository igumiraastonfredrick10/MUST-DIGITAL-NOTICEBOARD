<?php
/**
 * Get Post Details with Comments and Files
 */

ob_start();

require_once '../config/database.php';
require_once '../config/config.php';

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID required']);
    exit();
}

$post_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    $conn = getDBConnection();

    // Get post details with allow_comments check
    $stmt = $conn->prepare("SELECT n.*, c.name as category_name, c.icon as category_icon,
                            CONCAT(u.first_name, ' ', u.last_name) as user_name,
                            at.slug as audience_type_slug,
                            na.faculty_id, na.department_id, na.program_id, na.year_level,
                            (n.priority = 'urgent') as is_urgent,
                            (SELECT COUNT(*) FROM notice_likes WHERE notice_id = n.id) as like_count,
                            (SELECT COUNT(*) FROM notice_likes WHERE notice_id = n.id AND user_id = ?) as user_liked,
                            1 as allow_comments
                            FROM notices n
                            LEFT JOIN categories c ON n.category_id = c.id
                            LEFT JOIN users u ON n.author_id = u.id
                            LEFT JOIN notice_audiences na ON n.id = na.notice_id
                            LEFT JOIN audience_types at ON na.audience_type_id = at.id
                            WHERE n.id = ? AND n.status = 'published'");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Post not found']);
        exit();
    }

    $post = $result->fetch_assoc();
    $stmt->close();

    // Get audience value
    $audience_value = '';
    if ($post['faculty_id']) {
        $facStmt = $conn->prepare("SELECT name FROM faculties WHERE id = ?");
        $facStmt->bind_param("i", $post['faculty_id']);
        $facStmt->execute();
        $facResult = $facStmt->get_result();
        if ($facResult->num_rows > 0) {
            $audience_value = $facResult->fetch_assoc()['name'];
        }
        $facStmt->close();
    } elseif ($post['department_id']) {
        $deptStmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
        $deptStmt->bind_param("i", $post['department_id']);
        $deptStmt->execute();
        $deptResult = $deptStmt->get_result();
        if ($deptResult->num_rows > 0) {
            $audience_value = $deptResult->fetch_assoc()['name'];
        }
        $deptStmt->close();
    } elseif ($post['program_id']) {
        $progStmt = $conn->prepare("SELECT name FROM programs WHERE id = ?");
        $progStmt->bind_param("i", $post['program_id']);
        $progStmt->execute();
        $progResult = $progStmt->get_result();
        if ($progResult->num_rows > 0) {
            $audience_value = $progResult->fetch_assoc()['name'];
        }
        $progStmt->close();
    } elseif ($post['year_level']) {
        $audience_value = (string)$post['year_level'];
    }

    $post['audience_type'] = $post['audience_type_slug'];
    $post['audience_value'] = $audience_value;

    // Get attachments
    $fileStmt = $conn->prepare("SELECT id, file_name, file_path, file_type, file_size 
                                FROM notice_attachments 
                                WHERE notice_id = ? 
                                ORDER BY display_order");
    $fileStmt->bind_param("i", $post_id);
    $fileStmt->execute();
    $fileResult = $fileStmt->get_result();
    $files = [];
    while ($file = $fileResult->fetch_assoc()) {
        $files[] = $file;
    }
    $fileStmt->close();

    // Get comments with user info
    $commentStmt = $conn->prepare("SELECT nc.id, nc.content, nc.created_at,
                                   CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                   u.id as user_id
                                   FROM notice_comments nc
                                   LEFT JOIN users u ON nc.user_id = u.id
                                   WHERE nc.notice_id = ?
                                   ORDER BY nc.created_at ASC");
    $commentStmt->bind_param("i", $post_id);
    $commentStmt->execute();
    $commentResult = $commentStmt->get_result();
    $comments = [];
    while ($comment = $commentResult->fetch_assoc()) {
        $comments[] = $comment;
    }
    $commentStmt->close();

    closeDBConnection($conn);

    echo json_encode([
        'success' => true,
        'post' => $post,
        'files' => $files,
        'comments' => $comments
    ]);

} catch (Exception $e) {
    error_log("Get post details error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading post details'
    ]);
}
exit();
?>