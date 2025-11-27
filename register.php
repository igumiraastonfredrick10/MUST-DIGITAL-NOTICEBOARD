<?php
/**
 * Registration Page - DISABLED
 * Registration is not available for this system
 */
session_start();

// Registration is disabled - redirect to login
header("Location: login.php?message=registration_disabled");
exit();

require_once '../config/database.php';

$error_message = '';
$success_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $faculty_id = $_POST['faculty_id'] ?? null;
    $department_id = $_POST['department_id'] ?? null;
    $program_id = $_POST['program_id'] ?? null;
    $year_of_study = $_POST['year_of_study'] ?? null;
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
        $error_message = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif ($role === 'student' && (empty($faculty_id) || empty($program_id) || empty($year_of_study))) {
        $error_message = 'Students must select faculty, program, and year of study.';
    } else {
        try {
            $conn = getDBConnection();
            
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
         