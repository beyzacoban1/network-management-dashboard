CREATE DATABASE IF NOT EXISTS network_monitor;
USE network_monitor;

CREATE TABLE IF NOT EXISTS device_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(50),
    cpu_usage FLOAT,
    ram_usage FLOAT,
    latency INT,
    status ENUM('UP', 'DOWN'),
    open_ports VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
