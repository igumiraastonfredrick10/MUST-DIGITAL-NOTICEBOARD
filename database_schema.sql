-- Digital Notice Board Database Schema
-- This file contains the complete database structure needed for the audience filtering system

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS mustnn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mustnn;

-- =============================================
-- CORE TABLES
-- =============================================

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'lecturer', 'student') NOT NULL DEFAULT 'student',
    faculty_id INT NULL,
    department_id INT NULL,
    program_id INT NULL,
    year_of_study INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Faculties table
CREATE TABLE IF NOT EXISTS faculties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_code (code)
);

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL,
    faculty_id INT NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    INDEX idx_name (name),
    INDEX idx_faculty (faculty_id)
);

-- Programs table
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    department_id INT NOT NULL,
    faculty_id INT NOT NULL,
    duration_years INT NOT NULL DEFAULT 3,
    degree_type ENUM('certificate', 'diploma', 'bachelor', 'master', 'phd') NOT NULL DEFAULT 'bachelor',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    INDEX idx_name (name),
    INDEX idx_department (department_id),
    INDEX idx_faculty (faculty_id)
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) DEFAULT 'bullhorn',
    color VARCHAR(7) DEFAULT '#007bff',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- Audience Types table
CREATE TABLE IF NOT EXISTS audience_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
);

-- =============================================
-- NOTICES SYSTEM
-- =============================================

-- Notices table
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category_id INT NOT NULL,
    author_id INT NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    slug VARCHAR(255) UNIQUE NOT NULL,
    publish_date TIMESTAMP NULL,
    expiry_date TIMESTAMP NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    allow_comments BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_publish_date (publish_date),
    INDEX idx_category (category_id),
    INDEX idx_author (author_id),
    INDEX idx_priority (priority),
    FULLTEXT idx_search (title, content)
);

-- Notice Audiences table (handles complex audience targeting)
CREATE TABLE IF NOT EXISTS notice_audiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notice_id INT NOT NULL,
    audience_type_id INT NOT NULL,
    faculty_id INT NULL,
    department_id INT NULL,
    program_id INT NULL,
    year_level INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notice_id) REFERENCES notices(id) ON DELETE CASCADE,
    FOREIGN KEY (audience_type_id) REFERENCES audience_types(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    INDEX idx_notice (notice_id),
    INDEX idx_audience_type (audience_type_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_department (department_id),
    INDEX idx_program (program_id),
    INDEX idx_year (year_level)
);

-- Notice Attachments table
CREATE TABLE IF NOT EXISTS notice_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notice_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL,
    display_order INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notice_id) REFERENCES notices(id) ON DELETE CASCADE,
    INDEX idx_notice (notice_id)
);

-- Notice Comments table
CREATE TABLE IF NOT EXISTS notice_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notice_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (notice_id) REFERENCES notices(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notice (notice_id),
    INDEX idx_user (user_id)
);

-- Notice Likes table
CREATE TABLE IF NOT EXISTS notice_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notice_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notice_id) REFERENCES notices(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (notice_id, user_id),
    INDEX idx_notice (notice_id),
    INDEX idx_user (user_id)
);

-- Activity Logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(50) NULL,
    entity_id INT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
);

-- =============================================
-- FOREIGN KEY CONSTRAINTS FOR USERS TABLE
-- =============================================

-- Add foreign key constraints to users table (after other tables are created)
ALTER TABLE users 
ADD CONSTRAINT fk_users_faculty FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_users_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_users_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL;

-- =============================================
-- INITIAL DATA
-- =============================================

-- Insert default audience types
INSERT IGNORE INTO audience_types (name, slug, description) VALUES
('All Users', 'all', 'Visible to all registered users'),
('Students Only', 'students', 'Visible only to students'),
('Lecturers Only', 'lecturers', 'Visible only to lecturers'),
('Specific Faculty', 'faculty', 'Visible to specific faculty, departments, programs, or years'),
('Specific Program', 'program', 'Visible to specific academic programs'),
('Specific Year', 'year', 'Visible to specific year levels'),
('Specific Department', 'department', 'Visible to specific departments');

-- Insert default categories
INSERT IGNORE INTO categories (name, icon, color, description) VALUES
('Announcements', 'bullhorn', '#dc3545', 'General announcements and notices'),
('Academic Notices', 'graduation-cap', '#007bff', 'Academic-related information'),
('Events', 'calendar-day', '#28a745', 'Upcoming events and activities'),
('Library Updates', 'book-open', '#6f42c1', 'Library news and updates'),
('Campus News', 'newspaper', '#fd7e14', 'Campus news and information'),
('Examinations', 'clipboard-check', '#e83e8c', 'Examination schedules and results'),
('Admissions', 'user-plus', '#20c997', 'Admission-related notices'),
('Fee Payments', 'money-bill-wave', '#ffc107', 'Fee payment information'),
('Job Opportunities', 'briefcase', '#6c757d', 'Career and job opportunities'),
('Research', 'microscope', '#17a2b8', 'Research opportunities and updates');

