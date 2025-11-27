<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

$conn = getDBConnection();

// Get current user data
$stmt = $conn->prepare("SELECT u.*, f.name as faculty_name, d.name as department_name, r.name as role_name
                        FROM users u
                        LEFT JOIN faculties f ON u.faculty_id = f.id
                        LEFT JOIN departments d ON u.department_id = d.id
                        LEFT JOIN roles r ON u.role_id = r.id
                        WHERE u.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        
        $updateStmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
        $updateStmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
        
        if ($updateStmt->execute()) {
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $_SESSION['user_email'] = $email;
            $message = 'Profile updated successfully!';
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['email'] = $email;
        } else {
            $error = 'Error updating profile';
        }
        $updateStmt->close();
    }
    
    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['profile_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $upload_dir = 'uploads/profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])) {
                    unlink($user['profile_photo']);
                }
                
                $photoStmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                $photoStmt->bind_param("si", $upload_path, $user_id);
                $photoStmt->execute();
                $photoStmt->close();
                
                $user['profile_photo'] = $upload_path;
                $message = 'Profile photo updated successfully!';
            }
        } else {
            $error = 'Invalid file type. Only JPG, PNG and GIF allowed.';
        }
    }
    
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            // Check current password
            $checkStmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
            $checkStmt->bind_param("i", $user_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $userData = $checkResult->fetch_assoc();
            $checkStmt->close();
            
            // Support both hashed and plain text passwords
            $password_valid = false;
            if (substr($userData['password_hash'], 0, 4) === '$2y$') {
                $password_valid = password_verify($current_password, $userData['password_hash']);
            } else {
                $password_valid = ($current_password === $userData['password_hash']);
            }
            
            if ($password_valid) {
                // You can choose to hash or not hash the new password
                $new_hash = $new_password; // Plain text
                // OR use: $new_hash = password_hash($new_password, PASSWORD_DEFAULT); // Hashed
                
                $passStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $passStmt->bind_param("si", $new_hash, $user_id);
                
                if ($passStmt->execute()) {
                    $message = 'Password changed successfully!';
                } else {
                    $error = 'Error changing password';
                }
                $passStmt->close();
            } else {
                $error = 'Current password is incorrect';
            }
        }
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - MUST Notice Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
           
                  background: url('assets/images/FCI.jpeg') no-repeat center center fixed;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, green, #006400);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .profile-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .profile-photo-section {
            text-align: center;
            padding: 30px;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid green;
            margin-bottom: 20px;
        }

        .placeholder-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: green;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 20px;
            margin-bottom: 20px;
            color: #2c3e50;
            border-bottom: 2px solid green;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: green;
        }

        .btn {
            background: green;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #006400;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1><i class="fas fa-user-cog"></i> Profile Settings</h1>
                <p>Manage your account information</p>
            </div>
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Profile Photo Section -->
        <div class="profile-section">
            <div class="profile-photo-section">
                <?php if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile" class="profile-photo">
                <?php else: ?>
                    <div class="placeholder-photo">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_photo" accept="image/*" id="photoInput" style="display: none;">
                    <button type="button" class="btn" onclick="document.getElementById('photoInput').click();">
                        <i class="fas fa-camera"></i> Change Photo
                    </button>
                    <button type="submit" class="btn" style="background: #0c3b84;" id="uploadBtn" style="display: none;">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </form>
            </div>

            <h2 class="section-title">Basic Information</h2>
            <form method="POST">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <button type="submit" name="update_profile" class="btn">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>

        <!-- Account Information -->
        <div class="profile-section">
            <h2 class="section-title">Account Information</h2>
            <div class="info-item">
                <div class="info-label">Username</div>
                <div><?php echo htmlspecialchars($user['username']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Role</div>
                <div><?php echo htmlspecialchars($user['role_name']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Faculty</div>
                <div><?php echo htmlspecialchars($user['faculty_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Department</div>
                <div><?php echo htmlspecialchars($user['department_name'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="profile-section">
            <h2 class="section-title">Change Password</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" name="change_password" class="btn">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('photoInput').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('uploadBtn').style.display = 'inline-block';
                this.form.submit();
            }
        });
    </script>
</body>
</html>