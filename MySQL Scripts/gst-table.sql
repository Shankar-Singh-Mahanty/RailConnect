-- Create table

CREATE TABLE gst (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cgst_percentage DECIMAL(5, 2) NOT NULL,
    sgst_percentage DECIMAL(5, 2) NOT NULL,
    effective_date DATE NOT NULL
);

-- Insert Data

INSERT INTO gst (cgst_percentage, sgst_percentage, effective_date) VALUES
(9.00, 9.00, '2024-07-15'),
(9.00, 9.00, '2024-07-15');


-- Describe table
DESC gst;

-- View all the records of the table
SELECT * FROM gst;