-- Insert sample faculties (customize for MUST)
INSERT IGNORE INTO faculties (name, code, description) VALUES
('Faculty of Computing and Informatics', 'FCI', 'Computer Science, Information Technology, and related programs'),
('Faculty of Medicine', 'FOM', 'Medical and health sciences programs'),
('Faculty of Business and Management Sciences', 'FBMS', 'Business, management, and commerce programs'),
('Faculty of Science', 'FOS', 'Natural sciences and mathematics programs'),
('Faculty of Education', 'FOE', 'Education and teaching programs'),
('Faculty of Applied Sciences', 'FAS', 'Applied sciences and technology programs');

-- Insert sample departments for FCI
INSERT IGNORE INTO departments (name, code, faculty_id, description) VALUES
('Computer Science', 'CS', 1, 'Computer Science Department'),
('Information Technology', 'IT', 1, 'Information Technology Department'),
('Software Engineering', 'SE', 1, 'Software Engineering Department'),
('Information Systems', 'IS', 1, 'Information Systems Department');

-- Insert sample programs for Computer Science Department
INSERT IGNORE INTO programs (name, code, department_id, faculty_id, duration_years, degree_type, description) VALUES
('Bachelor of Computer Science', 'BCS', 1, 1, 3, 'bachelor', 'Undergraduate program in Computer Science'),
('Bachelor of Information Technology', 'BIT', 2, 1, 3, 'bachelor', 'Undergraduate program in Information Technology'),
('Bachelor of Software Engineering', 'BSE', 3, 1, 4, 'bachelor', 'Undergraduate program in Software Engineering'),
('Bachelor of Information Systems', 'BIS', 4, 1, 3, 'bachelor', 'Undergraduate program in Information Systems'),
('Master of Computer Science', 'MCS', 1, 1, 2, 'master', 'Graduate program in Computer Science'),
('Master of Information Technology', 'MIT', 2, 1, 2, 'master', 'Graduate program in Information Technology');

-- =============================================
-- USEFUL VIEWS
-- =============================================

-- View for complete user information
CREATE OR REPLACE VIEW user_details AS
SELECT 
    u.id, u.username, u.email, u.first_name, u.last_name, u.role,
    u.year_of_study, u.is_active, u.created_at,
    f.name as faculty_name, f.code as faculty_code,
    d.name as department_name, d.code as department_code,
    p.name as program_name, p.code as program_code, p.duration_years
FROM users u
LEFT JOIN faculties f ON u.faculty_id = f.id
LEFT JOIN departments d ON u.department_id = d.id
LEFT JOIN programs p ON u.program_id = p.id;

-- View for notice details with audience information
CREATE OR REPLACE VIEW notice_details AS
SELECT 
    n.id, n.title, n.content, n.priority, n.status, n.publish_date, n.expiry_date,
    n.is_pinned, n.view_count, n.allow_comments, n.created_at, n.updated_at,
    c.name as category_name, c.icon as category_icon, c.color as category_color,
    CONCAT(u.first_name, ' ', u.last_name) as author_name, u.username as author_username,
    at.name as audience_type_name, at.slug as audience_type_slug,
    f.name as target_faculty, d.name as target_department, 
    p.name as target_program, na.year_level as target_year
FROM notices n
LEFT JOIN categories c ON n.category_id = c.id
LEFT JOIN users u ON n.author_id = u.id
LEFT JOIN notice_audiences na ON n.id = na.notice_id
LEFT JOIN audience_types at ON na.audience_type_id = at.id
LEFT JOIN faculties f ON na.faculty_id = f.id
LEFT JOIN departments d ON na.department_id = d.id
LEFT JOIN programs p ON na.program_id = p.id;

-- =============================================
-- INDEXES FOR PERFORMANCE
-- =============================================

-- Additional indexes for better performance
CREATE INDEX IF NOT EXISTS idx_notices_published ON notices(status, publish_date, is_pinned);
CREATE INDEX IF NOT EXISTS idx_notice_audiences_composite ON notice_audiences(notice_id, audience_type_id, faculty_id, program_id, year_level);
CREATE INDEX IF NOT EXISTS idx_users_composite ON users(role, faculty_id, program_id, year_of_study);

-- =============================================
-- SAMPLE ADMIN USER (Change password!)
-- =============================================

-- Insert sample admin user (password: admin123 - CHANGE THIS!)
INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, role, is_active) VALUES
('admin', 'admin@must.ac.ug', 'admin123', 'System', 'Administrator', 'admin', TRUE);

-- Insert sample student user (password: student123)
INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, role, faculty_id, department_id, program_id, year_of_study, is_active) VALUES
('student1', 'student1@must.ac.ug', 'student123', 'John', 'Doe', 'student', 1, 1, 1, 2, TRUE);

-- Insert sample lecturer user (password: lecturer123)
INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, role, faculty_id, department_id, is_active) VALUES
('lecturer1', 'lecturer1@must.ac.ug', 'lecturer123', 'Jane', 'Smith', 'lecturer', 1, 1, TRUE);

-- =============================================
-- COMPLETION MESSAGE
-- =============================================

SELECT 'Database schema created successfully!' as message;