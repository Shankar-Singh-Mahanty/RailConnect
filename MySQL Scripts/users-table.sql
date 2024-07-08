-- Table Creation

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    contact VARCHAR(10) NOT NULL CHECK (LENGTH(contact) = 10 AND contact REGEXP '^[0-9]{10}$'),
    address VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    role ENUM('admin', 'dealer') NOT NULL
);

-- Insert Data

INSERT INTO users (username, email, password, contact, address, created_at, role) VALUES 
('admin1', 'admin1@gmail.com', SHA2('pass001', 256), '8260529733', 'Bhubaneswar', CURRENT_TIMESTAMP, 'admin'),
('admin2', 'admin2@gmail.com', SHA2('pass002', 256), '9437562924', 'Ghatika', CURRENT_TIMESTAMP, 'admin'),

('dealer1', 'dealer1@gmail.com', SHA2('pass01', 256), '6371929991', 'Kenjhor', CURRENT_TIMESTAMP, 'dealer'),
('dealer2', 'dealer2@gmail.com', SHA2('pass02', 256), '8895821654', 'Cuttack', CURRENT_TIMESTAMP, 'dealer');

-- Describe table
DESC users;

-- View all the records of the table
SELECT * FROM users;
