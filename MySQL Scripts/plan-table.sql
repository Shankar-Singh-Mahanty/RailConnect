-- Create the plans table
CREATE TABLE plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(50) UNIQUE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    validity_days INT NOT NULL,
    data_per_day DECIMAL(4, 1) NOT NULL,
    talktime VARCHAR(50) NOT NULL
);

-- Insert the given plans into the plans table
INSERT INTO plans (plan_name, price, validity_days, data_per_day, talktime) VALUES
('A', 74.61, 84, 2.0, 'Unlimited'),
('B', 59.05, 56, 1.5, 'Unlimited'),
('C', 39.90, 28, 1.0, 'Unlimited');
