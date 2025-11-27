<?php
ob_start();
require_once '../config/database.php';
require_once '../config/config.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'posts' => [], 'count' => 0]);
    exit();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

try {
    error_log("=== GET POSTS START ===");
    error_log("User ID: " . $user_id);
    
    $conn = getDBConnection();
    error_log("Database connection successful");

    $sql = "SELECT DISTINCT
                n.id, n.title, n.content, n.priority, n.status, n.publish_date, 
                n.expiry_date, n.is_pinned, n.view_count, n.slug, n.created_at, n.updated_at,
                c.name as category_name, c.icon as category_icon,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                u.username,
                at.slug as audience_type,
                (n.priority = 'urgent') as is_urgent,
                (SELECT COUNT(*) FROM notice_comments WHERE notice_id = n.id) as comment_count,
                (SELECT COUNT(*) FROM notice_likes WHERE notice_id = n.id) as like_count,
                (SELECT COUNT(*) FROM notice_attachments WHERE notice_id = n.id) as media_count,
                EXISTS(SELECT 1 FROM notice_likes WHERE notice_id = n.id AND user_id = ?) as user_liked
            FROM notices n
            LEFT JOIN categories c ON n.category_id = c.id
            LEFT JOIN users u ON n.author_id = u.id
            LEFT JOIN notice_audiences na ON n.id = na.notice_id
            LEFT JOIN audience_types at ON na.audience_type_id = at.id
            WHERE n.status = 'published'
            AND (n.expiry_date IS NULL OR n.expiry_date > NOW())
            ORDER BY n.is_pinned DESC, n.publish_date DESC, n.created_at DESC";

    error_log("Preparing query...");
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    error_log("Query executed. Rows: " . $result->num_rows);

    $posts = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Get audience value by reconstructing from notice_audiences table
            $audience_value = '';
            
            // Get all audience entries for this notice
            $audienceStmt = $conn->prepare("
                SELECT na.faculty_id, na.department_id, na.program_id, na.year_level,
                       f.name as faculty_name, d.name as department_name, p.name as program_name
                FROM notice_audiences na
                LEFT JOIN faculties f ON na.faculty_id = f.id
                LEFT JOIN departments d ON na.department_id = d.id
                LEFT JOIN programs p ON na.program_id = p.id
                WHERE na.notice_id = ?
            ");
            $audienceStmt->bind_param("i", $row['id']);
            $audienceStmt->execute();
            $audienceResult = $audienceStmt->get_result();
            
            $audience_parts = array();
            
            if ($audienceResult && $audienceResult->num_rows > 0) {
                while ($audienceRow = $audienceResult->fetch_assoc()) {
                    if ($audienceRow['program_id'] && $audienceRow['year_level']) {
                        // Faculty with program-year combination
                        $audience_parts[] = $audienceRow['program_name'] . ' Year ' . $audienceRow['year_level'];
                    } elseif ($audienceRow['faculty_id']) {
                        // Faculty only
                        $audience_parts[] = $audienceRow['faculty_name'];
                    } elseif ($audienceRow['department_id']) {
                        // Department only
                        $audience_parts[] = $audienceRow['department_name'];
                    } elseif ($audienceRow['program_id']) {
                        // Program only
                        $audience_parts[] = $audienceRow['program_name'];
                    } elseif ($audienceRow['year_level']) {
                        // Year only
                        $audience_parts[] = 'Year ' . $audienceRow['year_level'];
                    }
                }
            }
            
            $audience_value = implode(', ', array_unique($audience_parts));
            $audienceStmt->close();
            
            error_log("Post ID {$row['id']}: audience_type={$row['audience_type']}, audience_value=$audience_value");
            
            $posts[] = array(
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'content' => $row['content'],
                'category' => $row['category_name'],
                'category_name' => $row['category_name'],
                'category_icon' => $row['category_icon'],
                'user_name' => $row['user_name'],
                'user_id' => $row['username'],
                'audience_type' => $row['audience_type'],
                'audience_value' => $audience_value,
                'is_urgent' => (bool)$row['is_urgent'],
                'is_pinned' => (bool)$row['is_pinned'],
                'created_at' => $row['created_at'],
                'publish_date' => $row['publish_date'],
                'like_count' => (int)$row['like_count'],
                'comment_count' => (int)$row['comment_count'],
                'media_count' => (int)$row['media_count'],
                'user_liked' => (bool)$row['user_liked'],
                'view_count' => (int)$row['view_count']
            );
        }
    }

    $stmt->close();
    closeDBConnection($conn);

    error_log("Posts loaded: " . count($posts));
    error_log("=== GET POSTS END ===");

    echo json_encode(array(
        'success' => true,
        'posts' => $posts,
        'count' => count($posts)
    ));

} catch (Exception $e) {
    error_log("Get posts error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode(array(
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'posts' => array(),
        'count' => 0
    ));
}
exit();