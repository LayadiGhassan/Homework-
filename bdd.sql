CREATE DATABASE bmi_calculator;
USE bmi_calculator;

CREATE TABLE bmi_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    weight FLOAT NOT NULL,
    height FLOAT NOT NULL,
    bmi FLOAT NOT NULL,
    interpretation VARCHAR(50) NOT NULL
);
