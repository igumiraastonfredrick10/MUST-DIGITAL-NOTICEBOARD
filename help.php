<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - MUST Notice Board</title>
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
            max-width: 1000px;
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

        .help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .help-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .help-card h3 {
            color: green;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-form {
            margin-bottom: 20px;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .contact-form button {
            background: green;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .contact-form button:hover {
            background: #006400;
        }

        .department-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .department-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid green;
        }

        .department-card h4 {
            color: green;
            margin-bottom: 10px;
        }

        .department-card p {
            margin-bottom: 5px;
            font-size: 14px;
        }

        .faq-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .faq-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .faq-answer {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1><i class="fas fa-question-circle"></i> Help & Support</h1>
                <p>Find answers to common questions</p>
            </div>
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <div class="help-grid">
            <div class="help-card">
                <h3><i class="fas fa-book"></i> User Guide</h3>
                <p>Learn how to use the notice board system effectively.</p>
                <ul style="margin-top: 15px; margin-left: 20px;">
                    <li>Creating posts</li>
                    <li>Managing notifications</li>
                    <li>Viewing notices</li>
                    <li>Commenting and liking</li>
                </ul>
            </div>

            <div class="help-card">
                <h3><i class="fas fa-video"></i> Video Tutorials</h3>
                <p>Watch step-by-step video guides.</p>
                <ul style="margin-top: 15px; margin-left: 20px;">
                    <li>Getting started</li>
                    <li>Creating your first post</li>
                    <li>Managing your profile</li>
                    <li>Using quick links</li>
                </ul>
            </div>

            <div class="help-card">
                <h3><i class="fas fa-headset"></i> Contact Support</h3>
                <p>Need more help? Reach out to our support team or specific departments.</p>
                
                <div class="contact-form">
                    <h4 style="color: green; margin-bottom: 15px;">Submit an Inquiry</h4>
                    <form action="submit_support.php" method="POST">
                        <input type="text" name="name" placeholder="Name" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="tel" name="phone" placeholder="Phone">
                        <input type="text" name="subject" placeholder="Subject" required>
                        <textarea name="message" rows="4" placeholder="Message" required></textarea>
                        <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
                    </form>
                </div>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <h4 style="color: green; margin-bottom: 15px;">We are here to help</h4>
                    <p style="margin-bottom: 15px;"><strong>Address:</strong> P.O. Box 1410, Mbarara, Uganda</p>
                    
                    <div class="department-grid">
                        <div class="department-card">
                            <h4>Academic Affairs & Admissions</h4>
                            <p><strong>For more information regarding admissions</strong></p>
                            <p><strong>Email:</strong> registrar@must.ac.ug</p>
                            <p><strong>Admissions:</strong> admissions@must.ac.ug</p>
                            <p><strong>Phone:</strong> +256 485 421 234</p>
                        </div>

                        <div class="department-card">
                            <h4>Student Welfare</h4>
                            <p><strong>Maureen Kabonesa, Dean of Students</strong></p>
                            <p><strong>Office:</strong> +256 485 421 235</p>
                            <p><strong>WhatsApp:</strong> +256 772 430 205</p>
                            <p><strong>Email:</strong> studentwelfare@must.ac.ug</p>
                        </div>

                        <div class="department-card">
                            <h4>International Relations Affairs</h4>
                            <p><strong>For International Students Programs</strong></p>
                            <p><strong>WhatsApp:</strong> +256 772 430 205</p>
                            <p><strong>Email:</strong> international@must.ac.ug</p>
                        </div>

                        <div class="department-card">
                            <h4>Directorate of Research and Graduate Training Programs</h4>
                            <p><strong>Email:</strong> dorgt@must.ac.ug</p>
                            <p><strong>Phone:</strong> +256 485 421 236</p>
                        </div>
                    </div>

                    <p style="margin-top: 15px; font-size: 12px; color: #666;">
                        <strong>Public Relations:</strong> +256 772 430 205
                    </p>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2 style="margin-bottom: 25px; color: green;">Frequently Asked Questions</h2>

            <div class="faq-item">
                <div class="faq-question">How do I create a new notice?</div>
                <div class="faq-answer">Click the "New Post" button on the dashboard, select a category and audience, fill in the title and content, then click "Publish".</div>
            </div>

            <div class="faq-item">
                <div class="faq-question">Who can see my posts?</div>
                <div class="faq-answer">This depends on the audience you select when creating the post. You can target all users, specific faculties, programs, years, or departments.</div>
            </div>

            <div class="faq-item">
                <div class="faq-question">How do I change my profile picture?</div>
                <div class="faq-answer">Go to Settings, click "Change Photo" under your profile picture, select an image, and it will be uploaded automatically.</div>
            </div>

            <div class="faq-item">
                <div class="faq-question">Can I delete a post after publishing?</div>
                <div class="faq-answer">Yes, go to "My Posts" from the sidebar menu, find the post you want to delete, and click the delete button.</div>
            </div>

            <div class="faq-item">
                <div class="faq-question">How do I attach files to a notice?</div>
                <div class="faq-answer">When creating a post, click "Attach Files" and select the files you want to upload. Supported formats include PDF, Word, Excel, images, and more.</div>
            </div>
        </div>
    </div>
</body>
</html>