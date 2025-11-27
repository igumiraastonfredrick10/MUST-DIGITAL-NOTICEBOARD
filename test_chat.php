<?php
session_start();

// Set up a test session if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['name'] = 'Test User';
}

echo "<h2>Chat API Test</h2>";

// Show current session data
echo "<h3>Current Session Data:</h3>";
echo "<p>User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "</p>";
echo "<p>User Name: " . ($_SESSION['user_name'] ?? 'Not set') . "</p>";
echo "<p>Name: " . ($_SESSION['name'] ?? 'Not set') . "</p>";
echo "<p>User Role: " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";

// Test 1: Check if API file exists
if (file_exists('api/chat.php')) {
    echo "<p>✅ Chat API file exists</p>";
} else {
    echo "<p>❌ Chat API file missing</p>";
}

// Test 2: Test online status update
echo "<h3>Testing Online Status Update:</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/work/api/chat.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'action=online');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Response: $response</p>";

// Test 3: Test get users
echo "<h3>Testing Get Online Users:</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/work/api/chat.php?action=users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Response: $response</p>";

// Test 4: Test send message
echo "<h3>Testing Send Message:</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/work/api/chat.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'action=send&message=Test message from API test');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Response: $response</p>";

// Test 5: Test get messages
echo "<h3>Testing Get Messages:</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/work/api/chat.php?action=get&last_id=0');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Response: $response</p>";

echo "<p><a href='index.php'>Back to Dashboard</a></p>";
?>