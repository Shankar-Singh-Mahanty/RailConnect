CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE operators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);


-- Insert initial data into departments table
INSERT INTO departments (name) VALUES 
('S&T'),
('ENGG'),
('ACCTS'),
('ELECT'),
('OPTG'),
('PERS'),
('SECURITY'),
('AUDIT'),
('MED'),
('COMM'),
('GA'),
('MECH'),
('SAFETY'),
('STORES'),
('RRC'),
('WAGON'),
('WELFARE');

-- Insert initial data into operators table
INSERT INTO operators (name) VALUES 
('Jio'),
('Airtel'),
('VI'),
('BSNL');

-- Insert initial data into units table
INSERT INTO units (name) VALUES 
('CON'),
('HQ'),
('MCS');
