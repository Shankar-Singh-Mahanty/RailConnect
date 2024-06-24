CREATE TABLE CUGDetails (
    cug_number INT(50) PRIMARY KEY NOT NULL,
    emp_number VARCHAR(50) UNIQUE NOT NULL,
    empname VARCHAR(100) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    division VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    bill_unit VARCHAR(50) NOT NULL,
    allocation DECIMAL(10, 2) NOT NULL,
    operator VARCHAR(50) NOT NULL,
    plan ENUM('A', 'B', 'C') NOT NULL,
    status ENUM('Active', 'Inactive') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT CHK_allocation CHECK (allocation >= 0),  -- Allocation should be non-negative
    CONSTRAINT CHK_cug_number_length CHECK (LENGTH(cug_number) = 11),  -- CUG number must be 11 digits
    CONSTRAINT CHK_emp_number_length CHECK (LENGTH(emp_number) = 12)  -- Employee number must be 12 digits long
);

Desc CUGDetails;
