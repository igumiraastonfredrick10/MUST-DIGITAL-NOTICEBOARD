<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

$conn = getDBConnection();

// Get user's posts with time calculations
$stmt = $conn->prepare("SELECT n.*, c.name as category_name, c.icon as category_icon,
                        COUNT(DISTINCT nc.id) as comment_count,
                        COUNT(DISTINCT nl.id) as like_count,
                        TIMESTAMPDIFF(SECOND, n.created_at, NOW()) as seconds_since_creation
                        FROM notices n
                        LEFT JOIN categories c ON n.category_id = c.id
                        LEFT JOIN notice_comments nc ON n.id = nc.notice_id
                        LEFT JOIN notice_likes nl ON n.id = nl.notice_id
                        WHERE n.author_id = ?
                        GROUP BY n.id
                        ORDER BY n.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts - MUST Notice Board</title>
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
            max-width: 1200px;
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
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .posts-grid {
            display: grid;
            gap: 20px;
        }

        .post-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .post-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .post-meta {
            display: flex;
            gap: 15px;
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .post-content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .post-stats {
            display: flex;
            gap: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-direction: column;
        }

        .edit-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-btn:hover {
            background: #2980b9;
        }

        .edit-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .time-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .time-expired {
            color: #e74c3c;
        }

        .time-remaining {
            color: #27ae60;
        }

        /* Edit Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .no-posts {
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
                <h1><i class="fas fa-clipboard-list"></i> My Posts</h1>
                <p>Manage your notices and announcements</p>
            </div>
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <div class="posts-grid">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div>
                                <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                <div class="post-meta">
                                    <span><i class="fas fa-folder"></i> <?php echo htmlspecialchars($post['category_name']); ?></span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                                    <?php if ($post['priority'] === 'urgent'): ?>
                                        <span style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> URGENT</span>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $seconds_since_creation = $post['seconds_since_creation'];
                                $one_hour = 3600; // 1 hour in seconds
                                $can_edit = $seconds_since_creation <= $one_hour || $user_role === 'admin';
                                $remaining_seconds = max(0, $one_hour - $seconds_since_creation);
                                ?>
                                <div class="time-info">
                                    <?php if ($can_edit && $user_role !== 'admin'): ?>
                                        <span class="time-remaining">
                                            <i class="fas fa-clock"></i> 
                                            Can edit for <?php echo gmdate("i:s", $remaining_seconds); ?> more
                                        </span>
                                    <?php elseif ($user_role !== 'admin'): ?>
                                        <span class="time-expired">
                                            <i class="fas fa-clock"></i> 
                                            Edit time expired (1 hour limit)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="action-buttons">
                                <?php if ($can_edit): ?>
                                    <button class="edit-btn" onclick="editPost(<?php echo $post['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                <?php endif; ?>
                                <button class="delete-btn" onclick="deletePost(<?php echo $post['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <div class="post-content">
                            <?php echo htmlspecialchars(substr($post['content'], 0, 200)); ?>
                            <?php if (strlen($post['content']) > 200): ?>...<?php endif; ?>
                        </div>
                        <div class="post-stats">
                            <span class="stat"><i class="fas fa-eye"></i> <?php echo $post['view_count']; ?> views</span>
                            <span class="stat"><i class="fas fa-thumbs-up"></i> <?php echo $post['like_count']; ?> likes</span>
                            <span class="stat"><i class="fas fa-comment"></i> <?php echo $post['comment_count']; ?> comments</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-posts">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                    <h2>No posts yet</h2>
                    <p>You haven't created any notices yet. Go to the dashboard to create your first post!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Post</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editPostId">
                    
                    <div class="form-group">
                        <label for="editTitle">Title</label>
                        <input type="text" id="editTitle" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editCategory">Category</label>
                        <select id="editCategory" required>
                            <option value="">Select Category</option>
                            <option value="Announcements">Announcements</option>
                            <option value="Academic Notices">Academic Notices</option>
                            <option value="Events">Events</option>
                            <option value="Library Updates">Library Updates</option>
                            <option value="Campus News">Campus News</option>
                            <option value="Examinations">Examinations</option>
                            <option value="Admissions">Admissions</option>
                            <option value="Fee Payments">Fee Payments</option>
                            <option value="Job Opportunities">Job Opportunities</option>
                            <option value="Research">Research</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="editContent">Content</label>
                        <textarea id="editContent" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="editIsUrgent">
                            <label for="editIsUrgent">Mark as Urgent</label>
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function deletePost(postId) {
            if (!confirm('Are you sure you want to delete this post?')) return;
            
            fetch('api/delete_post.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'post_id=' + postId
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    alert('Post deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            });
        }

        function editPost(postId) {
            // Fetch post details
            fetch('api/get_post_for_edit.php?post_id=' + postId)
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    if (!result.can_edit) {
                        alert('Edit time expired. Posts can only be edited within 1 hour of creation. You can delete this post instead.');
                        return;
                    }
                    
                    // Populate the edit form
                    document.getElementById('editPostId').value = result.post.id;
                    document.getElementById('editTitle').value = result.post.title;
                    document.getElementById('editContent').value = result.post.content;
                    document.getElementById('editCategory').value = result.post.category_name;
                    document.getElementById('editIsUrgent').checked = result.post.is_urgent;
                    
                    // Show the modal
                    document.getElementById('editModal').style.display = 'block';
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Error loading post details');
            });
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Handle edit form submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData();
            formData.append('post_id', document.getElementById('editPostId').value);
            formData.append('title', document.getElementById('editTitle').value);
            formData.append('content', document.getElementById('editContent').value);
            formData.append('category', document.getElementById('editCategory').value);
            formData.append('is_urgent', document.getElementById('editIsUrgent').checked ? 1 : 0);
            
            fetch('api/edit_post.php', {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    alert('Post updated successfully!');
                    closeEditModal();
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                    if (result.can_delete) {
                        if (confirm('Would you like to delete this post instead?')) {
                            deletePost(document.getElementById('editPostId').value);
                        }
                    }
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Error updating post');
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }

        // Update remaining time every second for posts that can still be edited
        function updateRemainingTimes() {
            var timeElements = document.querySelectorAll('.time-remaining');
            timeElements.forEach(function(element) {
                var timeText = element.textContent;
                var match = timeText.match(/(\d+):(\d+)/);
                if (match) {
                    var minutes = parseInt(match[1]);
                    var seconds = parseInt(match[2]);
                    var totalSeconds = minutes * 60 + seconds;
                    
                    if (totalSeconds > 0) {
                        totalSeconds--;
                        var newMinutes = Math.floor(totalSeconds / 60);
                        var newSeconds = totalSeconds % 60;
                        var newTime = String(newMinutes).padStart(2, '0') + ':' + String(newSeconds).padStart(2, '0');
                        element.innerHTML = '<i class="fas fa-clock"></i> Can edit for ' + newTime + ' more';
                    } else {
                        element.className = 'time-info time-expired';
                        element.innerHTML = '<i class="fas fa-clock"></i> Edit time expired (1 hour limit)';
                        // Disable edit button and reload page to update UI
                        setTimeout(function() { location.reload(); }, 1000);
                    }
                }
            });
        }

        // Update times every second
        setInterval(updateRemainingTimes, 1000);

        // Check if we need to auto-open edit modal from URL parameter
        window.addEventListener('load', function() {
            var urlParams = new URLSearchParams(window.location.search);
            var editPostId = urlParams.get('edit');
            if (editPostId) {
                editPost(editPostId);
                // Clean up URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>
</html>