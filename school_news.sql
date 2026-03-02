-- Create database
CREATE DATABASE IF NOT EXISTS school_news;
USE school_news;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,   -- hashed password
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123) – hash: $2y$10$YourHashHere
-- You must generate a real hash using password_hash('admin123', PASSWORD_DEFAULT)
-- For simplicity, we'll include a precomputed hash (use your own in production)
INSERT INTO users (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@school.com');
-- NOTE: The above hash is for 'password' (example). Replace with your own!

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample categories
INSERT INTO categories (name, description) VALUES
('Events', 'School events and celebrations'),
('Announcements', 'Important announcements for students and parents'),
('Sports', 'News about sports activities');

-- News table
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category_id INT,
    image VARCHAR(255),          -- path to uploaded image
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Sample news
INSERT INTO news (title, content, category_id, image) VALUES
('Annual Day Celebration', 'The annual day function will be held on 15th March...', 1, NULL),
('Winter Break Notice', 'School will remain closed from 25th Dec to 5th Jan.', 2, NULL);