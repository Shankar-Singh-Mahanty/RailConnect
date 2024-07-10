-- Table Creation

CREATE TABLE bills (
    bill_id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cug_number BIGINT NOT NULL CHECK (CHAR_LENGTH(cug_number) = 10),
    periodic_charge DECIMAL(10, 2) NOT NULL,
    usage_amount DECIMAL(10, 2) NOT NULL,
    data_amount DECIMAL(10, 2) NOT NULL,
    voice DECIMAL(10, 2) NOT NULL,
    video DECIMAL(10, 2) NOT NULL,
    sms DECIMAL(10, 2) NOT NULL,
    vas DECIMAL(10, 2) NOT NULL,
    bill_month INT NOT NULL,
    bill_year INT NOT NULL,
    FOREIGN KEY (cug_number) REFERENCES cugdetails(cug_number) ON DELETE CASCADE
);

-- Insert Data

INSERT INTO bills (cug_number, periodic_charge, usage_amount, data_amount, voice, video, sms, vas, bill_month, bill_year)
VALUES
(1014785798, 550.00, 250.00, 120.00, 60.00, 35.00, 25.00, 15.00, 1, 2024),
(9677141709, 600.00, 220.00, 110.00, 55.00, 32.00, 22.00, 12.00, 1, 2024),
(9677137458, 480.00, 180.00, 90.00, 45.00, 28.00, 18.00, 18.00, 1, 2024),
(9677141905, 510.00, 190.00, 95.00, 48.00, 29.00, 19.00, 19.00, 1, 2024),
(9677145886, 530.00, 210.00, 105.00, 52.00, 31.00, 21.00, 11.00, 1, 2024);


-- Describe table
DESC bills;

-- View all the records of the table
SELECT * FROM bills;