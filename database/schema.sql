DROP DATABASE IF EXISTS nexttrak;
CREATE DATABASE nexttrak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexttrak;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Companies Table
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- Application Statuses Table
CREATE TABLE application_statuses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    status_name VARCHAR(50) NOT NULL UNIQUE
);

-- Seed default statuses
INSERT INTO application_statuses (status_name) VALUES
('Applied'),
('Interview'),
('Offer'),
('Rejected');

-- Job Applications Table
CREATE TABLE job_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_id INT NOT NULL,
    status_id INT NOT NULL DEFAULT 1,
    job_title VARCHAR(150) NOT NULL,
    job_url VARCHAR(500),
    salary INT COMMENT 'Salary in local currency',
    location VARCHAR(100) COMMENT 'e.g., Remote, New York, NY, Mumbai, India',
    job_type ENUM('WFH', 'WFO', 'Hybrid') DEFAULT 'WFO' COMMENT 'Work From Home, Work From Office, Hybrid',
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium' COMMENT 'Application priority level',
    application_date DATE NOT NULL,
    follow_up_date DATE,
    follow_up_email VARCHAR(100),
    last_contact_date DATE,
    interview_date DATETIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (status_id) REFERENCES application_statuses(id),
    INDEX idx_user_status (user_id, status_id),
    INDEX idx_user_company (user_id, company_id),
    INDEX idx_application_date (application_date),
    INDEX idx_follow_up_date (follow_up_date),
    INDEX idx_interview_date (interview_date),
    INDEX idx_job_type (job_type),
    INDEX idx_priority (priority)
);

-- Authentication Tokens Table
CREATE TABLE auth_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    selector VARCHAR(255) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_selector (selector)
);

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for cleanup queries
CREATE INDEX idx_user_expires ON password_reset_tokens(user_id, expires_at);