<?php
/**
 * Login Page for MUST Digital Notice Board
 */
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';

$error_message = '';
$success_message = '';

// Handle registration disabled message
if (isset($_GET['message']) && $_GET['message'] === 'registration_disabled') {
    $error_message = 'Registration is not available. Please contact the administrator for account creation.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        try {
            $conn = getDBConnection();
            
            // Get user details with faculty/program information
            $stmt = $conn->prepare("
                SELECT u.id, u.username, u.email, u.password_hash, u.first_name, u.last_name, 
                       u.role, u.faculty_id, u.department_id, u.program_id, u.year_of_study, u.is_active,
                       f.name as faculty_name, d.name as department_name, p.name as program_name
                FROM users u
                LEFT JOIN faculties f ON u.faculty_id = f.id
                LEFT JOIN departments d ON u.department_id = d.id
                LEFT JOIN programs p ON u.program_id = p.id
                WHERE u.username = ? AND u.is_active = 1
            ");
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password (plain text comparison)
                if ($password === $user['password_hash']) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['faculty_name'] = $user['faculty_name'] ?? 'N/A';
                    $_SESSION['department_name'] = $user['department_name'] ?? 'N/A';
                    $_SESSION['program_name'] = $user['program_name'] ?? 'N/A';
                    $_SESSION['year_of_study'] = $user['year_of_study'] ?? 'N/A';
                    
                    // Log the login activity
                    logActivity($conn, $user['id'], 'User logged in', 'auth', null);
                    
                    $stmt->close();
                    closeDBConnection($conn);
                    
                    // Redirect to dashboard
                    header("Location: ../index.php");
                    exit();
                } else {
                    $error_message = 'Invalid username or password.';
                }
            } else {
                $error_message = 'Invalid username or password.';
            }
            
            $stmt->close();
            closeDBConnection($conn);
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error_message = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MUST Digital Notice Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: url('../assets/images/FCI.jpeg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 500px;
        }

        .login-left {
            background: linear-gradient(135deg, green 0%, #006400 100%);
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }

        .university-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            overflow: hidden;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .university-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .login-left h1 {
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 700;
            line-height: 1.2;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .login-right {
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: green;
            background: white;
            box-shadow: 0 0 0 3px rgba(0,128,0,0.1);
        }

        .form-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 18px;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, green 0%, #006400 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,128,0,0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
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

        .login-links {
            text-align: center;
            margin-top: 20px;
        }

        .login-links a {
            color: green;
            text-decoration: none;
            font-weight: 500;
            margin: 0 10px;
        }

        .login-links a:hover {
            text-decoration: underline;
        }

        .demo-accounts {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .demo-accounts h4 {
            color: #0c5460;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .demo-accounts p {
            font-size: 12px;
            color: #0c5460;
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 400px;
            }

            .login-left {
                padding: 40px 20px;
            }

            .login-right {
                padding: 40px 20px;
            }

            .login-header h2 {
                font-size: 24px;
            }

            .login-left h1 {
                font-size: 22px;
            }
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }

        .loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="university-logo">
                <img src="../assets/images/download.png" alt="MUST Logo" onerror="this.style.display='none'">
            </div>
            <h1>MBARARA UNIVERSITY OF SCIENCE AND TECHNOLOGY NOTICE BOARD</h1>
            <p>Stay connected with campus announcements, academic notices, and important updates from your university.</p>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to access your dashboard</p>
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

            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="Enter your username">
                    <i class="fas fa-user"></i>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                    <i class="fas fa-eye" id="togglePassword" style="cursor: pointer; right: 15px; top: 50%; transform: translateY(-50%); position: absolute; color: #666; font-size: 18px;" onclick="togglePasswordVisibility()"></i>
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>

                <div class="loading" id="loading">
                    <i class="fas fa-spinner"></i> Signing in...
                </div>
            </form>

            <div class="login-links">
                <span style="color: #666; font-size: 14px;"><i class="fas fa-info-circle"></i> Need an account? Contact system administrator.</span>
            </div>


        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('loginBtn').style.display = 'none';
            document.getElementById('loading').style.display = 'block';
        });

        // Auto-focus on username field
        document.getElementById('username').focus();

        // Enter key handling
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });

        // Password visibility toggle
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>