<?php
/**
 * Get Programs from Database
 * Returns all programs with department and faculty info as JSON
 */

require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

$sql = "SELECT p.id, p.name, p.duration_years, p.description, 
        d.name as department_name, d.id as department_id,
        f.name as faculty_name, f.id as faculty_id
        FROM programs p
        LEFT JOIN departments d ON p.department_id = d.id
        LEFT JOIN faculties f ON d.faculty_id = f.id
        ORDER BY p.name ASC";
$result = $conn->query($sql);

$programs = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $programs[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'duration_years' => $row['duration_years'],
            'description' => $row['description'],
            'department_id' => $row['department_id'],
            'department_name' => $row['department_name'],
            'faculty_id' => $row['faculty_id'],
            'faculty_name' => $row['faculty_name']
        );
    }
}

closeDBConnection($conn);

echo json_encode(array(
    'success' => true,
    'programs' => $programs,
    'count' => count($programs)
));
?>