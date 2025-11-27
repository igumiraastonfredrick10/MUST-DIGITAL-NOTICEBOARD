<?php
/**
 * Create Post API - DEBUG VERSION
 * Add error logging to find the issue
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/../logs/create_post_error.log');

// Start output buffering to prevent any accidental output
ob_start();

session_start();
require_once '../config/database.php';
require_once '../config/config.php';

// Clear any output that might have been generated
ob_clean();

header('Content-Type: application/json');

// Log the start
error_log("=== CREATE POST START ===");
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));

if (!isLoggedIn()) {
    error_log("User not logged in");
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
$content = isset($_POST['content']) ? sanitize($_POST['content']) : '';
$category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
$audience_type = isset($_POST['audience_type']) ? sanitize($_POST['audience_type']) : '';
$audience_value = isset($_POST['audience_value']) ? sanitize($_POST['audience_value']) : '';
$is_urgent = isset($_POST['is_urgent']) ? (int)$_POST['is_urgent'] : 0;
$allow_comments = isset($_POST['allow_comments']) ? (int)$_POST['allow_comments'] : 1;

error_log("Parsed data - Title: $title, Category: $category, Audience Type: $audience_type, Audience Value: $audience_value");

// Validation
if (empty($title) || empty($content) || empty($category) || empty($audience_type)) {
    error_log("Validation failed - missing fields");
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    $conn = getDBConnection();
    error_log("Database connected");

    // Get category ID
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        error_log("Category not found: $category");
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Invalid category: ' . $category]);
        exit();
    }

    $category_id = $result->fetch_assoc()['id'];
    $stmt->close();
    error_log("Category ID: $category_id");

    // Insert notice
    $priority = $is_urgent ? 'urgent' : 'normal';
    $slug = strtolower(str_replace(' ', '-', $title)) . '-' . time();

    error_log("Inserting notice with priority: $priority, slug: $slug");

    $stmt = $conn->prepare("INSERT INTO notices (title, content, category_id, author_id, priority, status, slug, publish_date, created_at) 
                            VALUES (?, ?, ?, ?, ?, 'published', ?, NOW(), NOW())");
    $stmt->bind_param("ssiiss", $title, $content, $category_id, $user_id, $priority, $slug);

    if (!$stmt->execute()) {
        error_log("Notice insert failed: " . $stmt->error);
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Error creating post: ' . $stmt->error]);
        exit();
    }

    $notice_id = $stmt->insert_id;
    $stmt->close();
    error_log("Notice inserted with ID: $notice_id");

    // Get audience_type_id
    $stmt = $conn->prepare("SELECT id FROM audience_types WHERE slug = ?");
    $stmt->bind_param("s", $audience_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        error_log("Audience type not found: $audience_type");
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Invalid audience type: ' . $audience_type]);
        exit();
    }

    $audience_type_id = $result->fetch_assoc()['id'];
    $stmt->close();
    error_log("Audience type ID: $audience_type_id");

    // Handle cascading audience (faculty with program-year combinations)
    if ($audience_type === 'faculty' && !empty($audience_value)) {
        error_log("Processing faculty audience with value: $audience_value");
        
        // audience_value format: "Bachelor of Software Engineering Year 2, BIT Year 1"
        $combinations = array_map('trim', explode(',', $audience_value));
        error_log("Combinations: " . print_r($combinations, true));
        
        foreach ($combinations as $combination) {
            // Parse "Bachelor of Software Engineering Year 2" into program and year
            if (preg_match('/^(.+?)\s+Year\s+(\d+)$/i', $combination, $matches)) {
                $program_name = trim($matches[1]);
                $year_level = (int)$matches[2];
                
                error_log("Looking for program: $program_name, year: $year_level");
                
                // Get program_id from program name
                $stmt = $conn->prepare("SELECT id FROM programs WHERE name = ?");
                $stmt->bind_param("s", $program_name);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $program_id = $result->fetch_assoc()['id'];
                    error_log("Found program ID: $program_id");
                    
                    // Insert notice_audience with program and year
                    $insertStmt = $conn->prepare("INSERT INTO notice_audiences (notice_id, audience_type_id, program_id, year_level) 
                                                  VALUES (?, ?, ?, ?)");
                    $insertStmt->bind_param("iiii", $notice_id, $audience_type_id, $program_id, $year_level);
                    
                    if ($insertStmt->execute()) {
                        error_log("Inserted audience: program_id=$program_id, year=$year_level");
                    } else {
                        error_log("Failed to insert audience: " . $insertStmt->error);
                    }
                    
                    $insertStmt->close();
                } else {
                    error_log("Program not found: $program_name");
                }
                $stmt->close();
            } else {
                error_log("Failed to parse combination: $combination");
            }
        }
    } 
    // Handle simple audience types (all, students, lecturers)
    else {
        error_log("Processing simple audience type");
        $stmt = $conn->prepare("INSERT INTO notice_audiences (notice_id, audience_type_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $notice_id, $audience_type_id);
        
        if ($stmt->execute()) {
            error_log("Inserted simple audience");
        } else {
            error_log("Failed to insert simple audience: " . $stmt->error);
        }
        
        $stmt->close();
    }

    // Handle file uploads
    if (isset($_FILES['media']) && !empty($_FILES['media']['name'][0])) {
        error_log("Processing file uploads");
        $upload_dir = UPLOAD_DIR;
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $display_order = 1;
        foreach ($_FILES['media']['name'] as $key => $filename) {
            if ($_FILES['media']['error'][$key] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['media']['tmp_name'][$key];
                $file_size = $_FILES['media']['size'][$key];
                $file_type = $_FILES['media']['type'][$key];
                
                if (!isFileTypeAllowed($filename, $file_type)) {
                    error_log("File type not allowed: $filename");
                    continue;
                }
                
                if ($file_size > MAX_FILE_SIZE) {
                    error_log("File too large: $filename");
                    continue;
                }
                
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $new_filename = uniqid() . '_' . time() . '.' . $ext;
                $file_path = 'uploads/' . $new_filename;
                $full_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($tmp_name, $full_path)) {
                    $stmt = $conn->prepare("INSERT INTO notice_attachments (notice_id, file_name, file_path, file_type, file_size, display_order) 
                                           VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issiii", $notice_id, $filename, $file_path, $file_type, $file_size, $display_order);
                    $stmt->execute();
                    $stmt->close();
                    $display_order++;
                    error_log("File uploaded: $filename");
                }
            }
        }
    }

    // Log activity
    logActivity($conn, $user_id, 'Created post: ' . $title, 'notice', $notice_id);
    error_log("Activity logged");

    closeDBConnection($conn);

    error_log("=== CREATE POST SUCCESS ===");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Post created successfully',
        'post_id' => $notice_id,
        'audience_stored' => $audience_value
    ]);
    
} catch (Exception $e) {
    error_log("EXCEPTION: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

exit();
?>