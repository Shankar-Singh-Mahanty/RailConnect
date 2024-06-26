CREATE TABLE CUGDetails (
    cug_id INT AUTO_INCREMENT PRIMARY KEY,
    cug_number BIGINT NOT NULL,
    emp_number BIGINT UNIQUE NOT NULL,
    empname VARCHAR(100) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    bill_unit_no VARCHAR(50) NOT NULL,
    allocation DECIMAL(10, 2) NOT NULL,
    operator VARCHAR(50) NOT NULL,
    plan ENUM('A', 'B', 'C') NOT NULL,
    status ENUM('Active', 'Inactive') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT CHK_allocation CHECK (allocation >= 0),
    CONSTRAINT CHK_cug_number_length CHECK (CHAR_LENGTH(cug_number) = 11),
    CONSTRAINT CHK_emp_number_length CHECK (CHAR_LENGTH(emp_number) = 12)
);

DESC CUGDetails;

SELECT * FROM CUGDetails;
