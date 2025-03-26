-- Create database
CREATE DATABASE IF NOT EXISTS prime_opinion;
USE prime_opinion;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    country VARCHAR(50),
    date_of_birth DATE,
    gender VARCHAR(20),
    balance DECIMAL(10,2) DEFAULT 0.00,
    signup_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    verification_token VARCHAR(100),
    is_verified BOOLEAN DEFAULT FALSE
);

-- Survey categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    icon VARCHAR(50)
);

-- Surveys table
CREATE TABLE IF NOT EXISTS surveys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    reward DECIMAL(10,2) NOT NULL,
    duration_minutes INT,
    total_questions INT,
    min_age INT DEFAULT 18,
    max_age INT DEFAULT 99,
    countries VARCHAR(255), -- Comma-separated list of eligible countries
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Questions table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'checkbox', 'text', 'rating', 'dropdown') NOT NULL,
    required BOOLEAN DEFAULT TRUE,
    display_order INT,
    FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE
);

-- Question options (for multiple choice, checkbox, dropdown)
CREATE TABLE IF NOT EXISTS question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    display_order INT,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- User survey responses
CREATE TABLE IF NOT EXISTS survey_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    survey_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    status ENUM('started', 'completed', 'disqualified', 'abandoned') DEFAULT 'started',
    reward_paid BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, survey_id)
);

-- Individual question responses
CREATE TABLE IF NOT EXISTS question_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_response_id INT NOT NULL,
    question_id INT NOT NULL,
    response_text TEXT,
    FOREIGN KEY (survey_response_id) REFERENCES survey_responses(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Multiple choice/checkbox responses (stores selected options)
CREATE TABLE IF NOT EXISTS option_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_response_id INT NOT NULL,
    option_id INT NOT NULL,
    FOREIGN KEY (question_response_id) REFERENCES question_responses(id) ON DELETE CASCADE,
    FOREIGN KEY (option_id) REFERENCES question_options(id) ON DELETE CASCADE
);

-- Payment methods
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    min_payout DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    icon VARCHAR(50)
);

-- User payment requests
CREATE TABLE IF NOT EXISTS payment_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_details VARCHAR(255) NOT NULL, -- e.g., PayPal email, bank account
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    status ENUM('pending', 'approved', 'rejected', 'paid') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, description, icon) VALUES
('Lifestyle', 'Surveys about your daily habits and preferences', 'lifestyle'),
('Technology', 'Surveys about gadgets, apps, and tech usage', 'tech'),
('Food & Drink', 'Surveys about food preferences and dining habits', 'food'),
('Health', 'Surveys about health, wellness, and fitness', 'health'),
('Entertainment', 'Surveys about movies, music, and media', 'entertainment'),
('Shopping', 'Surveys about shopping habits and preferences', 'shopping'),
('Travel', 'Surveys about travel experiences and preferences', 'travel'),
('Finance', 'Surveys about financial habits and services', 'finance');

-- Insert payment methods
INSERT INTO payment_methods (name, description, min_payout, is_active, icon) VALUES
('bKash', 'Receive money directly to your bKash account', 500.00, TRUE, 'bkash'),
('Nagad', 'Withdraw your earnings to Nagad', 500.00, TRUE, 'nagad'),
('Rocket', 'Get paid via Rocket mobile banking', 500.00, TRUE, 'rocket'),
('Bank Transfer', 'Direct transfer to your bank account', 1000.00, TRUE, 'bank'),
('Gift Cards', 'Exchange for popular gift cards', 500.00, TRUE, 'gift');

-- Insert sample surveys
INSERT INTO surveys (title, description, category_id, reward, duration_minutes, total_questions, countries, expires_at) VALUES
('Shopping Habits Survey', 'Tell us about your shopping preferences and habits', 6, 500.00, 10, 15, 'Bangladesh,India,Pakistan', DATE_ADD(NOW(), INTERVAL 30 DAY)),
('Smartphone Usage', 'Share your experience with mobile devices and apps', 2, 450.00, 8, 12, 'Bangladesh,India,Nepal,Sri Lanka', DATE_ADD(NOW(), INTERVAL 15 DAY)),
('Food Delivery Experience', 'Rate your experience with food delivery services', 3, 350.00, 5, 8, 'Bangladesh', DATE_ADD(NOW(), INTERVAL 45 DAY)),
('Financial Services Feedback', 'Help us improve banking and payment services', 8, 600.00, 15, 20, 'Bangladesh,India', DATE_ADD(NOW(), INTERVAL 60 DAY)),
('Entertainment Streaming Preferences', 'Tell us about your favorite streaming services', 5, 400.00, 7, 10, 'Bangladesh,India,Pakistan,Nepal', DATE_ADD(NOW(), INTERVAL 30 DAY));