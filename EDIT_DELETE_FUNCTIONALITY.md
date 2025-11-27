# Post Edit/Delete Functionality

## Overview
This implementation adds time-based edit and delete functionality to the Digital Notice Board system. Users can edit their posts within 1 hour of creation, and after that time expires, they can only delete their posts.

## Features

### 1. Time-Based Edit Window
- **Edit Period**: Users can edit their posts for exactly 1 hour (3600 seconds) after creation
- **Real-time Countdown**: The remaining edit time is displayed and updates every second
- **Automatic Expiration**: Edit buttons are automatically disabled when the time expires

### 2. Post Management Actions
- **Edit**: Available within 1 hour of post creation
- **Delete**: Always available for post authors
- **Admin Override**: Administrators can edit posts at any time

### 3. User Interface Enhancements
- **My Posts Page**: Shows edit/delete buttons with time indicators
- **Dashboard**: Shows edit/delete options directly on posts for authors
- **Modal Edit Form**: Clean interface for editing post details
- **Time Display**: Shows remaining edit time or expiration status

## Files Modified/Created

### New API Files
1. **`api/edit_post.php`** - Handles post editing with time validation
2. **`api/get_post_for_edit.php`** - Retrieves post data for editing

### Modified Files
1. **`my_posts.php`** - Added edit functionality and time-based UI
2. **`index.php`** - Added edit/delete buttons to post display
3. **`api/delete_post.php`** - Already existed, no changes needed

### Test Files
1. **`test_edit_time_limit.php`** - Test script to verify functionality

## Technical Implementation

### Database Changes
No database schema changes were required. The system uses the existing `created_at` timestamp to calculate edit eligibility.

### Time Calculation
```php
$seconds_since_creation = TIMESTAMPDIFF(SECOND, created_at, NOW());
$one_hour = 3600; // 1 hour in seconds
$can_edit = $seconds_since_creation <= $one_hour;
```

### Security Features
- **Ownership Validation**: Users can only edit/delete their own posts
- **Admin Override**: Administrators bypass time restrictions
- **Input Sanitization**: All form inputs are sanitized
- **CSRF Protection**: Uses session-based authentication

## User Experience

### For Regular Users
1. **Create Post**: Normal post creation process
2. **Edit Window**: 1-hour window to make changes
3. **Time Indicator**: Real-time countdown showing remaining edit time
4. **Post-Expiration**: Only delete option available after 1 hour

### For Administrators
- Can edit any post at any time
- No time restrictions apply
- Full post management capabilities

## API Endpoints

### Edit Post
```
POST /api/edit_post.php
Parameters:
- post_id: ID of the post to edit
- title: New post title
- content: New post content
- category: Post category
- is_urgent: Urgent flag (0 or 1)
```

### Get Post for Edit
```
GET /api/get_post_for_edit.php?post_id={id}
Returns:
- Post details
- Edit eligibility status
- Remaining edit time
```

### Delete Post
```
POST /api/delete_post.php
Parameters:
- post_id: ID of the post to delete
```

## Error Handling

### Time Expiration
When edit time expires:
- User receives clear message about expiration
- Option to delete post is offered
- UI updates to reflect current status

### Validation Errors
- Missing required fields
- Invalid post ID
- Unauthorized access attempts
- Database connection issues

## Testing

### Manual Testing
1. Create a new post
2. Verify edit button appears with countdown
3. Edit the post within 1 hour
4. Wait for time expiration
5. Verify edit button disappears
6. Verify delete button still works

### Automated Testing
Run `test_edit_time_limit.php` to verify:
- Time calculations are correct
- Database queries work properly
- Edit eligibility is determined correctly

## Configuration

### Time Limit Adjustment
To change the edit time limit, modify the `$one_hour` variable in:
- `api/edit_post.php`
- `api/get_post_for_edit.php`
- `my_posts.php`

Example for 30 minutes:
```php
$one_hour = 1800; // 30 minutes in seconds
```

## Browser Compatibility
- Modern browsers with JavaScript enabled
- Real-time countdown requires JavaScript
- Graceful degradation for older browsers

## Performance Considerations
- Minimal database impact (uses existing timestamps)
- Client-side countdown reduces server requests
- Efficient SQL queries with proper indexing

## Future Enhancements
1. **Edit History**: Track post edit history
2. **Notification System**: Notify users before edit time expires
3. **Bulk Operations**: Edit/delete multiple posts
4. **Custom Time Limits**: Different limits per user role
5. **Draft System**: Save drafts during editing

## Troubleshooting

### Common Issues
1. **Edit button not appearing**: Check user ownership and time limit
2. **Time not updating**: Ensure JavaScript is enabled
3. **Edit fails**: Verify time hasn't expired and user has permissions
4. **Database errors**: Check connection and table structure

### Debug Mode
Enable error logging in PHP to troubleshoot issues:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Security Considerations
- All user inputs are sanitized
- Session-based authentication required
- Ownership validation on all operations
- SQL injection prevention with prepared statements
- XSS protection with proper escaping