<?php
/**
 * Database Setup Script
 * This script will create or update the database structure for the audience filtering system
 */

require_once 'config/database.php';

echo "<h2>Database Setup for Audience Filtering System</h2>\n";

try {
    $conn = getDBConnection();
    
    // Read and execute the SQL schema
    $sql_file = 'database_schema.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("SQL schema file not found: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt) && !preg_match('/^\s*\/\*/', $stmt);
        }
    );
    
    echo "<h3>Executing Database Setup...</h3>\n";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 400px; overflow-y: auto;'>\n";
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        echo "<p><strong>Statement " . ($index + 1) . ":</strong> ";
        
        // Show first 100 characters of statement
        $preview = strlen($statement) > 100 ? substr($statement, 0, 100) . '...' : $statement;
        echo htmlspecialchars($preview) . "</p>\n";
        
        try {
            if ($conn->query($statement)) {
                echo "<p style='color: green; margin-left: 20px;'>✅ Success</p>\n";
                $success_count++;
            } else {
                echo "<p style='color: red; margin-left: 20px;'>❌ Error: " . htmlspecialchars($conn->error) . "</p>\n";
                $error_count++;
            }
        } catch (Exception $e) {
            echo "<p style='color: red; margin-left: 20px;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>\n";
            $error_count++;
        }
        
        echo "<hr style='margin: 10px 0; border: 1px solid #dee2e6;'>\n";
        flush();
    }
    
    echo "</div>\n";
    
    echo "<h3>Setup Summary:</h3>\n";
    echo "<ul>\n";
    echo "<li style='color: green;'>✅ Successful statements: $success_count</li>\n";
    echo "<li style='color: " . ($error_count > 0 ? 'red' : 'green') . ";'>" . ($error_count > 0 ? '❌' : '✅') . " Failed statements: $error_count</li>\n";
    echo "</ul>\n";
    
    // Verify key tables exist
    echo "<h3>Verification:</h3>\n";
    $key_tables = ['users', 'faculties', 'departments', 'programs', 'notices', 'notice_audiences', 'audience_types', 'categories'];
    
    echo "<ul>\n";
    foreach ($key_tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<li style='color: green;'>✅ Table '$table' exists</li>\n";
        } else {
            echo "<li style='color: red;'>❌ Table '$table' missing</li>\n";
        }
    }
    echo "</ul>\n";
    
    // Check audience types
    echo "<h3>Audience Types Check:</h3>\n";
    $result = $conn->query("SELECT name, slug FROM audience_types ORDER BY id");
    if ($result && $result->num_rows > 0) {
        echo "<ul>\n";
        while ($row = $result->fetch_assoc()) {
            echo "<li>✅ {$row['name']} ({$row['slug']})</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: red;'>❌ No audience types found</p>\n";
    }
    
    // Check categories
    echo "<h3>Categories Check:</h3>\n";
    $result = $conn->query("SELECT name, icon FROM categories ORDER BY id");
    if ($result && $result->num_rows > 0) {
        echo "<ul>\n";
        while ($row = $result->fetch_assoc()) {
            echo "<li>✅ {$row['name']} (icon: {$row['icon']})</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: red;'>❌ No categories found</p>\n";
    }
    
    // Check sample data
    echo "<h3>Sample Data Check:</h3>\n";
    
    $tables_to_check = [
        'faculties' => 'SELECT COUNT(*) as count FROM faculties',
        'departments' => 'SELECT COUNT(*) as count FROM departments', 
        'programs' => 'SELECT COUNT(*) as count FROM programs',
        'users' => 'SELECT COUNT(*) as count FROM users'
    ];
    
    foreach ($tables_to_check as $table => $query) {
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $count = $row['count'];
            echo "<li>📊 $table: $count records</li>\n";
        }
    }
    
    closeDBConnection($conn);
    
    if ($error_count === 0) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
        echo "<h3>🎉 Database Setup Complete!</h3>\n";
        echo "<p>Your database is now ready for the audience filtering system.</p>\n";
        echo "<p><strong>Next steps:</strong></p>\n";
        echo "<ul>\n";
        echo "<li>Visit <a href='check_database_structure.php'>Database Structure Checker</a> to verify everything is working</li>\n";
        echo "<li>Visit <a href='test_audience_fix.php'>Audience Fix Tester</a> to test the filtering</li>\n";
        echo "<li>Go to <a href='index.php'>Dashboard</a> to start using the system</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
        echo "<h3>⚠️ Setup Completed with Errors</h3>\n";
        echo "<p>Some statements failed. Please review the errors above and fix any issues.</p>\n";
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
    echo "<h3>❌ Setup Failed</h3>\n";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}

echo "<p><a href='index.php'>← Back to Dashboard</a></p>\n";
?>