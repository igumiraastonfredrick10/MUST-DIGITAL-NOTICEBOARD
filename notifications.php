<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - MUST Notice Board</title>
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

        .notification-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .notification-item.unread {
            border-left: 4px solid green;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: green;
            font-size: 20px;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .notification-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .notification-time {
            font-size: 12px;
            color: #999;
        }

        .no-notifications {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1><i class="fas fa-bell"></i> Notifications</h1>
                <p>Stay updated with the latest activities</p>
            </div>
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <div class="notifications-list">
            <div class="notification-item unread">
                <div class="notification-icon">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Someone liked your post</div>
                    <div class="notification-text">John Doe liked "Welcome to the new academic year"</div>
                    <div class="notification-time">2 hours ago</div>
                </div>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-comment"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">New comment on your post</div>
                    <div class="notification-text">Mary Smith commented on "Registration Deadline Extended"</div>
                    <div class="notification-time">5 hours ago</div>
                </div>
            </div>

            <!-- Add more notifications as needed -->
        </div>
    </div>
</body>
</html>