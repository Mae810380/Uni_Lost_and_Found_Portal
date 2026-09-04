CREATE DATABASE IF NOT EXISTS university_lost_found_db;
USE university_lost_found_db;

DROP TABLE IF EXISTS claim_requests;
DROP TABLE IF EXISTS lost_found_records;
DROP TABLE IF EXISTS university_accounts;

CREATE TABLE university_accounts (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Claimer','Finder','Staff','Admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE lost_found_records (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_type ENUM('Lost','Found') NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(150) NOT NULL,
    item_date DATE NOT NULL,
    status ENUM('Pending','Approved','Rejected','Returned') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES university_accounts(user_id) ON DELETE CASCADE
);

CREATE TABLE claim_requests (
    claim_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES lost_found_records(item_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES university_accounts(user_id) ON DELETE CASCADE
);

-- Demo password for all accounts: 123456
INSERT INTO university_accounts (student_id, name, email, password, role) VALUES
('22-46183-1', 'Rahim Student', 'rahim@student.aiub.edu', '$2y$12$nTJavsqO4h35.jc.nwdwAu1po8pdGOKNV9Od3xGKsQ.HRwVRX0Fhe', 'Claimer'),
('23-48210-2', 'Karim Finder', 'karim@finder.aiub.edu', '$2y$12$nTJavsqO4h35.jc.nwdwAu1po8pdGOKNV9Od3xGKsQ.HRwVRX0Fhe', 'Finder'),
('21-40001-1', 'Nusrat Staff', 'nusrat@staff.aiub.edu', '$2y$12$nTJavsqO4h35.jc.nwdwAu1po8pdGOKNV9Od3xGKsQ.HRwVRX0Fhe', 'Staff'),
('20-30001-1', 'Admin User', 'admin@aiub.edu', '$2y$12$nTJavsqO4h35.jc.nwdwAu1po8pdGOKNV9Od3xGKsQ.HRwVRX0Fhe', 'Admin');

INSERT INTO lost_found_records (user_id, item_type, item_name, category, description, location, item_date, status) VALUES
(2, 'Found', 'Black Wallet', 'Wallet', 'Black leather wallet found near the library.', 'Campus 4, Library', CURDATE(), 'Approved'),
(1, 'Lost', 'Blue Backpack', 'Bag', 'Blue backpack with university books inside.', 'Campus 4, Cafeteria', CURDATE(), 'Pending');
