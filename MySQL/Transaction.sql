CREATE TABLE Transaction (
    transaction_id INT PRIMARY KEY,
    cug_number INT,
    user_id INT,
    transaction_type ENUM('Allocation', 'De-allocation'),
    amount DECIMAL(10, 2),cugdetails
    transaction_date DATE,
    FOREIGN KEY (cug_number) REFERENCES CUGDetails(cug_number),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    CONSTRAINT CHK_transaction_amount CHECK (amount >= 0),  -- Amount should be non-negative
    CONSTRAINT CHK_transaction_type CHECK (
        (transaction_type = 'Allocation' AND amount > 0) OR   -- Allocation amount must be positive
        (transaction_type = 'De-allocation' AND amount < 0)  -- De-allocation amount must be negative
    )
);

Desc Transaction;