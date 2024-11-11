USE db_plato;

-- Drop tables if they exist (to avoid errors on re-running the script)
DROP TABLE IF EXISTS `user_feedback`;
DROP TABLE IF EXISTS `donations`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `requests`;
DROP TABLE IF EXISTS `tbl_users`;

-- Create `tbl_users` table
CREATE TABLE `tbl_users` (
  `user_id` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT, 
  `user_lastname` VARCHAR(180) NOT NULL,
  `user_firstname` VARCHAR(180) NOT NULL,
  `username` VARCHAR(180) NOT NULL UNIQUE,
  `user_password` VARCHAR(255) NOT NULL,
  `user_access` ENUM('Super Admin', 'Admin', 'Secretary', 'User') NOT NULL DEFAULT 'User',
  `user_date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_date_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_status` TINYINT(1) NOT NULL DEFAULT '0',
  `user_address` VARCHAR(255) DEFAULT NULL,
  `user_city` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000001 DEFAULT CHARSET=utf8mb4;


-- Create `user_feedback` table
CREATE TABLE `user_feedback` (
    `feedback_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(8) UNSIGNED NOT NULL,  
    `feedback` TEXT NOT NULL,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `archived` TINYINT(1) DEFAULT 0,
    FOREIGN KEY (`user_id`) REFERENCES `tbl_users`(`user_id`) ON DELETE CASCADE,
    INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create `donations` table
CREATE TABLE `donations` (
    `donation_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(8) UNSIGNED NOT NULL,
    `user_firstname` VARCHAR(180) NOT NULL,
    `user_lastname` VARCHAR(180) NOT NULL,
    `amount` INT NOT NULL,
    `purpose` TEXT NOT NULL,
    `status` ENUM('pending', 'received') DEFAULT 'pending',
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `reference_number` VARCHAR(255) UNIQUE,  
    FOREIGN KEY (`user_id`) REFERENCES `tbl_users`(`user_id`) ON DELETE CASCADE,
    INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create `notifications` table
CREATE TABLE `notifications` (
    `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(8) UNSIGNED NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `tbl_users`(`user_id`) ON DELETE CASCADE,
    INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create `requests` table
CREATE TABLE `requests` (
    `request_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(8) UNSIGNED NOT NULL,
    `request_details` TEXT NOT NULL,
    `request_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `longitude` FLOAT DEFAULT NULL,
    `latitude` FLOAT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `tbl_users`(`user_id`) ON DELETE CASCADE,
    INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

