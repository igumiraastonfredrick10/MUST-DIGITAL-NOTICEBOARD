<?php
/**
 * Get Departments from Database
 * Returns all departments with faculty info as JSON
 */

require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

$sql = "SELECT d.id, d.name, d.description, d.head, f.name as faculty_name, f.id as faculty_id
        FROM departments d
        LEFT JOIN faculties f ON d.faculty_id = f.id
        ORDER BY d.name ASC";
$result = $conn->query($sql);

$departments = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'head' => $row['head'],
            'faculty_id' => $row['faculty_id'],
            'faculty_name' => $row['faculty_name']
        );
    }
}

closeDBConnection($conn);

echo json_encode(array(
    'success' => true,
    'departments' => $departments,
    'count' => count($departments)
));
?>