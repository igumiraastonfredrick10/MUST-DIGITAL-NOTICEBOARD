<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Get user data from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$faculty_name = isset($_SESSION['faculty_name']) ? $_SESSION['faculty_name'] : 'N/A';
$department_name = isset($_SESSION['department_name']) ? $_SESSION['department_name'] : 'N/A';
$program_name = isset($_SESSION['program_name']) ? $_SESSION['program_name'] : 'N/A';
$year_of_study = isset($_SESSION['year_of_study']) ? $_SESSION['year_of_study'] : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MUST Digital Notice Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            transition: margin-left 0.3s ease;
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

        .toggle-sidebar-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .toggle-sidebar-btn:hover {
            background: rgba(255,255,255,0.1);
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            top: 0;
            left: -280px;
            transition: left 0.3s ease;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 12px 0;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .sidebar-menu a:hover {
            background: #34495e;
            transform: translateX(5px);
        }

        .sidebar-menu i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .sidebar-user-info {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar-user-info .user-name {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .sidebar-user-info .user-role {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 3px;
        }

        .sidebar-user-info .user-faculty {
            font-size: 12px;
            opacity: 0.7;
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

        .date-display {
            text-align: right;
        }

        .current-date {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .current-time {
            font-size: 28px;
            font-weight: bold;
            color: #ffcc00;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        @media (max-width: 1200px) {
            .dashboard {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .card-header h2 {
            font-size: 20px;
            color: green;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header i {
            font-size: 22px;
            color: green;
        }

        .badge {
            background: #e74c3c;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .notice-item {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            gap: 15px;
            transition: background 0.2s;
        }

        .notice-item:hover {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 0 -15px;
        }

        .notice-item:last-child {
            border-bottom: none;
        }

        .notice-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e8f0fe;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notice-icon i {
            color: orangered;
            font-size: 18px;
        }

        .notice-content {
            flex: 1;
        }

        .notice-content h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: #2c3e50;
            cursor: pointer;
            transition: color 0.2s;
        }

        .notice-content h3:hover {
            color: #0c3b84;
            text-decoration: underline;
        }

        .notice-content p {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .notice-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }

        .notice-time {
            font-size: 12px;
            color: #888;
        }

        .notice-audience {
            background: #e8f0fe;
            color: #0c3b84;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .post-stats {
            display: flex;
            gap: 15px;
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }

        .post-stats span {
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .post-stats span:hover {
            background: #f0f0f0;
        }

        .post-media-preview {
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .post-media-preview img {
            max-width: 100px;
            max-height: 60px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .urgent {
            border-left: 4px solid #e74c3c;
            padding-left: 10px;
        }

        .event-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .event-item:hover {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 0 -15px;
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-date {
            width: 50px;
            height: 50px;
            background: orangered;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .event-day {
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
        }

        .event-month {
            font-size: 10px;
            text-transform: uppercase;
            line-height: 1;
        }

        .event-details {
            flex: 1;
        }

        .event-details h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: #2c3e50;
            cursor: pointer;
            transition: color 0.2s;
        }

        .event-details h3:hover {
            color: #0c3b84;
            text-decoration: underline;
        }

        .event-details p {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .event-meta {
            display: flex;
            gap: 10px;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }

        .event-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-top: 15px;
        }

        .calendar-header {
            grid-column: span 7;
            text-align: center;
            margin-bottom: 10px;
            font-weight: 600;
            color: green;
            font-size: 16px;
        }

        .calendar-day {
            text-align: center;
            padding: 8px 0;
            font-size: 14px;
            font-weight: 600;
            color: #666;
        }

        .calendar-date {
            text-align: center;
            padding: 8px 0;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 14px;
        }

        .calendar-date:hover {
            background: #e8f0fe;
        }

        .calendar-date.current {
            background: #0c3b84;
            color: white;
        }

        .post-notice-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }

        .post-notice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .post-notice-header h2 {
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: green;
        }

        .new-post-btn {
            background: linear-gradient(135deg, orangered, #ff6b35);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .new-post-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .post-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            border: 1px solid #e9ecef;
        }

        .post-form.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .post-form select, .post-form textarea, .post-form input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 12px;
            font-family: inherit;
            font-size: 14px;
            background: white;
            transition: border-color 0.3s;
        }

        .post-form select:focus, .post-form textarea:focus, .post-form input:focus {
            outline: none;
            border-color: green;
            box-shadow: 0 0 0 2px rgba(0,128,0,0.1);
        }

        .post-form textarea {
            resize: vertical;
            min-height: 120px;
        }

        .post-form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .file-upload {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            cursor: pointer;
            padding: 8px 12px;
            border: 1px dashed #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .file-upload:hover {
            background: #e9ecef;
            border-color: green;
        }

        #mediaPreview {
            margin-top: 10px;
        }

        #mediaPreview img, #mediaPreview video {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
        }

        #mediaPreview audio {
            width: 100%;
            margin-top: 10px;
        }

        .submit-post {
            background: green;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .submit-post:hover {
            background: #006400;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .submit-post:active {
            transform: translateY(0);
        }

        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 10px;
        }

        .quick-link-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .quick-link-item:hover {
            background: #e8f0fe;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-color: #0c3b84;
        }

        .quick-link-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, green, #006400);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .quick-link-content h3 {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .quick-link-content p {
            font-size: 12px;
            color: #666;
            line-height: 1.3;
        }

        .forum-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .forum-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid green;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .forum-stat:hover {
            background: #e8f0fe;
            transform: translateY(-2px);
        }

        .forum-stat i {
            font-size: 24px;
            color: green;
        }

        .forum-count {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        .forum-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }

        .recent-threads {
            max-height: 200px;
            overflow-y: auto;
        }

        .thread-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }

        .thread-item:hover {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin: 0 -10px;
        }

        .thread-item:last-child {
            border-bottom: none;
        }

        .thread-title {
            font-size: 14px;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .thread-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #666;
        }

        .thread-author {
            color: green;
            font-weight: 500;
        }

        .thread-time {
            color: #888;
        }

        .no-posts {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-style: italic;
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

        footer a {
            color: #0c3b84;
            text-decoration: none;
            font-weight: 500;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .refresh-btn {
            background: none;
            border: none;
            color: green;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .refresh-btn:hover {
            background: #e8f0fe;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .audience-badge {
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            margin-left: 5px;
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .form-checkbox input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            background: linear-gradient(135deg, green, #006400);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .modal-header h2 {
            margin: 0;
            flex: 1;
            font-size: 24px;
        }

        .close-btn {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            padding: 5px;
            margin-left: 15px;
        }

        .close-btn:hover {
            opacity: 0.7;
        }

        .modal-body {
            padding: 25px;
        }

        .post-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            color: #666;
        }

        .post-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .urgent-badge {
            background: #e74c3c;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .post-content {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
            white-space: pre-wrap;
        }

        .file-attachments {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .file-attachments h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-list {
            display: grid;
            gap: 10px;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .file-item:hover {
            border-color: green;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .file-icon {
            width: 40px;
            height: 40px;
            background: #e8f0fe;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0c3b84;
            font-size: 18px;
        }

        .file-details h4 {
            margin: 0 0 4px 0;
            font-size: 14px;
            color: #2c3e50;
        }

        .file-details p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .download-btn {
            background: green;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background: #006400;
            transform: translateY(-1px);
        }

        .comments-section {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }

        .comments-section h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comments-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .comment-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .comment-item:last-child {
            border-bottom: none;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .comment-author {
            font-weight: bold;
            color: green;
        }

        .comment-time {
            font-size: 12px;
            color: #666;
        }

        .comment-content {
            font-size: 14px;
            line-height: 1.4;
            color: #2c3e50;
        }

        .add-comment {
            margin-top: 20px;
        }

        .add-comment textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: vertical;
            font-family: inherit;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .add-comment textarea:focus {
            outline: none;
            border-color: green;
        }

        .submit-comment-btn {
            background: green;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .submit-comment-btn:hover {
            background: #006400;
        }

        .submit-comment-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .no-comments {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .quick-links-grid {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                width: 100%;
                left: -100%;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .post-form-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
            
            .modal-header {
                padding: 15px;
            }
            
            .modal-header h2 {
                font-size: 20px;
            }
            
            .post-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            .file-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .download-btn {
                align-self: stretch;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-bars"></i> Menu
        </div>
        <div class="sidebar-user-info">
            <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
            <div class="user-role"><?php echo htmlspecialchars(ucfirst($user_role)); ?></div>
            <div class="user-faculty"><?php echo htmlspecialchars($faculty_name); ?></div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" id="dashboard-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#" id="my-posts-link"><i class="fas fa-clipboard-list"></i> My Posts</a></li>
            <li><a href="#" id="notifications-link"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="#" id="messages-link"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="#" id="calendar-link"><i class="fas fa-calendar"></i> Academic Calendar</a></li>
            <li><a href="#" id="courses-link"><i class="fas fa-book"></i> My Courses</a></li>
            <li><a href="#" id="grades-link"><i class="fas fa-chart-line"></i> Grades & Results</a></li>
            <li><a href="#" id="library-link"><i class="fas fa-book-open"></i> Library</a></li>
            <li><a href="#" id="settings-link"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="#" id="help-link"><i class="fas fa-question-circle"></i> Help & Support</a></li>
            <li><a href="#" id="contact-link"><i class="fas fa-headset"></i> Contact Admin</a></li>
            <li><a href="auth/logout.php" id="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Container -->
    <div class="container">
        <header>
            <button class="toggle-sidebar-btn" id="toggleSidebarBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="university-info">
                <div class="university-logo">
                    <img src="assets/images/download.png" alt="MUST Logo">
                </div>
                <div class="university-text">
                    <h1>Mbarara University of Science & Technology</h1>
                    <p id="user-role">Digital Notice Board - <?php echo htmlspecialchars(ucfirst($user_role)); ?></p>
                    <p><small>Faculty: <?php echo htmlspecialchars($faculty_name); ?> | Program: <?php echo htmlspecialchars($program_name); ?> | Year: <?php echo htmlspecialchars($year_of_study); ?></small></p>
                </div>
            </div>
            <div class="date-display">
                <div class="current-date" id="currentDate"></div>
                <div class="current-time" id="currentTime"></div>
            </div>
        </header>

        <!-- Dashboard Grid -->
        <div class="dashboard">
            <!-- Dynamic Category Cards will be inserted here by JavaScript -->
            
            <!-- Fixed Cards -->
            <div class="card" id="post-notice-card">
                <div class="card-header">
                    <h2><i class="fas fa-plus-circle"></i> Create New Post</h2>
                    <button class="new-post-btn" id="newPostBtn">
                        <i class="fas fa-plus"></i> New Post
                    </button>
                </div>
                <div class="post-form" id="postForm">
                    <select id="postCategory" required>
                        <option value="">Select Category</option>
                        <!-- Categories will be loaded dynamically -->
                    </select>
                    
                    <select id="postAudienceType" required onchange="toggleAudienceValue()">
                        <option value="">Select Audience</option>
                        <option value="all">All Users</option>
                        <option value="students">Students Only</option>
                        <option value="lecturers">Lecturers Only</option>
                        <option value="faculty">Specific Faculty</option>
                        <option value="program">Specific Program</option>
                        <option value="year">Specific Year</option>
                        <option value="department">Specific Department</option>
                    </select>
                    
                    <div id="audienceValueContainer" style="display: none;">
                        <select id="postAudienceValue" required>
                            <option value="">Select...</option>
                        </select>
                    </div>
                    
                    <input type="text" id="postTitle" placeholder="Post Title" required>
                    
                    <textarea id="postContent" placeholder="Write your post content here..." required></textarea>
                    
                    <div class="post-form-actions">
                        <label class="file-upload">
                            <i class="fas fa-paperclip"></i>
                            <span>Attach Files</span>
                            <input type="file" id="postMedia" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.zip,.rar" multiple style="display: none;">
                        </label>
                        
                        <label class="form-checkbox">
                            <input type="checkbox" id="allowComments" checked>
                            <span>Allow Comments</span>
                        </label>
                        
                        <label class="form-checkbox">
                            <input type="checkbox" id="isUrgent">
                            <span>Mark as Urgent</span>
                        </label>
                        
                        <button class="submit-post" id="submitPost">
                            <i class="fas fa-paper-plane"></i> Publish
                        </button>
                    </div>
                    
                    <div id="mediaPreview"></div>
                </div>
            </div>

            <div class="card" id="quick-links-card">
                <div class="card-header">
                    <h2><i class="fas fa-external-link-alt"></i> Quick Links</h2>
                </div>
                <div class="quick-links-grid">
                    <div class="quick-link-item" onclick="openQuickLink('student-portal')">
                        <div class="quick-link-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Student Portal</h3>
                            <p>Access your academic records and registration</p>
                        </div>
                    </div>
                    
                    <div class="quick-link-item" onclick="openQuickLink('e-learning')">
                        <div class="quick-link-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>E-Learning</h3>
                            <p>Online courses and learning materials</p>
                        </div>
                    </div>
                    
                    <div class="quick-link-item" onclick="openQuickLink('library')">
                        <div class="quick-link-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Digital Library</h3>
                            <p>Access online resources and databases</p>
                        </div>
                    </div>
                    
                    <div class="quick-link-item" onclick="openQuickLink('email')">
                        <div class="quick-link-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>University Email</h3>
                            <p>Access your institutional email</p>
                        </div>
                    </div>
                    
                    <div class="quick-link-item" onclick="openQuickLink('timetable')">
                        <div class="quick-link-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Class Timetable</h3>
                            <p>View your class schedule</p>
                        </div>
                    </div>
                    
                    <div class="quick-link-item" onclick="openQuickLink('fees')">
                        <div class="quick-link-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Fee Payment</h3>
                            <p>Pay tuition and other fees online</p>
                        </div>
                    </div>
                    
                    <div class="quick-link-item" onclick="openQuickLink('hostel')">
                        <div class="quick-link-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Hostel Portal</h3>
                            <p>Accommodation and hostel services</p>
                        </div>
                    </div>
                    
                    <div class="quick-link-item" onclick="openQuickLink('careers')">
                        <div class="quick-link-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Career Services</h3>
                            <p>Job opportunities and career guidance</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" id="calendar-card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-alt"></i> Academic Calendar</h2>
                </div>
                <div class="calendar" id="calendar">
                    <!-- Calendar will be generated by JavaScript -->
                </div>
            </div>

            <div class="card" id="forum-card">
                <div class="card-header">
                    <h2><i class="fas fa-comments"></i> Forum Activity</h2>
                    <button class="refresh-btn" onclick="refreshForum()" title="Refresh Forum">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div id="forum-activity">
                    <div class="forum-stats">
                        <div class="forum-stat" onclick="viewForumThreads()">
                            <i class="fas fa-comment-dots"></i>
                            <div>
                                <span class="forum-count" id="totalThreads">0</span>
                                <span class="forum-label">Active Threads</span>
                            </div>
                        </div>
                        <div class="forum-stat" onclick="viewForumReplies()">
                            <i class="fas fa-reply"></i>
                            <div>
                                <span class="forum-count" id="totalReplies">0</span>
                                <span class="forum-label">Recent Replies</span>
                            </div>
                        </div>
                    </div>
                    <div class="recent-threads" id="recentThreads">
                        <div class="no-posts">Loading forum activity...</div>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <p><strong>Mbarara University of Science & Technology</strong> • P.O. Box 1410, Mbarara, Uganda • © 2024</p>
            <p>Contact: info@must.ac.ug | Tel: +256 414 123 456</p>
        </footer>
    </div>

    <!-- Post Details Modal -->
    <div id="postModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalPostTitle"></h2>
                <span class="close-btn" onclick="closePostModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="post-meta">
                    <span id="modalPostCategory"></span>
                    <span id="modalPostAuthor"></span>
                    <span id="modalPostDate"></span>
                    <span id="modalPostAudience"></span>
                    <span id="modalPostUrgent" class="urgent-badge" style="display: none;">URGENT</span>
                </div>
                
                <div id="modalPostContent" class="post-content"></div>
                
                <!-- File Attachments Section -->
                <div id="modalPostFiles" class="file-attachments" style="display: none;">
                    <h3><i class="fas fa-paperclip"></i> Attachments</h3>
                    <div id="fileList" class="file-list"></div>
                </div>
                
                <!-- Comments Section -->
                <div class="comments-section">
                    <h3><i class="fas fa-comments"></i> Comments</h3>
                    <div id="commentsList" class="comments-list"></div>
                    <div class="add-comment">
                        <textarea id="commentText" placeholder="Add a comment..." rows="3"></textarea>
                        <button onclick="addComment()" class="submit-comment-btn">
                            <i class="fas fa-paper-plane"></i> Post Comment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script 
    // Current user data from PHP session
var currentUser = {
    id: <?php echo $user_id; ?>,
    name: '<?php echo addslashes($user_name); ?>',
    role: '<?php echo $user_role; ?>',
    faculty: '<?php echo addslashes($faculty_name); ?>',
    department: '<?php echo addslashes($department_name); ?>',
    program: '<?php echo addslashes($program_name); ?>',
    year: '<?php echo addslashes($year_of_study); ?>'
};

// Global posts and categories storage
var allPosts = [];
var categories = [];
var faculties = [];
var years = [];
var programs = [];
var departments = [];
var currentPostId = null;

// Sanitize input to prevent XSS
function sanitizeInput(input) {
    var div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

// Update date and time
function updateDateTime() {
    var now = new Date();
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', options);
    document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
}

// Generate calendar
function generateCalendar() {
    var now = new Date();
    var month = now.getMonth();
    var year = now.getFullYear();
    var currentDay = now.getDate();
    
    var firstDay = new Date(year, month, 1);
    var lastDay = new Date(year, month + 1, 0);
    var daysInMonth = lastDay.getDate();
    
    var monthName = now.toLocaleString('en-US', { month: 'long' });
    var calendar = document.getElementById('calendar');
    
    var calendarHTML = '<div class="calendar-header">' + monthName + ' ' + year + '</div>';
    calendarHTML += '<div class="calendar-day">Sun</div>';
    calendarHTML += '<div class="calendar-day">Mon</div>';
    calendarHTML += '<div class="calendar-day">Tue</div>';
    calendarHTML += '<div class="calendar-day">Wed</div>';
    calendarHTML += '<div class="calendar-day">Thu</div>';
    calendarHTML += '<div class="calendar-day">Fri</div>';
    calendarHTML += '<div class="calendar-day">Sat</div>';
    
    for (var i = 0; i < firstDay.getDay(); i++) {
        calendarHTML += '<div class="calendar-date"></div>';
    }
    
    for (var day = 1; day <= daysInMonth; day++) {
        var isCurrentDay = day === currentDay;
        calendarHTML += '<div class="calendar-date ' + (isCurrentDay ? 'current' : '') + '" onclick="selectDate(' + day + ')">' + day + '</div>';
    }
    
    calendar.innerHTML = calendarHTML;
}

// Calendar date selection
function selectDate(day) {
    var dates = document.querySelectorAll('.calendar-date');
    for (var i = 0; i < dates.length; i++) {
        dates[i].classList.remove('current');
    }
    event.target.classList.add('current');
    showDateEvents(day);
}

function showDateEvents(day) {
    alert('Events for ' + day + ' ' + new Date().toLocaleString('en-US', { month: 'long' }) + ' will be shown here');
}

// Load categories from database
function loadCategories() {
    fetch('api/get_categories.php')
        .then(function(response) { return response.json(); })
        .then(function(result) {
            if (result.success) {
                categories = result.categories;
                populateCategoryDropdown();
                createCategoryCards();
            } else {
                console.error('Error loading categories:', result.message);
                loadDefaultCategories();
            }
        })
        .catch(function(error) {
            console.error('Error loading categories:', error);
            loadDefaultCategories();
        });
}

// Populate category dropdown in post form
function populateCategoryDropdown() {
    var categorySelect = document.getElementById('postCategory');
    categorySelect.innerHTML = '<option value="">Select Category</option>';
    
    for (var i = 0; i < categories.length; i++) {
        var option = document.createElement('option');
        option.value = categories[i].name;
        option.textContent = categories[i].name;
        option.setAttribute('data-icon', categories[i].icon);
        categorySelect.appendChild(option);
    }
}

// Toggle audience value dropdown based on audience type
function toggleAudienceValue() {
    var audienceType = document.getElementById('postAudienceType').value;
    var container = document.getElementById('audienceValueContainer');
    var audienceValue = document.getElementById('postAudienceValue');
    
    if (audienceType === 'faculty' || audienceType === 'program' || audienceType === 'year' || audienceType === 'department') {
        container.style.display = 'block';
        populateAudienceValueDropdown(audienceType);
    } else {
        container.style.display = 'none';
        audienceValue.innerHTML = '<option value="">Select...</option>';
    }
}

// Populate audience value dropdown based on audience type
function populateAudienceValueDropdown(type) {
    var audienceValue = document.getElementById('postAudienceValue');
    audienceValue.innerHTML = '<option value="">Loading...</option>';
    
    fetch('api/get_audience_options.php?type=' + type)
        .then(function(response) { return response.json(); })
        .then(function(result) {
            if (result.success) {
                audienceValue.innerHTML = '<option value="">Select...</option>';
                
                for (var i = 0; i < result.options.length; i++) {
                    var option = result.options[i];
                    var optionElement = document.createElement('option');
                    optionElement.value = option.name;
                    
                    if (option.faculty_name) {
                        optionElement.textContent = option.name + ' (' + option.faculty_name + ')';
                    } else {
                        optionElement.textContent = option.name;
                    }
                    
                    optionElement.setAttribute('data-id', option.id);
                    audienceValue.appendChild(optionElement);
                }
            } else {
                audienceValue.innerHTML = '<option value="">Error loading options</option>';
                console.error('Error loading audience options:', result.message);
            }
        })
        .catch(function(error) {
            console.error('Error loading audience options:', error);
            audienceValue.innerHTML = '<option value="">Error loading options</option>';
        });
}

// Create dynamic cards for each category
function createCategoryCards() {
    var dashboard = document.querySelector('.dashboard');
    
    var fixedCards = ['post-notice-card', 'quick-links-card', 'calendar-card', 'forum-card'];
    var cards = document.querySelectorAll('.card');
    for (var i = 0; i < cards.length; i++) {
        var cardId = cards[i].id;
        if (fixedCards.indexOf(cardId) === -1) {
            cards[i].remove();
        }
    }
    
    for (var i = 0; i < categories.length; i++) {
        var category = categories[i];
        var cardId = category.name.toLowerCase().replace(/\s+/g, '-') + '-card';
        var categoryKey = category.name.toLowerCase().replace(/\s+/g, '_');
        
        var cardHTML = '<div class="card" id="' + cardId + '">' +
            '<div class="card-header">' +
            '<h2><i class="fas fa-' + category.icon + '"></i> ' + category.name + '</h2>' +
            '<div>' +
            '<span class="badge" id="' + categoryKey + '-count">0 new</span>' +
            '<button class="refresh-btn" onclick="refreshCard(\'' + categoryKey + '\')" title="Refresh">' +
            '<i class="fas fa-sync-alt"></i>' +
            '</button>' +
            '</div>' +
            '</div>' +
            '<div id="' + categoryKey + '-list" class="loading">' +
            '<div class="no-posts">Loading ' + category.name.toLowerCase() + '...</div>' +
            '</div>' +
            '</div>';
        
        var postNoticeCard = document.getElementById('post-notice-card');
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = cardHTML;
        dashboard.insertBefore(tempDiv.firstChild, postNoticeCard);
    }
}

// Fallback to default categories if database fails
function loadDefaultCategories() {
    categories = [
        { name: 'University Announcements', icon: 'bullhorn' },
        { name: 'Academic Notices', icon: 'graduation-cap' },
        { name: 'Events', icon: 'calendar-day' },
        { name: 'Library Updates', icon: 'book-open' },
        { name: 'Campus News', icon: 'newspaper' }
    ];
    
    populateCategoryDropdown();
    createCategoryCards();
}

// Check if post should be visible to current user
function isPostVisible(post) {
    if (currentUser.role === 'admin') return true;
    
    var userFaculty = (currentUser.faculty || '').toLowerCase();
    var userProgram = (currentUser.program || '').toLowerCase();
    var userYear = (currentUser.year || '').toLowerCase();
    var userDepartment = (currentUser.department || '').toLowerCase();
    
    var postAudienceValue = (post.audience_value || '').toLowerCase();
    
    switch(post.audience_type) {
        case 'all':
            return true;
        case 'students':
            return currentUser.role === 'student';
        case 'lecturers':
            return currentUser.role === 'lecturer';
        case 'faculty':
            return userFaculty.indexOf(postAudienceValue) !== -1 || postAudienceValue.indexOf(userFaculty) !== -1;
        case 'program':
            return userProgram.indexOf(postAudienceValue) !== -1 || postAudienceValue.indexOf(userProgram) !== -1;
        case 'year':
            return userYear === postAudienceValue;
        case 'department':
            return userDepartment.indexOf(postAudienceValue) !== -1 || postAudienceValue.indexOf(userDepartment) !== -1;
        default:
            return false;
    }
}

// Load posts from database
function loadPostsFromDatabase() {
    fetch('api/get_posts.php')
        .then(function(response) { return response.json(); })
        .then(function(result) {
            if (result.success) {
                allPosts = result.posts;
                console.log('Loaded ' + allPosts.length + ' posts from database');
                refreshAllCards();
            } else {
                console.error('Error loading posts from database:', result.message);
            }
        })
        .catch(function(error) {
            console.error('Error loading posts from database:', error);
        });
}

// Get posts filtered by category and audience
function getPostsByCategory(category) {
    var filtered = [];
    for (var i = 0; i < allPosts.length; i++) {
        var post = allPosts[i];
        if (category && post.category_name !== category && post.category !== category) continue;
        if (isPostVisible(post)) {
            filtered.push(post);
        }
    }
    return filtered;
}

// Render notices for specific card
function renderNoticesForCard(posts, category, maxItems) {
    maxItems = maxItems || 5;
    
    if (!posts || posts.length === 0) {
        return '<div class="no-posts">No ' + category.replace(/_/g, ' ') + ' available</div>';
    }

    var limitedPosts = posts.slice(0, maxItems);
    var html = '';
    
    for (var i = 0; i < limitedPosts.length; i++) {
        var post = limitedPosts[i];
        var date = new Date(post.created_at);
        var isUrgent = post.is_urgent;
        var audienceDisplay = getAudienceDisplay(post);
        var categoryIcon = post.category_icon || getCategoryIcon(post.category);
        
        html += '<div class="notice-item ' + (isUrgent ? 'urgent' : '') + '" data-post-id="' + post.id + '">';
        html += '<div class="notice-icon"><i class="fas fa-' + categoryIcon + '"></i></div>';
        html += '<div class="notice-content">';
        html += '<h3 onclick="viewPostDetails(' + post.id + ')">' + sanitizeInput(post.title) + '</h3>';
        html += '<p>' + sanitizeInput(post.content.substring(0, 120)) + (post.content.length > 120 ? '...' : '') + '</p>';
        
        if (post.media && post.media.length > 0) {
            html += '<div class="post-media-preview">';
            html += '<i class="fas fa-paperclip"></i>';
            html += '<small>' + post.media.length + ' attachment(s)</small>';
            html += '</div>';
        }
        
        html += '<div class="notice-meta">';
        html += '<span class="notice-time">' + date.toLocaleDateString() + ' • ' + sanitizeInput(post.user_name) + '</span>';
        html += '<span class="notice-audience">' + audienceDisplay + '</span>';
        html += '</div>';
        
        html += '<div class="post-stats">';
        html += '<span onclick="likePost(' + post.id + ')">';
        html += '<i class="fas fa-thumbs-up ' + (post.user_liked ? 'liked' : '') + '"></i> ' + (post.likes || post.like_count || 0);
        html += '</span>';
        html += '<span onclick="viewPostComments(' + post.id + ')">';
        html += '<i class="fas fa-comment"></i> ' + (post.comments_count || post.comment_count || 0);
        html += '</span>';
        html += '<span onclick="sharePost(' + post.id + ')">';
        html += '<i class="fas fa-share"></i> Share';
        html += '</span>';
        if (post.is_urgent) {
            html += '<span class="audience-badge">URGENT</span>';
        }
        html += '</div>';
        
        html += '</div></div>';
    }
    
    return html;
}

// Helper functions
function getCategoryIcon(category) {
    for (var i = 0; i < categories.length; i++) {
        var cat = categories[i];
        if (cat.name.toLowerCase().replace(/\s+/g, '_') === (category || '').toLowerCase().replace(/\s+/g, '_')) {
            return cat.icon;
        }
    }
    return 'bullhorn';
}

function getAudienceDisplay(post) {
    switch(post.audience_type) {
        case 'all': return 'All Users';
        case 'students': return 'Students Only';
        case 'lecturers': return 'Lecturers Only';
        case 'faculty': return post.audience_value + ' Faculty';
        case 'program': return post.audience_value + ' Program';
        case 'year': return 'Year ' + post.audience_value;
        case 'department': return post.audience_value + ' Department';
        default: return 'Specific Audience';
    }
}

// Post interaction functions
function likePost(postId) {
    var formData = new FormData();
    formData.append('post_id', postId);
    
    fetch('api/like_post.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(result) {
        if (result.success) {
            for (var i = 0; i < allPosts.length; i++) {
                if (allPosts[i].id === postId) {
                    allPosts[i].likes = result.like_count;
                    allPosts[i].user_liked = result.liked;
                    break;
                }
            }
            refreshAllCards();
            showNotification(result.liked ? 'Post liked!' : 'Post unliked!');
        } else {
            showNotification('Error: ' + result.message);
        }
    })
    .catch(function(error) {
        console.error('Error liking post:', error);
        showNotification('Error liking post');
    });
}

function viewPostComments(postId) {
    viewPostDetails(postId);
}

function sharePost(postId) {
    var post = null;
    for (var i = 0; i < allPosts.length; i++) {
        if (allPosts[i].id === postId) {
            post = allPosts[i];
            break;
        }
    }
    
    if (post) {
        var shareUrl = window.location.origin + '/view_post.html?id=' + postId;
        if (navigator.share) {
            navigator.share({
                title: post.title,
                text: post.content,
                url: shareUrl
            });
        } else {
            var tempInput = document.createElement('input');
            tempInput.value = shareUrl;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            showNotification('Post link copied to clipboard!');
        }
    }
}

// Post Modal Functions
function viewPostDetails(postId) {
    currentPostId = postId;
    
    fetch('api/get_post_details.php?id=' + postId)
        .then(function(response) { return response.json(); })
        .then(function(result) {
            if (result.success) {
                displayPostModal(result.post, result.files, result.comments);
            } else {
                showNotification('Error loading post: ' + result.message);
            }
        })
        .catch(function(error) {
            console.error('Error loading post details:', error);
            showNotification('Error loading post details');
        });
}

function displayPostModal(post, files, comments) {
    var modal = document.getElementById('postModal');
    var modalTitle = document.getElementById('modalPostTitle');
    var modalCategory = document.getElementById('modalPostCategory');
    var modalAuthor = document.getElementById('modalPostAuthor');
    var modalDate = document.getElementById('modalPostDate');
    var modalAudience = document.getElementById('modalPostAudience');
    var modalUrgent = document.getElementById('modalPostUrgent');
    var modalContent = document.getElementById('modalPostContent');
    var modalFiles = document.getElementById('modalPostFiles');
    var fileList = document.getElementById('fileList');
    var commentsList = document.getElementById('commentsList');
    
    modalTitle.textContent = sanitizeInput(post.title);
    modalCategory.innerHTML = '<i class="fas fa-' + post.category_icon + '"></i> ' + sanitizeInput(post.category_name);
    modalAuthor.innerHTML = '<i class="fas fa-user"></i> ' + sanitizeInput(post.user_name);
    modalDate.innerHTML = '<i class="fas fa-calendar"></i> ' + new Date(post.created_at).toLocaleString();
    modalAudience.innerHTML = '<i class="fas fa-users"></i> ' + getAudienceDisplay(post);
    
    modalUrgent.style.display = post.is_urgent ? 'inline-flex' : 'none';
    modalContent.textContent = post.content;
    
    if (files && files.length > 0) {
        modalFiles.style.display = 'block';
        var filesHTML = '';
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            filesHTML += '<div class="file-item">';
            filesHTML += '<div class="file-info">';
            filesHTML += '<div class="file-icon"><i class="' + getFileIcon(file.file_type) + '"></i></div>';
            filesHTML += '<div class="file-details">';
            filesHTML += '<h4>' + sanitizeInput(file.file_name) + '</h4>';
            filesHTML += '<p>' + formatFileSize(file.file_size) + ' • ' + file.file_type + '</p>';
            filesHTML += '</div></div>';
            filesHTML += '<a href="api/view_file.php?id=' + file.id + '&download=1" class="download-btn">';
            filesHTML += '<i class="fas fa-download"></i> Download</a>';
            filesHTML += '</div>';
        }
        fileList.innerHTML = filesHTML;
    } else {
        modalFiles.style.display = 'none';
    }
    
    if (comments && comments.length > 0) {
        var commentsHTML = '';
        for (var i = 0; i < comments.length; i++) {
            var comment = comments[i];
            commentsHTML += '<div class="comment-item">';
            commentsHTML += '<div class="comment-header">';
            commentsHTML += '<span class="comment-author">' + sanitizeInput(comment.user_name) + '</span>';
            commentsHTML += '<span class="comment-time">' + new Date(comment.created_at).toLocaleString() + '</span>';
            commentsHTML += '</div>';
            commentsHTML += '<div class="comment-content">' + sanitizeInput(comment.content) + '</div>';
            commentsHTML += '</div>';
        }
        commentsList.innerHTML = commentsHTML;
    } else {
        commentsList.innerHTML = '<div class="no-comments">No comments yet. Be the first to comment!</div>';
    }
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closePostModal() {
    var modal = document.getElementById('postModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    currentPostId = null;
}

function getFileIcon(fileType) {
    if (fileType.indexOf('image/') === 0) return 'fas fa-file-image';
    if (fileType.indexOf('video/') === 0) return 'fas fa-file-video';
    if (fileType.indexOf('audio/') === 0) return 'fas fa-file-audio';
    if (fileType.indexOf('pdf') !== -1) return 'fas fa-file-pdf';
    if (fileType.indexOf('word') !== -1 || fileType.indexOf('document') !== -1) return 'fas fa-file-word';
    if (fileType.indexOf('excel') !== -1 || fileType.indexOf('spreadsheet') !== -1) return 'fas fa-file-excel';
    if (fileType.indexOf('powerpoint') !== -1 || fileType.indexOf('presentation') !== -1) return 'fas fa-file-powerpoint';
    if (fileType.indexOf('zip') !== -1 || fileType.indexOf('archive') !== -1) return 'fas fa-file-archive';
    return 'fas fa-file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Comment functionality
function addComment() {
    if (!currentPostId) return;
    
    var commentText = document.getElementById('commentText').value.trim();
    if (!commentText) {
        showNotification('Please enter a comment');
        return;
    }
    
    var formData = new FormData();
    formData.append('post_id', currentPostId);
    formData.append('content', commentText);
    
    fetch('api/add_comment.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(result) {
        if (result.success) {
            document.getElementById('commentText').value = '';
            showNotification('Comment added successfully!');
            viewPostDetails(currentPostId);
        } else {
            showNotification('Error adding comment: ' + result.message);
        }
    })
    .catch(function(error) {
        console.error('Error adding comment:', error);
        showNotification('Error adding comment');
    });
}

// Create a new post
function createPost(postData) {
    var formData = new FormData();
    formData.append('title', postData.title);
    formData.append('content', postData.content);
    formData.append('category', postData.category);
    formData.append('audience_type', postData.audience_type);
    formData.append('audience_value', postData.audience_value);
    formData.append('is_urgent', postData.isUrgent);
    formData.append('allow_comments', postData.allowComments);
    
    var mediaInput = document.getElementById('postMedia');
    if (mediaInput.files.length > 0) {
        for (var i = 0; i < mediaInput.files.length; i++) {
            formData.append('media[]', mediaInput.files[i]);
        }
    }
    
    fetch('api/create_post.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(result) {
        if (result.success) {
            showNotification('Post created successfully!');
            loadPostsFromDatabase();
            return result.post_id;
        } else {
            throw new Error(result.message);
        }
    })
    .catch(function(error) {
        console.error('Error creating post:', error);
        showNotification('Error creating post: ' + error.message);
    });
}

// Quick Links functionality
function openQuickLink(linkType) {
    var links = {
        'student-portal': 'https://portal.must.ac.ug',
        'e-learning': 'https://elearning.must.ac.ug',
        'library': 'https://library.must.ac.ug',
        'email': 'https://mail.must.ac.ug',
        'timetable': 'https://timetables.must.ac.ug',
        'fees': 'https://fees.must.ac.ug',
        'hostel': 'https://hostels.must.ac.ug',
        'careers': 'https://careers.must.ac.ug'
    };
    
    var url = links[linkType];
    if (url) {
        window.open(url, '_blank');
        showNotification('Opening ' + linkType.replace('-', ' ') + '...');
    } else {
        alert('Link not available at the moment. Please try again later.');
    }
}

// Forum functionality
function loadForumActivity() {
    document.getElementById('totalThreads').textContent = '24';
    document.getElementById('totalReplies').textContent = '156';
    
    var threadsHTML = '<div class="thread-item"><div class="thread-title">Welcome to the new academic year!</div>';
    threadsHTML += '<div class="thread-meta"><span class="thread-author">by Academic Dean</span>';
    threadsHTML += '<span class="thread-time">2 hours ago</span></div></div>';
    
    document.getElementById('recentThreads').innerHTML = threadsHTML;
}

function viewForumThreads() {
    showNotification('Opening forum threads...');
}

function viewForumReplies() {
    showNotification('Opening recent replies...');
}

function refreshForum() {
    loadForumActivity();
    showNotification('Forum activity refreshed!');
}

// Refresh card content
function refreshCard(cardType) {
    var cardElement = document.getElementById(cardType + '-list');
    if (!cardElement) return;
    
    cardElement.classList.add('loading');
    
    loadPostsFromDatabase();
    
    setTimeout(function() {
        var posts = getPostsByCategory(cardType);
        cardElement.innerHTML = renderNoticesForCard(posts, cardType);
        
        var countBadge = document.getElementById(cardType + '-count');
        if (countBadge) {
            countBadge.textContent = posts.length + ' new';
        }
        
        cardElement.classList.remove('loading');
        showNotification(cardType.replace(/_/g, ' ') + ' refreshed!');
    }, 500);
}

function refreshAllCards() {
    for (var i = 0; i < categories.length; i++) {
        var categoryKey = categories[i].name.toLowerCase().replace(/\s+/g, '_');
        var cardElement = document.getElementById(categoryKey + '-list');
        if (cardElement) {
            var posts = getPostsByCategory(categories[i].name);
            cardElement.innerHTML = renderNoticesForCard(posts, categoryKey);
            
            var countBadge = document.getElementById(categoryKey + '-count');
            if (countBadge) {
                countBadge.textContent = posts.length + ' new';
            }
        }
    }
}

// Notification system
function showNotification(message) {
    var notification = document.createElement('div');
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; background: green; color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10000; animation: slideInRight 0.3s ease;';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(function() {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add CSS for notifications
var style = document.createElement('style');
style.textContent = '@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
style.textContent += '@keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }';
style.textContent += '.liked { color: #0c3b84 !important; }';
document.head.appendChild(style);

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    
    updateDateTime();
    setInterval(updateDateTime, 1000);
    generateCalendar();
    loadForumActivity();
    
    setTimeout(function() {
        loadPostsFromDatabase();
    }, 1000);

    var sidebar = document.getElementById('sidebar');
    var toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
toggleSidebarBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !e.target.closest('#toggleSidebarBtn')) {
            sidebar.classList.remove('active');
        }
    });

    var sidebarLinks = [
        'dashboard-link', 'my-posts-link', 'notifications-link', 'messages-link',
        'calendar-link', 'courses-link', 'grades-link', 'library-link',
        'settings-link', 'help-link', 'contact-link'
    ];

    for (var i = 0; i < sidebarLinks.length; i++) {
        var linkId = sidebarLinks[i];
        var linkElement = document.getElementById(linkId);
        if (linkElement) {
            linkElement.addEventListener('click', function(e) {
                e.preventDefault();
                var linkName = this.id.replace('-link', '').replace(/-/g, ' ');
                showNotification(linkName.charAt(0).toUpperCase() + linkName.slice(1) + ' feature will be implemented soon!');
                sidebar.classList.remove('active');
            });
        }
    }
    
    // Logout link
    var logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    }

    var newPostBtn = document.getElementById('newPostBtn');
    var postForm = document.getElementById('postForm');
    var submitPostBtn = document.getElementById('submitPost');

    newPostBtn.addEventListener('click', function() {
        postForm.classList.toggle('active');
        if (postForm.classList.contains('active')) {
            showNotification('Create a new post');
        }
    });

    document.getElementById('postMedia').addEventListener('change', function(e) {
        var files = e.target.files;
        var preview = document.getElementById('mediaPreview');
        preview.innerHTML = '';
        
        if (files.length > 0) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var fileElement = document.createElement('div');
                fileElement.className = 'file-preview';
                fileElement.innerHTML = '<i class="fas fa-file"></i> ' + file.name + ' (' + formatFileSize(file.size) + ')';
                preview.appendChild(fileElement);
            }
        }
    });

    submitPostBtn.addEventListener('click', function() {
        var title = document.getElementById('postTitle').value.trim();
        var content = document.getElementById('postContent').value.trim();
        var category = document.getElementById('postCategory').value;
        var audienceType = document.getElementById('postAudienceType').value;
        var audienceValueSelect = document.getElementById('postAudienceValue');
        var audienceValue = (audienceType === 'all' || audienceType === 'students' || audienceType === 'lecturers') ? '' : audienceValueSelect.value;
        var allowComments = document.getElementById('allowComments').checked;
        var isUrgent = document.getElementById('isUrgent').checked;

        // Validation
        if (!title || !content || !category || !audienceType) {
            alert('Please fill in all required fields.');
            return;
        }

        if ((audienceType === 'faculty' || audienceType === 'program' || audienceType === 'year' || audienceType === 'department') && !audienceValue) {
            alert('Please select a specific audience value.');
            return;
        }

        var postData = {
            title: title,
            content: content,
            category: category,
            audience_type: audienceType,
            audience_value: audienceValue,
            isUrgent: isUrgent,
            allowComments: allowComments
        };

        createPost(postData);
        
        // Reset form
        document.getElementById('postForm').reset();
        document.getElementById('mediaPreview').innerHTML = '';
        document.getElementById('audienceValueContainer').style.display = 'none';
        postForm.classList.remove('active');
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        var modal = document.getElementById('postModal');
        if (event.target === modal) {
            closePostModal();
        }
    });
});

// Auto-refresh every 5 minutes
setInterval(function() {
    refreshAllCards();
}, 300000);
    </script>
</body>
</html>