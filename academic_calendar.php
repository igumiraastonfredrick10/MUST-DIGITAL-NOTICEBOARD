<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$faculty_name = isset($_SESSION['faculty_name']) ? $_SESSION['faculty_name'] : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Calendar - MUST Digital Notice Board</title>
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
            background-size: cover;
            color: #2c3e50;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, green, rgb(4, 136, 4));
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            color: white;
        }

        .university-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .university-logo {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #ffcc00;
            overflow: hidden;
        }

        .university-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .university-text h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .university-text p {
            font-size: 14px;
            opacity: 0.9;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .calendar-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .calendar-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .calendar-header h2 {
            color: green;
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .calendar-header p {
            color: #666;
            font-size: 16px;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .calendar-table thead {
            background: linear-gradient(135deg, green, rgb(4, 136, 4));
            color: white;
        }

        .calendar-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 16px;
        }

        .calendar-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 15px;
        }

        .calendar-table tbody tr {
            transition: background 0.3s ease;
        }

        .calendar-table tbody tr:hover {
            background: #f8f9fa;
        }

        .activity-cell {
            color: #2c3e50;
            font-weight: 500;
        }

        .date-cell {
            color: #666;
        }

        .urgent-row {
            background: #fff3cd;
        }

        .urgent-row:hover {
            background: #ffe69c;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #0c3b84;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .calendar-table {
                font-size: 14px;
            }

            .calendar-table th,
            .calendar-table td {
                padding: 10px;
            }

            .calendar-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="university-info">
                <div class="university-logo">
                    <img src="assets/images/download.png" alt="MUST Logo">
                </div>
                <div class="university-text">
                    <h1>Mbarara University of Science & Technology</h1>
                    <p>Academic Calendar</p>
                </div>
            </div>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </header>

        <div class="calendar-card">
            <div class="calendar-header">
                <h2><i class="fas fa-calendar-alt"></i> Semester I - 2024/2025 Academic Year</h2>
                <p>Important dates and deadlines for the current semester</p>
            </div>

            <table class="calendar-table">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Dates</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="activity-cell">Reporting & Registration of First Years</td>
                        <td class="date-cell">12th August 2024 to 16th August 2024</td>
                    </tr>
                    <tr>
                        <td class="activity-cell">Lectures for All</td>
                        <td class="date-cell">19th August 2024</td>
                    </tr>
                    <tr>
                        <td class="activity-cell">Orientation for First Years</td>
                        <td class="date-cell">19th August 2024 to 23rd August 2024</td>
                    </tr>
                    <tr class="urgent-row">
                        <td class="activity-cell">Graduation Day</td>
                        <td class="date-cell">19th October 2024</td>
                    </tr>
                    <tr>
                        <td class="activity-cell">End of Semester I Examinations</td>
                        <td class="date-cell">2nd December 2024 to 13th December 2024</td>
                    </tr>
                    <tr>
                        <td class="activity-cell">Closure of Semester I</td>
                        <td class="date-cell">14th December 2024</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer>
            <p><strong>Mbarara University of Science & Technology</strong> • P.O. Box 1410, Mbarara, Uganda • © 2024</p>
            <p>Contact: info@must.ac.ug | Tel: +256 414 123 456</p>
        </footer>
    </div>
</body>
</html>