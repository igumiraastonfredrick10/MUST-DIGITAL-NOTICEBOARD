<?php
/**
 * Test script to verify audience filtering fix
 */
session_start();
require_once 'config/database.php';
require_once 'config/config.php';

// Simulate a logged-in user (replace with actual user data)
if (!isset($_SESSION['user_id'])) {
    echo "Please log in first to test the audience filtering.\n";
    echo "Visit: <a href='auth/login.php'>Login Page</a>\n";
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Test User';
$user_role = $_SESSION['user_role'] ?? 'student';
$faculty_name = $_SESSION['faculty_name'] ?? 'Faculty of Computing and Informatics';
$program_name = $_SESSION['program_name'] ?? 'Bachelor of Software Engineering';
$year_of_study = $_SESSION['year_of_study'] ?? '2';

echo "<h2>Testing Audience Filtering Fix</h2>\n";
echo "<h3>Current User Info:</h3>\n";
echo "<ul>\n";
echo "<li>User ID: $user_id</li>\n";
echo "<li>Name: $user_name</li>\n";
echo "<li>Role: $user_role</li>\n";
echo "<li>Faculty: $faculty_name</li>\n";
echo "<li>Program: $program_name</li>\n";
echo "<li>Year: $year_of_study</li>\n";
echo "</ul>\n";

// Test the API
echo "<h3>Testing API Response:</h3>\n";
$api_url = 'api/get_posts.php?user_id=' . $user_id;

// Use cURL to test the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/work/' . $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($data && $data['success']) {
    echo "<p><strong>API Success!</strong> Found " . count($data['posts']) . " posts.</p>\n";
    
    echo "<h3>Posts with Faculty Audience:</h3>\n";
    $faculty_posts = 0;
    
    foreach ($data['posts'] as $post) {
        if ($post['audience_type'] === 'faculty') {
            $faculty_posts++;
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>\n";
            echo "<h4>" . htmlspecialchars($post['title']) . "</h4>\n";
            echo "<p><strong>Audience Type:</strong> " . htmlspecialchars($post['audience_type']) . "</p>\n";
            echo "<p><strong>Audience Value:</strong> " . htmlspecialchars($post['audience_value']) . "</p>\n";
            echo "<p><strong>Content:</strong> " . htmlspecialchars(substr($post['content'], 0, 100)) . "...</p>\n";
            echo "<p><strong>Created:</strong> " . htmlspecialchars($post['created_at']) . "</p>\n";
            echo "</div>\n";
        }
    }
    
    if ($faculty_posts === 0) {
        echo "<p><em>No faculty-targeted posts found. Try creating a post with faculty audience to test.</em></p>\n";
    }
    
} else {
    echo "<p><strong>API Error:</strong> " . ($data['message'] ?? 'Unknown error') . "</p>\n";
    echo "<pre>" . htmlspecialchars($response) . "</pre>\n";
}

echo "<h3>JavaScript Test:</h3>\n";
echo "<p>Open browser console to see detailed filtering logs when you visit the main dashboard.</p>\n";

echo "<p><a href='index.php'>← Back to Dashboard</a></p>\n";
?>