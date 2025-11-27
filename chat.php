<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Debug session data
error_log("Chat API - Session data: " . print_r($_SESSION, true));

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated', 'session_debug' => $_SESSION]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch ($action) {
        case 'send':
            sendMessage($conn);
            break;
        case 'get':
            getMessages($conn);
            break;
        case 'online':
            updateOnlineStatus($conn);
            break;
        case 'users':
            getOnlineUsers($conn);
            break;
        case 'session':
            echo json_encode([
                'session_data' => $_SESSION,
                'user_id' => $_SESSION['user_id'] ?? null,
                'user_name' => $_SESSION['user_name'] ?? null,
                'name' => $_SESSION['name'] ?? null
            ]);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
    
    closeDBConnection($conn);
} catch (Exception $e) {
    error_log("Chat API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

function sendMessage($conn) {
    $message = trim($_POST['message'] ?? '');
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['user_name'] ?? 'Unknown User';
    
    // Debug logging
    error_log("Chat sendMessage - User ID: $user_id, Username: $username, Message: $message");
    
    if (empty($message)) {
        http_response_code(400);
        echo json_encode(['error' => 'Message cannot be empty']);
        return;
    }
    
    if (strlen($message) > 500) {
        http_response_code(400);
        echo json_encode(['error' => 'Message too long']);
        return;
    }
    
    // Create chat_messages table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        username VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at)
    )";
    
    if (!$conn->query($createTable)) {
        throw new Exception("Error creating chat_messages table: " . $conn->error);
    }
    
    $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, username, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("iss", $user_id, $username, $message);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message_id' => $conn->insert_id]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();
}

function getMessages($conn) {
    $last_id = intval($_GET['last_id'] ?? 0);
    
    // Create table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        username VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at)
    )";
    
    if (!$conn->query($createTable)) {
        throw new Exception("Error creating chat_messages table: " . $conn->error);
    }
    
    $stmt = $conn->prepare("
        SELECT id, username, message, created_at 
        FROM chat_messages 
        WHERE id > ? 
        ORDER BY created_at ASC 
        LIMIT 50
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $last_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'id' => (int)$row['id'],
            'username' => $row['username'],
            'message' => $row['message'],
            'timestamp' => date('g:i A', strtotime($row['created_at']))
        ];
    }
    
    echo json_encode(['messages' => $messages]);
    $stmt->close();
}

function updateOnlineStatus($conn) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['user_name'] ?? 'Unknown User';
    
    // Create online_users table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS online_users (
        user_id INT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTable)) {
        throw new Exception("Error creating online_users table: " . $conn->error);
    }
    
    $stmt = $conn->prepare("
        INSERT INTO online_users (user_id, username, last_seen) 
        VALUES (?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE last_seen = NOW()
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("is", $user_id, $username);
    
    if ($stmt->execute()) {
        // Clean up old entries (users offline for more than 5 minutes)
        $conn->query("DELETE FROM online_users WHERE last_seen < DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();
}

function getOnlineUsers($conn) {
    // Create table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS online_users (
        user_id INT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTable)) {
        throw new Exception("Error creating online_users table: " . $conn->error);
    }
    
    // Clean up old entries first
    $conn->query("DELETE FROM online_users WHERE last_seen < DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    
    $result = $conn->query("SELECT username FROM online_users ORDER BY username");
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['username'];
    }
    
    echo json_encode([
        'users' => $users,
        'count' => count($users)
    ]);
}
?>