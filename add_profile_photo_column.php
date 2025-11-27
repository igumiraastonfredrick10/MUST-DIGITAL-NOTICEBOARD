<?php
/**
 * Database Migration: Add profile_photo column to users table
 * This script adds the profile_photo column to support user profile pictures
 */

require_once 'config/database.php';

try {
    $conn = getDBConnection();
    
    // Check if profile_photo column already exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_photo'");
    
    if ($checkColumn->num_rows == 0) {
        // Add profile_photo column to users table
        $sql = "ALTER TABLE users ADD COLUMN profile_photo VARCHAR(500) NULL AFTER email";
        
        if ($conn->query($sql) === TRUE) {
            echo "✅ Successfully added profile_photo column to users table\n";
        } else {
            echo "❌ Error adding profile_photo column: " . $conn->error . "\n";
        }
    } else {
        echo "ℹ️  profile_photo column already exists in users table\n";
    }
    
    // Create uploads/profiles directory if it doesn't exist
    $upload_dir = 'uploads/profiles/';
    if (!is_dir($upload_dir)) {
        if (mkdir($upload_dir, 0777, true)) {
            echo "✅ Created uploads/profiles directory\n";
        } else {
            echo "❌ Failed to create uploads/profiles directory\n";
        }
    } else {
        echo "ℹ️  uploads/profiles directory already exists\n";
    }
    
    // Create .htaccess file to protect uploads directory
    $htaccess_content = "# Protect uploads directory\n";
    $htaccess_content .= "Options -Indexes\n";
    $htaccess_content .= "# Allow image files\n";
    $htaccess_content .= "<FilesMatch \"\\.(jpg|jpeg|png|gif)$\">\n";
    $htaccess_content .= "    Order allow,deny\n";
    $htaccess_content .= "    Allow from all\n";
    $htaccess_content .= "</FilesMatch>\n";
    
    $htaccess_file = 'uploads/.htaccess';
    if (!file_exists($htaccess_file)) {
        if (file_put_contents($htaccess_file, $htaccess_content)) {
            echo "✅ Created .htaccess file for uploads directory\n";
        } else {
            echo "❌ Failed to create .htaccess file\n";
        }
    }
    
    closeDBConnection($conn);
    echo "\n🎉 Profile photo migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
?>