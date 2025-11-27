<?php
/**
 * Admin User Creation Page
 * Only accessible by administrators
 */
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$error_message = '';
$success_message = '';

// Handle user creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    $faculty_id = $_POST['faculty_id'] ?? null;
    $department_id = $_POST['department_id'] ?? null;
    $program_id = $_POST['program_id'] ?? null;
    $year_of_study = $_POST['year_of_study'] ?? null;
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $conn = getDBConnection();
            
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = 'Username or email already exists.';
            } else {
                // Store password as plain text (not recommended for production)
                $password_hash = $password;
                
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, role, faculty_id, department_id, program_id, year_of_study, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->bind_param("ssssssiiis", $first_name, $last_name, $username, $email, $password_hash, $role, $faculty_id, $department_id, $program_id, $year_of_study);
                
                if ($stmt->execute()) {
                    $success_message = "User '$username' created successfully!";
                    // Clear form
                    $first_name = $last_name = $username = $email = $password = $role = '';
                    $faculty_id = $department_id = $program_id = $year_of_study = null;
                } else {
                    $error_message = 'Error creating user: ' . $stmt->error;
                }
            }
            
            $stmt->close();
            closeDBConnection($conn);
            
        } catch (Exception $e) {
            error_log("User creation error: " . $e->getMessage());
            $error_message = 'Error creating user. Please try again.';
        }
    }
}

// Get faculties for dropdown
$faculties = [];
$programs = [];
try {
    $conn = getDBConnection();
    
    $result = $conn->query("SELECT id, name FROM faculties WHERE is_active = 1 ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        $faculties[] = $row;
    }
    
    $result = $conn->query("SELECT id, name, faculty_id FROM programs WHERE is_active = 1 ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
    
    closeDBConnection($conn);
} catch (Exception $e) {
    // Handle error silently
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f9fa;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, green, #006400);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .content {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: green;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: green;
            color: white;
        }

        .btn-primary:hover {
            background: #006400;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-right: 10px;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .student-fields {
            display: none;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Create New User</h1>
            <p>Admin Panel - User Management</p>
        </div>

        <div class="content">
            <div style="margin-bottom: 20px;">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Admin Panel
                </a>
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?php echo htmlspecialchars($first_name ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?php echo htmlspecialchars($last_name ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required onchange="toggleStudentFields()">
                            <option value="">Select Role</option>
                            <option value="admin" <?php echo ($role ?? '') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                            <option value="lecturer" <?php echo ($role ?? '') === 'lecturer' ? 'selected' : ''; ?>>Lecturer</option>
                            <option value="student" <?php echo ($role ?? '') === 'student' ? 'selected' : ''; ?>>Student</option>
                        </select>
                    </div>
                </div>

                <div id="studentFields" class="student-fields">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">Student Information</h3>
                    
                    <div class="form-group">
                        <label for="faculty_id">Faculty</label>
                        <select id="faculty_id" name="faculty_id">
                            <option value="">Select Faculty</option>
                            <?php foreach ($faculties as $faculty): ?>
                                <option value="<?php echo $faculty['id']; ?>" 
                                        <?php echo ($faculty_id ?? '') == $faculty['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($faculty['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="program_id">Program</label>
                            <select id="program_id" name="program_id">
                                <option value="">Select Program</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?php echo $program['id']; ?>" 
                                            data-faculty="<?php echo $program['faculty_id']; ?>"
                                            <?php echo ($program_id ?? '') == $program['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($program['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year_of_study">Year of Study</label>
                            <select id="year_of_study" name="year_of_study">
                                <option value="">Select Year</option>
                                <option value="1" <?php echo ($year_of_study ?? '') === '1' ? 'selected' : ''; ?>>Year 1</option>
                                <option value="2" <?php echo ($year_of_study ?? '') === '2' ? 'selected' : ''; ?>>Year 2</option>
                                <option value="3" <?php echo ($year_of_study ?? '') === '3' ? 'selected' : ''; ?>>Year 3</option>
                                <option value="4" <?php echo ($year_of_study ?? '') === '4' ? 'selected' : ''; ?>>Year 4</option>
                                <option value="5" <?php echo ($year_of_study ?? '') === '5' ? 'selected' : ''; ?>>Year 5</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleStudentFields() {
            const role = document.getElementById('role').value;
            const studentFields = document.getElementById('studentFields');
            
            if (role === 'student') {
                studentFields.style.display = 'block';
            } else {
                studentFields.style.display = 'none';
            }
        }

        // Initialize on page load
        toggleStudentFields();
    </script>
</body>
</html>