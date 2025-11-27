<?php
ob_start();
require_once '../config/database.php';
require_once '../config/config.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    error_log("=== GET CATEGORIES START ===");
    
    $conn = getDBConnection();
    error_log("Database connection successful");
    
    $sql = "SELECT id, name, slug, icon, description FROM categories WHERE is_active = 1 ORDER BY name ASC";
    error_log("Executing query: " . $sql);
    
    $result = $conn->query($sql);
    error_log("Query executed. Rows: " . ($result ? $result->num_rows : 'NULL'));
    
    $categories = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = array(
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'slug' => $row['slug'],
                'icon' => $row['icon'],
                'description' => $row['description']
            );
        }
    }
    
    closeDBConnection($conn);
    
    error_log("Categories loaded: " . count($categories));
    error_log("=== GET CATEGORIES END ===");
    
    echo json_encode(array(
        'success' => true, 
        'categories' => $categories, 
        'count' => count($categories)
    ));
    
} catch (Exception $e) {
    error_log("Get categories error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode(array(
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(), 
        'categories' => array(), 
        'count' => 0
    ));
}
exit();