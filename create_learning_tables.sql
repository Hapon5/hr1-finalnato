-- Learning Management System Database Tables
-- Run this SQL script to create the necessary tables

-- Create courses table
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in hours',
  `instructor` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create course_enrollments table
CREATE TABLE IF NOT EXISTS `course_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `completion_date` timestamp NULL DEFAULT NULL,
  `status` enum('enrolled','in_progress','completed','dropped') DEFAULT 'enrolled',
  `progress_percentage` int(3) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create course_materials table
CREATE TABLE IF NOT EXISTS `course_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `material_type` enum('video','document','quiz','assignment') DEFAULT 'document',
  `order_index` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create learning_achievements table
CREATE TABLE IF NOT EXISTS `learning_achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `achievement_type` varchar(100) NOT NULL,
  `achievement_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `earned_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample courses
INSERT INTO `courses` (`title`, `description`, `category`, `duration`, `instructor`) VALUES
('Workplace Safety Fundamentals', 'Comprehensive training on workplace safety protocols, emergency procedures, and hazard identification to ensure a safe working environment for all employees.', 'Safety & Compliance', 8, 'Sarah Johnson'),
('Effective Communication Skills', 'Learn essential communication techniques for professional environments, including active listening, conflict resolution, and presentation skills.', 'Professional Development', 6, 'Mike Wilson'),
('Project Management Essentials', 'Master the fundamentals of project management including planning, execution, monitoring, and team leadership techniques.', 'Management', 12, 'Emily Davis'),
('Cybersecurity Awareness', 'Understand cybersecurity threats, best practices for data protection, and how to maintain secure digital practices in the workplace.', 'IT & Security', 4, 'David Brown'),
('Leadership & Team Building', 'Develop leadership skills and learn effective team building strategies to enhance team performance and collaboration.', 'Leadership', 10, 'John Smith'),
('Customer Service Excellence', 'Learn how to provide exceptional customer service, handle difficult situations, and build strong customer relationships.', 'Customer Service', 5, 'Lisa Anderson'),
('Time Management & Productivity', 'Master time management techniques, productivity tools, and strategies to maximize efficiency in your daily work.', 'Productivity', 3, 'Robert Taylor'),
('Diversity & Inclusion Training', 'Understand the importance of diversity and inclusion in the workplace and learn how to create an inclusive environment.', 'HR & Culture', 4, 'Maria Garcia');

-- Insert sample course materials
INSERT INTO `course_materials` (`course_id`, `title`, `description`, `material_type`, `order_index`) VALUES
(1, 'Safety Introduction Video', 'Introduction to workplace safety principles', 'video', 1),
(1, 'Safety Manual PDF', 'Complete safety manual and procedures', 'document', 2),
(1, 'Safety Quiz', 'Test your knowledge of safety protocols', 'quiz', 3),
(2, 'Communication Basics Video', 'Fundamentals of effective communication', 'video', 1),
(2, 'Communication Workbook', 'Interactive exercises for communication skills', 'document', 2),
(3, 'Project Planning Guide', 'Step-by-step project planning methodology', 'document', 1),
(3, 'Project Management Templates', 'Ready-to-use project management templates', 'document', 2),
(4, 'Cybersecurity Awareness Video', 'Understanding cyber threats and protection', 'video', 1),
(4, 'Security Best Practices Guide', 'Comprehensive security guidelines', 'document', 2);


