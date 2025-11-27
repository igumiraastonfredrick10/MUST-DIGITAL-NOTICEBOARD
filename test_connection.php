<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Like Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px 20px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Complete Like Feature Test</h1>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <p class="error">❌ Not logged in!</p>
        <a href="auth/login.php">Login First</a>
    <?php else: ?>
        <p class="success">✅ Logged in as User ID: <?php echo $_SESSION['user_id']; ?></p>
        
        <h2>Step 1: Test Database Connection</h2>
        <?php
        $conn = getDBConnection();
        if ($conn) {
            echo '<p class="success">✅ Database connected</p>';
            
            // Get a post
            $result = $conn->query("SELECT id, title FROM notices LIMIT 1");
            if ($result && $result->num_rows > 0) {
                $post = $result->fetch_assoc();
                $testPostId = $post['id'];
                echo '<p class="success">✅ Found post: ' . htmlspecialchars($post['title']) . ' (ID: ' . $testPostId . ')</p>';
                
                // Check likes
                $likesResult = $conn->query("SELECT COUNT(*) as count FROM notice_likes WHERE notice_id = " . $testPostId);
                $likesRow = $likesResult->fetch_assoc();
                echo '<p>Current likes on this post: ' . $likesRow['count'] . '</p>';
                
                // Check if user liked
                $userLikedResult = $conn->query("SELECT id FROM notice_likes WHERE notice_id = " . $testPostId . " AND user_id = " . $_SESSION['user_id']);
                $userLiked = ($userLikedResult->num_rows > 0);
                echo '<p>You ' . ($userLiked ? 'HAVE' : 'have NOT') . ' liked this post</p>';
                
                echo '<button onclick="testLike(' . $testPostId . ')">Test Like/Unlike</button>';
                echo '<div id="result"></div>';
                
            } else {
                echo '<p class="error">❌ No posts found in database</p>';
            }
            
            closeDBConnection($conn);
        } else {
            echo '<p class="error">❌ Database connection failed</p>';
        }
        ?>
    <?php endif; ?>
    
    <script>
    function testLike(postId) {
        console.log('Testing like for post ID:', postId);
        
        var formData = new FormData();
        formData.append('post_id', postId);
        
        fetch('api/like_post.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(function(result) {
            console.log('Result:', result);
            document.getElementById('result').innerHTML = 
                '<h3>API Response:</h3>' +
                '<pre>' + JSON.stringify(result, null, 2) + '</pre>' +
                '<p class="success">✅ Like API is working!</p>';
        })
        .catch(function(error) {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = 
                '<p class="error">❌ Error: ' + error + '</p>';
        });
    }
    </script>
</body>
</html>