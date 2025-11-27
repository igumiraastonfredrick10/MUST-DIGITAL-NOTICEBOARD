<?php
/**
 * Get Cascading Data for Faculty → Department → Program → Year
 * This API supports hierarchical selection
 */

require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');

$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;

if (empty($type)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Type parameter is required',
        'data' => array()
    ));
    exit();
}

$conn = getDBConnection();
$data = array();

switch ($type) {
    case 'faculties':
        // Get all faculties
        $sql = "SELECT id, name FROM faculties WHERE 1=1 ORDER BY name ASC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'id' => (int)$row['id'],
                    'name' => $row['name']
                );
            }
        }
        break;
        
    case 'departments':
        // Get departments for a specific faculty
        if ($parent_id > 0) {
            $stmt = $conn->prepare("SELECT id, name, faculty_id FROM departments WHERE faculty_id = ? ORDER BY name ASC");
            $stmt->bind_param("i", $parent_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = array(
                        'id' => (int)$row['id'],
                        'name' => $row['name'],
                        'faculty_id' => (int)$row['faculty_id']
                    );
                }
            }
            $stmt->close();
        } else {
            // Get all departments with faculty info
            $sql = "SELECT d.id, d.name, d.faculty_id, f.name as faculty_name 
                    FROM departments d 
                    LEFT JOIN faculties f ON d.faculty_id = f.id 
                    ORDER BY d.name ASC";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = array(
                        'id' => (int)$row['id'],
                        'name' => $row['name'],
                        'faculty_id' => (int)$row['faculty_id'],
                        'faculty_name' => $row['faculty_name']
                    );
                }
            }
        }
        break;
        
    case 'programs':
        // Get programs for a specific department
        if ($parent_id > 0) {
            $stmt = $conn->prepare("SELECT p.id, p.name, p.duration_years, p.department_id 
                                   FROM programs p 
                                   WHERE p.department_id = ? 
                                   ORDER BY p.name ASC");
            $stmt->bind_param("i", $parent_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = array(
                        'id' => (int)$row['id'],
                        'name' => $row['name'],
                        'duration_years' => (int)$row['duration_years'],
                        'department_id' => (int)$row['department_id']
                    );
                }
            }
            $stmt->close();
        } else {
            // Get all programs with department and faculty info
            $sql = "SELECT p.id, p.name, p.duration_years, p.department_id,
                    d.name as department_name, d.faculty_id,
                    f.name as faculty_name
                    FROM programs p
                    LEFT JOIN departments d ON p.department_id = d.id
                    LEFT JOIN faculties f ON d.faculty_id = f.id
                    ORDER BY p.name ASC";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = array(
                        'id' => (int)$row['id'],
                        'name' => $row['name'],
                        'duration_years' => (int)$row['duration_years'],
                        'department_id' => (int)$row['department_id'],
                        'department_name' => $row['department_name'],
                        'faculty_id' => (int)$row['faculty_id'],
                        'faculty_name' => $row['faculty_name']
                    );
                }
            }
        }
        break;
        
    case 'years':
        // Get years for a specific program
        if ($parent_id > 0) {
            $stmt = $conn->prepare("SELECT duration_years FROM programs WHERE id = ?");
            $stmt->bind_param("i", $parent_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $duration = (int)$row['duration_years'];
                
                for ($i = 1; $i <= $duration; $i++) {
                    $data[] = array(
                        'id' => $i,
                        'name' => "Year $i",
                        'program_id' => $parent_id
                    );
                }
            }
            $stmt->close();
        } else {
            // Return generic years (1-5)
            for ($i = 1; $i <= 5; $i++) {
                $data[] = array(
                    'id' => $i,
                    'name' => "Year $i"
                );
            }
        }
        break;
        
    default:
        closeDBConnection($conn);
        echo json_encode(array(
            'success' => false,
            'message' => 'Invalid type parameter: ' . $type,
            'data' => array()
        ));
        exit();
}

closeDBConnection($conn);

echo json_encode(array(
    'success' => true,
    'data' => $data,
    'count' => count($data)
));
?>