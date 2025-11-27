<?php
/**
 * Get Faculties from Database
 * Returns all faculties as JSON
 */

require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

$sql = "SELECT id, name, description, dean FROM faculties ORDER BY name ASC";
$result = $conn->query($sql);

$faculties = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faculties[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'dean' => $row['dean']
        );
    }
}

closeDBConnection($conn);

echo json_encode(array(
    'success' => true,
    'faculties' => $faculties,
    'count' => count($faculties)
));
?>