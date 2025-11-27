<?php
/**
 * Database Structure Checker
 * This script will show the current database structure and identify any issues
 */

require_once 'config/database.php';

try {
    $conn = getDBConnection();
    echo "<h2>Database Structure Analysis</h2>\n";
    echo "<p>Database: " . DB_NAME . "</p>\n";
    
    // Get all tables
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    
    echo "<h3>Tables in Database:</h3>\n<ul>\n";
    while ($row = $result->fetch_array()) {
        $table = $row[0];
        $tables[] = $table;
        echo "<li>$table</li>\n";
    }
    echo "</ul>\n";
    
    // Check key tables for audience system
    $key_tables = ['notices', 'notice_audiences', 'audience_types', 'faculties', 'departments', 'programs', 'users'];
    
    echo "<h3>Key Tables Analysis:</h3>\n";
    
    foreach ($key_tables as $table) {
        echo "<h4>Table: $table</h4>\n";
        
        if (in_array($table, $tables)) {
            echo "<p style='color: green;'>✅ Table exists</p>\n";
            
            // Show table structure
            $result = $conn->query("DESCRIBE $table");
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "<td>{$row['Extra']}</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
            
            // Show sample data for some tables
            if (in_array($table, ['audience_types', 'faculties', 'departments', 'programs'])) {
                $result = $conn->query("SELECT * FROM $table LIMIT 5");
                if ($result && $result->num_rows > 0) {
                    echo "<p><strong>Sample Data:</strong></p>\n";
                    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
                    
                    // Header
                    $fields = $result->fetch_fields();
                    echo "<tr>";
                    foreach ($fields as $field) {
                        echo "<th>{$field->name}</th>";
                    }
                    echo "</tr>\n";
                    
                    // Data
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
                        }
                        echo "</tr>\n";
                    }
                    echo "</table>\n";
                } else {
                    echo "<p style='color: orange;'>⚠️ Table is empty</p>\n";
                }
            }
            
        } else {
            echo "<p style='color: red;'>❌ Table missing</p>\n";
        }
        echo "<hr>\n";
    }
    
    // Check for posts with faculty audience
    if (in_array('notices', $tables) && in_array('notice_audiences', $tables)) {
        echo "<h3>Faculty Audience Posts Analysis:</h3>\n";
        
        $result = $conn->query("
            SELECT n.id, n.title, at.slug as audience_type, 
                   na.faculty_id, na.department_id, na.program_id, na.year_level
            FROM notices n 
            JOIN notice_audiences na ON n.id = na.notice_id 
            JOIN audience_types at ON na.audience_type_id = at.id 
            WHERE at.slug = 'faculty'
            LIMIT 10
        ");
        
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>Post ID</th><th>Title</th><th>Audience Type</th><th>Faculty ID</th><th>Dept ID</th><th>Program ID</th><th>Year</th></tr>\n";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>{$row['audience_type']}</td>";
                echo "<td>{$row['faculty_id']}</td>";
                echo "<td>{$row['department_id']}</td>";
                echo "<td>{$row['program_id']}</td>";
                echo "<td>{$row['year_level']}</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
        } else {
            echo "<p style='color: orange;'>⚠️ No faculty audience posts found</p>\n";
        }
    }
    
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
}

echo "<p><a href='index.php'>← Back to Dashboard</a></p>\n";
?>