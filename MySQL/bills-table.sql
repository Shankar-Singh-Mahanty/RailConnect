-- Table Creation

CREATE TABLE bills (
    bill_id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cug_id INT NOT NULL,
    periodic_charge DECIMAL(10, 2) NOT NULL,
    usage_amount DECIMAL(10, 2) NOT NULL,
    data_amount DECIMAL(10, 2) NOT NULL,
    voice DECIMAL(10, 2) NOT NULL,
    video DECIMAL(10, 2) NOT NULL,
    sms DECIMAL(10, 2) NOT NULL,
    vas DECIMAL(10, 2) NOT NULL,
    bill_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cug_id) REFERENCES cugdetails(cug_id)
);

-- Insert Data

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 550.00, 250.00, 120.00, 60.00, 35.00, 25.00, 15.00
FROM cugdetails c
WHERE c.cug_number = 10147857981 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 600.00, 220.00, 110.00, 55.00, 32.00, 22.00, 12.00
FROM cugdetails c
WHERE c.cug_number = 10147857211 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 480.00, 180.00, 90.00, 45.00, 28.00, 18.00, 18.00
FROM cugdetails c
WHERE c.cug_number = 10147857982 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 510.00, 190.00, 95.00, 48.00, 29.00, 19.00, 19.00
FROM cugdetails c
WHERE c.cug_number = 10147857985 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 530.00, 210.00, 105.00, 52.00, 31.00, 21.00, 11.00
FROM cugdetails c
WHERE c.cug_number = 10147857991 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 470.00, 160.00, 80.00, 40.00, 25.00, 15.00, 15.00
FROM cugdetails c
WHERE c.cug_number = 10147857993 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 580.00, 230.00, 115.00, 57.00, 33.00, 23.00, 13.00
FROM cugdetails c
WHERE c.cug_number = 10147857202 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 560.00, 240.00, 120.00, 60.00, 35.00, 25.00, 15.00
FROM cugdetails c
WHERE c.cug_number = 10147857201 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 490.00, 200.00, 100.00, 50.00, 30.00, 20.00, 10.00
FROM cugdetails c
WHERE c.cug_number = 10147857195 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 520.00, 210.00, 105.00, 52.00, 31.00, 21.00, 11.00
FROM cugdetails c
WHERE c.cug_number = 10147857987 AND c.status = 'Active';

INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas)
SELECT c.cug_id, 500.00, 200.00, 100.00, 50.00, 30.00, 20.00, 10.00
FROM cugdetails c
WHERE c.cug_number = 9677142604 AND c.status = 'Active';
