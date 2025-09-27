-- Recognition System Database Schema
-- Run this SQL script to create the necessary tables for the recognition system

-- Create recognition_categories table
CREATE TABLE IF NOT EXISTS `recognition_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-star',
  `color` varchar(20) DEFAULT '#d37a15',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create recognitions table
CREATE TABLE IF NOT EXISTS `recognitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_employee_id` int(11) NOT NULL,
  `to_employee_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `recognition_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `from_employee_id` (`from_employee_id`),
  KEY `to_employee_id` (`to_employee_id`),
  KEY `category_id` (`category_id`),
  FOREIGN KEY (`from_employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`to_employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `recognition_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create recognition_likes table for social features
CREATE TABLE IF NOT EXISTS `recognition_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recognition_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`recognition_id`, `employee_id`),
  KEY `recognition_id` (`recognition_id`),
  KEY `employee_id` (`employee_id`),
  FOREIGN KEY (`recognition_id`) REFERENCES `recognitions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default recognition categories
INSERT INTO `recognition_categories` (`name`, `description`, `icon`, `color`) VALUES
('Teamwork', 'Recognizing collaborative efforts and team spirit', 'fas fa-users', '#28a745'),
('Innovation', 'Acknowledging creative solutions and new ideas', 'fas fa-lightbulb', '#17a2b8'),
('Leadership', 'Recognizing leadership qualities and guidance', 'fas fa-crown', '#ffc107'),
('Excellence', 'Acknowledging outstanding performance and quality work', 'fas fa-trophy', '#dc3545'),
('Support', 'Recognizing helpfulness and support to colleagues', 'fas fa-hands-helping', '#6f42c1'),
('Achievement', 'Celebrating milestones and accomplishments', 'fas fa-medal', '#fd7e14'),
('Customer Service', 'Recognizing excellent customer interactions', 'fas fa-smile', '#20c997'),
('Problem Solving', 'Acknowledging effective problem-solving skills', 'fas fa-puzzle-piece', '#6c757d');

-- Insert sample recognitions (optional - for testing)
INSERT INTO `recognitions` (`from_employee_id`, `to_employee_id`, `category_id`, `title`, `message`, `recognition_date`) VALUES
(1, 2, 1, 'Great Team Collaboration', 'Thank you for your excellent teamwork on the recent project. Your collaboration made all the difference!', '2024-01-15 10:30:00'),
(2, 3, 4, 'Outstanding Performance', 'Your attention to detail and dedication to quality is truly impressive. Keep up the excellent work!', '2024-01-16 14:20:00'),
(3, 1, 2, 'Innovative Solution', 'Your creative approach to solving the technical challenge was brilliant. Thank you for thinking outside the box!', '2024-01-17 09:15:00');

