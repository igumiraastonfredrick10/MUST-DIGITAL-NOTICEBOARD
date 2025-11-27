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