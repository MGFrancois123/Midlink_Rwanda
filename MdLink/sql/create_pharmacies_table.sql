-- Create pharmacies table
CREATE TABLE IF NOT EXISTS pharmacies (
    pharmacy_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    license_number VARCHAR(100) NOT NULL UNIQUE,
    contact_person VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    location TEXT NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (pharmacy_id),
    INDEX idx_name (name),
    INDEX idx_license (license_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample pharmacies
INSERT INTO pharmacies (name, license_number, contact_person, contact_phone, location, status) VALUES
('Ineza Pharmacy', 'RL-2024-001', 'Dr. Jean Bosco', '+250 788 123 456', 'Kigali, Gasabo District, KG 123 St', 'active'),
('Keza Pharmacy', 'RL-2024-002', 'Dr. Marie Claire', '+250 789 987 654', 'Kigali, Kicukiro District, KG 456 St', 'active'),
('Urumuri Pharmacy', 'RL-2024-003', 'Dr. Paul Kagame', '+250 787 555 123', 'Kigali, Nyarugenge District, KG 789 St', 'active');

