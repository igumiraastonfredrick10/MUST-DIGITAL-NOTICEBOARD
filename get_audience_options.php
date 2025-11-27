<?php
/**
 * Get Dynamic Audience Options
 * Based on audience type (faculty, department, program, year)
 */

require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');

$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';

if (empty($type)) {
    sendJSON(false, 'Audience type is required');
}

$conn = getDBConnection();
$options = array();

switch ($type) {
    case 'faculty':
        $sql = "SELECT id, name FROM faculties ORDER BY name ASC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $options[] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                );
            }
        }
        break;
        
    case 'department':
        $sql = "SELECT d.id, d.name, f.name as faculty_name 
                FROM departments d 
                LEFT JOIN faculties f ON d.faculty_id = f.id 
                ORDER BY d.name ASC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $options[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'faculty_name' => $row['faculty_name']
                );
            }
        }
        break;
        
    case 'program':
        $sql = "SELECT p.id, p.name, d.name as department_name, f.name as faculty_name 
                FROM programs p 
                LEFT JOIN departments d ON p.department_id = d.id 
                LEFT JOIN faculties f ON d.faculty_id = f.id 
                ORDER BY p.name ASC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $options[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'department_name' => $row['department_name'],
                    'faculty_name' => $row['faculty_name']
                );
            }
        }
        break;
        
    case 'year':
        $options = array(
            array('id' => 1, 'name' => 'Year 1'),
            array('id' => 2, 'name' => 'Year 2'),
            array('id' => 3, 'name' => 'Year 3'),
            array('id' => 4, 'name' => 'Year 4'),
            array('id' => 5, 'name' => 'Year 5')
        );
        closeDBConnection($conn);
        sendJSON(true, '', array('options' => $options));
        break;
        
    default:
        closeDBConnection($conn);
        sendJSON(false, 'Invalid audience type');
}

closeDBConnection($conn);

sendJSON(true, '', array('options' => $options));
?>