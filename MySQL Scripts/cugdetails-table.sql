-- Table Creation

CREATE TABLE cugdetails (
    cug_number BIGINT PRIMARY KEY CHECK (CHAR_LENGTH(cug_number) = 10),
    emp_number BIGINT UNIQUE NOT NULL CHECK (CHAR_LENGTH(emp_number) = 12),
    empname VARCHAR(100) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    bill_unit_no VARCHAR(50) NOT NULL,
    allocation VARCHAR(50) NOT NULL CHECK (allocation > 0),
    operator VARCHAR(50) NOT NULL,
    plan ENUM('A', 'B', 'C') NOT NULL,
    status CHAR(6) DEFAULT 'Active' CHECK (status = 'Active'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Insert Data

INSERT INTO cugdetails (cug_number, emp_number, empname, designation, unit, department, bill_unit_no, allocation, operator, plan, status)
VALUES
(1014785798, 365166527088, 'DALLAP KUMDR SDMDL', 'AA', 'CON', 'S & T', '3106854', 873106, 'JIO', 'C', 'Active'),
(9677141709, 339555256153, 'SPDSPA KDNT MASPRD', 'FAnCAO', 'CON', 'ENGG', '06287', 873106, 'JIO', 'C', 'Active'),
(9677137458, 362276237069, 'SUMDND MBPDNTY', 'FAnCAO/TRAFFIC', 'CON', 'ENGG', '06025', 873106, 'JIO', 'C', 'Active'),
(9677141905, 363155739851, 'Y.DNDND', 'FAnCAO/WnS', 'CON', 'S & T', '06853', 873106, 'JIO', 'C', 'Active'),
(9677145886, 365154987804, 'NARMDL CPDNDRD SDRDNGA', 'AFA', 'CON', 'ENGG', '06287', 873106, 'JIO', 'C', 'Active');

-- Describe table
DESC cugdetails;

-- View all the records of the table
SELECT * FROM cugdetails;
