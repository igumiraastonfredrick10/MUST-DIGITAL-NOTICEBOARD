<?php
/**
 * Quick Fix for Audience System Tables
 * This script will add missing tables/columns needed for audience filtering
 */

require_once 'config/database.php';

echo "<h2>Quick Fix for Audience System</h2>\n";

try {
    $conn = getDBConnection();
    
    $fixes_applied = [];
    $errors = [];
    
    // Check and create audience_types table
    $result = $conn->query("SHOW TABLES LIKE 'audience_types'");
    if (!$result || $result->num_rows === 0) {
        $sql = "CREATE TABLE audience_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            slug VARCHAR(50) UNIQUE NOT NULL,
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_slug (slug)
        )";
        
        if ($conn->query($sql)) {
            $fixes_applied[] = "Created audience_types table";
            
            // Insert default audience types
            $insert_sql = "INSERT INTO audience_types (name, slug, description) VALUES
                ('All Users', 'all', 'Visible to all registered users'),
                ('Students Only', 'students', 'Visible only to students'),
                ('Lecturers Only', 'lecturers', 'Visible only to lecturers'),
                ('Specific Faculty', 'faculty', 'Visible to specific faculty, departments, programs, or years')";
            
            if ($conn->query($insert_sql)) {
                $fixes_applied[] = "Inserted default audience types";
            }
        } else {
            $errors[] = "Failed to create audience_types table: " . $conn->error;
        }
    }
    
    // Check and create notice_audiences table
    $result = $conn->query("SHOW TABLES LIKE 'notice_audiences'");
    if (!$result || $result->num_rows === 0) {
        $sql = "CREATE TABLE notice_audiences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            notice_id INT NOT NULL,
            audience_type_id INT NOT NULL,
            faculty_id INT NULL,
            department_id INT NULL,
            program_id INT NULL,
            year_level INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (notice_id) REFERENCES notices(id) ON DELETE CASCADE,
            FOREIGN KEY (audience_type_id) REFERENCES audience_types(id) ON DELETE CASCADE,
            INDEX idx_notice (notice_id),
            INDEX idx_audience_type (audience_type_id),
            INDEX idx_faculty (faculty_id),
            INDEX idx_program (program_id),
            INDEX idx_year (year_level)
        )";
        
        if ($conn->query($sql)) {
            $fixes_applied[] = "Created notice_audiences table";
        } else {
            $errors[] = "Failed to create notice_audiences table: " . $conn->error;
        }
    }
    
    // Check and create faculties table if missing
    $result = $conn->query("SHOW TABLES LIKE 'faculties'");
    if (!$result || $result->num_rows === 0) {
        $sql = "CREATE TABLE faculties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(10) UNIQUE NOT NULL,
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        )";
        
        if ($conn->query($sql)) {
            $fixes_applied[] = "Created faculties table";
            
            // Insert sample faculties
            $insert_sql = "INSERT INTO faculties (name, code, description) VALUES
                ('Faculty of Computing and Informatics', 'FCI', 'Computer Science and IT programs'),
                ('Faculty of Medicine', 'FOM', 'Medical and health sciences'),
                ('Faculty of Business and Management Sciences', 'FBMS', 'Business and management programs'),
                ('Faculty of Science', 'FOS', 'Natural sciences and mathematics'),
                ('Faculty of Education', 'FOE', 'Education and teaching programs')";
            
            if ($conn->query($insert_sql)) {
                $fixes_applied[] = "Inserted sample faculties";
            }
        } else {
            $errors[] = "Failed to create faculties table: " . $conn->error;
        }
    }
    
    // Check and create departments table if missing
    $result = $conn->query("SHOW TABLES LIKE 'departments'");
    if (!$result || $result->num_rows === 0) {
        $sql = "CREATE TABLE departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(10) NOT NULL,
            faculty_id INT NOT NULL,
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
            INDEX idx_name (name),
            INDEX idx_faculty (faculty_id)
        )";
        
        if ($conn->query($sql)) {
            $fixes_applied[] = "Created departments table";
            
            // Insert sample departments for FCI
            $insert_sql = "INSERT INTO departments (name, code, faculty_id, description) VALUES
                ('Computer Science', 'CS', 1, 'Computer Science Department'),
                ('Information Technology', 'IT', 1, 'Information Technology Department'),
                ('Software Engineering', 'SE', 1, 'Software Engineering Department')";
            
            if ($conn->query($insert_sql)) {
                $fixes_applied[] = "Inserted sample departments";
            }
        } else {
            $errors[] = "Failed to create departments table: " . $conn->error;
        }
    }
    
    // Check and create programs table if missing
    $result = $conn->query("SHOW TABLES LIKE 'programs'");
    if (!$result || $result->num_rows === 0) {
        $sql = "CREATE TABLE programs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20) NOT NULL,
            department_id INT NOT NULL,
            faculty_id INT NOT NULL,
            duration_years INT NOT NULL DEFAULT 3,
            degree_type ENUM('certificate', 'diploma', 'bachelor', 'master', 'phd') NOT NULL DEFAULT 'bachelor',
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
            FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
            INDEX idx_name (name),
            INDEX idx_department (department_id)
        )";
        
        if ($conn->query($sql)) {
            $fixes_applied[] = "Created programs table";
            
            // Insert sample programs
            $insert_sql = "INSERT INTO programs (name, code, department_id, faculty_id, duration_years, degree_type) VALUES
                ('Bachelor of Computer Science', 'BCS', 1, 1, 3, 'bachelor'),
                ('Bachelor of Information Technology', 'BIT', 2, 1, 3, 'bachelor'),
                ('Bachelor of Software Engineering', 'BSE', 3, 1, 4, 'bachelor'),
                ('Master of Computer Science', 'MCS', 1, 1, 2, 'master')";
            
            if ($conn->query($insert_sql)) {
                $fixes_applied[] = "Inserted sample programs";
            }
        } else {
            $errors[] = "Failed to create programs table: " . $conn->error;
        }
    }
    
    // Check if users table has the required columns
    $result = $conn->query("DESCRIBE users");
    $user_columns = [];
    while ($row = $result->fetch_assoc()) {
        $user_columns[] = $row['Field'];
    }
    
    $required_columns = ['faculty_id', 'department_id', 'program_id', 'year_of_study'];
    foreach ($required_columns as $column) {
        if (!in_array($column, $user_columns)) {
            $sql = "ALTER TABLE users ADD COLUMN $column INT NULL";
            if ($conn->query($sql)) {
                $fixes_applied[] = "Added $column column to users table";
            } else {
                $errors[] = "Failed to add $column column: " . $conn->error;
            }
        }
    }
    
    // Update existing posts to have proper audience entries
    $result = $conn->query("SELECT COUNT(*) as count FROM notice_audiences");
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            // No audience entries exist, create default ones for existing posts
            $posts_result = $conn->query("SELECT id FROM notices WHERE status = 'published'");
            if ($posts_result && $posts_result->num_rows > 0) {
                $all_audience_id = 1; // Assuming 'all' is ID 1
                
                while ($post = $posts_result->fetch_assoc()) {
                    $insert_sql = "INSERT INTO notice_audiences (notice_id, audience_type_id) VALUES ({$post['id']}, $all_audience_id)";
                    $conn->query($insert_sql);
                }
                
                $fixes_applied[] = "Added default audience entries for existing posts";
            }
        }
    }
    
    closeDBConnection($conn);
    
    // Display results
    echo "<h3>Fixes Applied:</h3>\n";
    if (count($fixes_applied) > 0) {
        echo "<ul style='color: green;'>\n";
        foreach ($fixes_applied as $fix) {
            echo "<li>✅ $fix</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: blue;'>ℹ️ No fixes needed - all tables and columns already exist.</p>\n";
    }
    
    if (count($errors) > 0) {
        echo "<h3>Errors:</h3>\n";
        echo "<ul style='color: red;'>\n";
        foreach ($errors as $error) {
            echo "<li>❌ $error</li>\n";
        }
        echo "</ul>\n";
    }
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
    echo "<h3>✅ Quick Fix Complete!</h3>\n";
    echo "<p>The audience system tables have been checked and updated.</p>\n";
    echo "<p><strong>Next steps:</strong></p>\n";
    echo "<ul>\n";
    echo "<li><a href='check_database_structure.php'>Check Database Structure</a></li>\n";
    echo "<li><a href='test_audience_fix.php'>Test Audience Filtering</a></li>\n";
    echo "<li><a href='index.php'>Go to Dashboard</a></li>\n";
    echo "</ul>\n";
    echo "</div>\n";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>\n";
    echo "<h3>❌ Error</h3>\n";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}

echo "<p><a href='index.php'>← Back to Dashboard</a></p>\n";
?>