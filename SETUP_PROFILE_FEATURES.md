# Profile Photo & Enhanced Settings Setup Guide

## Features Added

### 1. Profile Photo in Sidebar
- User profile photos now display in the sidebar
- Click on photo to navigate to settings
- Placeholder with initials shown if no photo uploaded

### 2. Enhanced Settings Page
The profile settings page now includes:
- **Profile Photo Upload**: Upload and manage profile pictures
- **Basic Information**: Update name and email
- **Notification Preferences**: Control email notifications
- **Privacy Settings**: Manage profile visibility and online status
- **Appearance**: Choose theme (Light/Dark/Auto) and language
- **Password Management**: Change account password

## Database Setup

To enable these features, run the migration scripts:

### Option 1: Via Browser
1. Navigate to: `http://localhost/work/add_profile_photo_column.php`
2. Navigate to: `http://localhost/work/add_user_settings_columns.php`

### Option 2: Via phpMyAdmin
Run these SQL commands:

```sql
-- Add profile photo column
ALTER TABLE users ADD COLUMN profile_photo VARCHAR(500) NULL AFTER email;

-- Add settings columns
ALTER TABLE users ADD COLUMN email_notifications TINYINT(1) DEFAULT 1;
ALTER TABLE users ADD COLUMN urgent_only TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN profile_visibility ENUM('public', 'faculty', 'private') DEFAULT 'public';
ALTER TABLE users ADD COLUMN show_online_status TINYINT(1) DEFAULT 1;
ALTER TABLE users ADD COLUMN theme ENUM('light', 'dark', 'auto') DEFAULT 'light';
ALTER TABLE users ADD COLUMN language VARCHAR(5) DEFAULT 'en';
```

## File Structure
- Profile photos stored in: `uploads/profiles/`
- Supported formats: JPG, JPEG, PNG, GIF
- Files named as: `user_{id}_{timestamp}.{ext}`

## Usage

### Uploading Profile Photo
1. Click on profile photo/placeholder in sidebar OR navigate to Settings
2. Click "Change Photo" button
3. Select image file
4. Photo uploads automatically

### Accessing Settings
- Click "Settings" in sidebar menu
- Or click on profile photo in sidebar
- Update any preferences and click respective "Update" buttons

## Security
- Profile photos protected by .htaccess
- File type validation on upload
- Old photos automatically deleted when new one uploaded
- All user inputs sanitized

## Next Steps
1. Run the database migrations
2. Test profile photo upload
3. Configure notification preferences
4. Customize appearance settings
