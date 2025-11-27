<?php
/**
 * Convert Hashed Passwords to Plain Text
 * WARNING: This is for development/testing only - NOT for production use!
 */

require_once 'config/database.php';

echo "<h2>Convert Passwords to Plain Text</h2>\n";
echo "<p style='color: red;'><strong>WARNING:</strong> This will convert all passwords to plain text. Only use for development!</p>\n";

try {
    $conn = getDBConnection();
    
    // Define default passwords for different roles
    $default_passwords = [
        'admin' => 'admin123',
        'lecturer' => 'lecturer123', 
        'student' => 'student123'
    ];
    
    // Get all users
    $result = $conn->query("SELECT id, username, role, password_hash FROM users");
    
    if ($result && $result->num_rows > 0) {
        echo "<h3>Converting Passwords:</h3>\n";
        echo "<ul>\n";
        
        while ($user = $result->fetch_assoc()) {
            $new_password = $default_passwords[$user['role']] ?? 'password123';
            
            // Update password to plain text
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $user['id']);
            
            if ($stmt->execute()) {
                echo "<li style='color: green;'>✅ User '{$user['username']}' ({$user['role']}) → Password: $new_password</li>\n";
            } else {
                echo "<li style='color: red;'>❌ Failed to update user '{$user['username']}'</li>\n";
            }
            
            $stmt->close();
        }
        
        echo "</ul>\n";
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
        echo "<h3>✅ Password Conversion Complete!</h3>\n";
        echo "<p>All users now have plain text passwords. You can now login with:</p>\n";
        echo "<ul>\n";
        echo "<li><strong>Admin:</strong> admin / admin123</li>\n";
        echo "<li><strong>Lecturer:</strong> lecturer1 / lecturer123</li>\n";
        echo "<li><strong>Student:</strong> student1 / student123</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
        
    } else {
        echo "<p style='color: orange;'>⚠️ No users found in database</p>\n";
    }
    
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>\n";
    echo "<h3>❌ Error</h3>\n";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}

echo "<p><a href='auth/login.php'>← Go to Login</a> | <a href='index.php'>Dashboard</a></p>\n";
?>