-- NextTrak Minimal Database Schema (MySQL 8+)

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS nexttrak;
CREATE DATABASE nexttrak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexttrak;

-- Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_picture VARCHAR(255),
    email_verified TINYINT(1) DEFAULT 0,
    email_verification_token VARCHAR(100),
    password_reset_token VARCHAR(100),
    password_reset_expires DATETIME,
    last_login DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_verification_token (email_verification_token),
    INDEX idx_reset_token (password_reset_token)
);

-- Companies
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    `domain` VARCHAR(100),
    website VARCHAR(255),
    industry VARCHAR(100),
    size_category ENUM('startup', 'small', 'medium', 'large', 'enterprise'),
    logo_url VARCHAR(255),
    description TEXT,
    headquarters VARCHAR(100),
    is_custom TINYINT(1) DEFAULT 0,
    added_by_user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_industry (industry),
    INDEX idx_custom (is_custom),
    FOREIGN KEY (added_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Application Statuses
CREATE TABLE application_statuses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    status_name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(50) NOT NULL,
    color_code CHAR(7) NOT NULL, -- Hex (#RRGGBB)
    bg_class VARCHAR(50) NOT NULL,  -- Bootstrap classes
    sort_order INT NOT NULL
);

-- Seed Statuses
INSERT INTO application_statuses (status_name, display_name, color_code, bg_class, sort_order) VALUES
('applied', 'Applied', '#4F46E5', 'bg-primary', 1),
('under_review', 'Under Review', '#6366F1', 'bg-info', 2),
('interview_scheduled', 'Interview Scheduled', '#F59E0B', 'bg-warning', 3),
('follow_up_required', 'Follow-up Required', '#8B5CF6', 'bg-secondary', 4),
('offer_received', 'Offer Received', '#10B981', 'bg-success', 5),
('rejected', 'Rejected', '#6B7280', 'bg-dark', 6);

-- Job Applications
CREATE TABLE job_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_id INT NOT NULL,
    status_id INT NOT NULL DEFAULT 1,
    
    job_title VARCHAR(150) NOT NULL,
    job_description TEXT,
    job_url VARCHAR(500),
    job_type ENUM('full_time', 'part_time', 'contract', 'freelance', 'internship') DEFAULT 'full_time',
    work_location ENUM('remote', 'onsite', 'hybrid') DEFAULT 'onsite',
    
    salary DECIMAL(10,2),
    salary_currency CHAR(3) DEFAULT 'USD',
    salary_period ENUM('hourly', 'monthly', 'yearly') DEFAULT 'yearly',
    
    application_date DATE NOT NULL,
    follow_up_date DATE,
    last_contact_date DATE,
    interview_date DATETIME,
    offer_date DATE,
    response_deadline DATE,
    
    contact_person VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    recruiter_name VARCHAR(100),
    
    application_method ENUM('website', 'email', 'linkedin', 'referral', 'job_board', 'other') DEFAULT 'website',
    referral_source VARCHAR(100),
    cover_letter_path VARCHAR(255),
    resume_path VARCHAR(255),
    
    notes TEXT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    is_favorite TINYINT(1) DEFAULT 0,
    
    views_count INT DEFAULT 0,
    last_viewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_status (user_id, status_id),
    INDEX idx_user_company (user_id, company_id),
    INDEX idx_application_date (application_date),
    INDEX idx_follow_up_date (follow_up_date),
    INDEX idx_priority (priority),
    INDEX idx_favorite (is_favorite),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
    FOREIGN KEY (status_id) REFERENCES application_statuses(id) ON DELETE RESTRICT
);

-- User Sessions
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    last_activity TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,         
    INDEX idx_user_sessions (user_id),
    INDEX idx_session_expiry (expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- System Logs
CREATE TABLE system_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    log_level ENUM('info', 'warning', 'error', 'debug') DEFAULT 'info',
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_logs (user_id, created_at),
    INDEX idx_action_logs (action, created_at),
    INDEX idx_log_level (log_level),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

SET FOREIGN_KEY_CHECKS = 1;
