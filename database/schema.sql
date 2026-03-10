-- Cow Farm Management System - Database Schema
-- Run this SQL file in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS cow_farm_db;
USE cow_farm_db;

-- Users table for authentication and role-based access
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'vet', 'manager', 'staff') DEFAULT 'staff',
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cow profiles table
CREATE TABLE IF NOT EXISTS cows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100),
    breed VARCHAR(50),
    date_of_birth DATE,
    gender ENUM('male', 'female') NOT NULL,
    weight DECIMAL(8,2),
    color VARCHAR(50),
    photo VARCHAR(255),
    sire_tag VARCHAR(50),
    dam_tag VARCHAR(50),
    purchase_date DATE,
    purchase_price DECIMAL(10,2),
    status ENUM('active', 'sold', 'deceased', 'transferred') DEFAULT 'active',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Health records table
CREATE TABLE IF NOT EXISTS health_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cow_id INT NOT NULL,
    record_date DATE NOT NULL,
    record_type ENUM('checkup', 'treatment', 'surgery', 'injury', 'other') NOT NULL,
    diagnosis TEXT,
    treatment TEXT,
    medication VARCHAR(255),
    dosage VARCHAR(100),
    vet_name VARCHAR(100),
    cost DECIMAL(10,2),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cow_id) REFERENCES cows(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vaccination records table
CREATE TABLE IF NOT EXISTS vaccinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cow_id INT NOT NULL,
    vaccine_name VARCHAR(100) NOT NULL,
    vaccination_date DATE NOT NULL,
    next_due_date DATE,
    batch_number VARCHAR(50),
    administered_by VARCHAR(100),
    cost DECIMAL(10,2),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cow_id) REFERENCES cows(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Breeding records table
CREATE TABLE IF NOT EXISTS breeding_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cow_id INT NOT NULL,
    breeding_type ENUM('AI', 'natural', 'embryo') NOT NULL,
    breeding_date DATE NOT NULL,
    bull_tag VARCHAR(50),
    ai_technician VARCHAR(100),
    expected_calving_date DATE,
    actual_calving_date DATE,
    pregnancy_status ENUM('pregnant', 'not_pregnant', 'aborted', 'delivered') DEFAULT 'pregnant',
    calving_notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cow_id) REFERENCES cows(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Milk production logs table
CREATE TABLE IF NOT EXISTS milk_production (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cow_id INT NOT NULL,
    production_date DATE NOT NULL,
    session ENUM('morning', 'evening', 'both') DEFAULT 'both',
    morning_yield DECIMAL(8,2),
    evening_yield DECIMAL(8,2),
    total_yield DECIMAL(8,2),
    quality_grade VARCHAR(20),
    temperature DECIMAL(5,2),
    notes TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cow_id) REFERENCES cows(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Feed types table
CREATE TABLE IF NOT EXISTS feed_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    unit VARCHAR(20) DEFAULT 'kg',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Feed inventory table
CREATE TABLE IF NOT EXISTS feed_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feed_type_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2),
    purchase_date DATE,
    expiry_date DATE,
    supplier VARCHAR(100),
    batch_number VARCHAR(50),
    status ENUM('available', 'low_stock', 'out_of_stock', 'expired') DEFAULT 'available',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (feed_type_id) REFERENCES feed_types(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Feed consumption logs table
CREATE TABLE IF NOT EXISTS feed_consumption (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cow_id INT,
    feed_type_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    consumption_date DATE NOT NULL,
    notes TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cow_id) REFERENCES cows(id) ON DELETE SET NULL,
    FOREIGN KEY (feed_type_id) REFERENCES feed_types(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expense_date DATE NOT NULL,
    category ENUM('feed', 'medicine', 'equipment', 'labor', 'utilities', 'maintenance', 'other') NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    vendor VARCHAR(100),
    payment_method ENUM('cash', 'bank', 'check', 'other') DEFAULT 'cash',
    receipt_number VARCHAR(50),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sales table (milk sales)
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_date DATE NOT NULL,
    customer_name VARCHAR(100),
    milk_quantity DECIMAL(8,2) NOT NULL,
    unit_price DECIMAL(8,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('paid', 'pending', 'partial') DEFAULT 'paid',
    payment_method ENUM('cash', 'bank', 'check', 'other') DEFAULT 'cash',
    invoice_number VARCHAR(50),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Appointments/Vet visits table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_date DATE NOT NULL,
    appointment_time TIME,
    cow_id INT,
    vet_name VARCHAR(100),
    purpose VARCHAR(255),
    status ENUM('scheduled', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cow_id) REFERENCES cows(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50),
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('admin', 'admin@cowfarm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Insert sample feed types
INSERT INTO feed_types (name, category, unit) VALUES
('Hay', 'Forage', 'kg'),
('Silage', 'Forage', 'kg'),
('Corn', 'Grain', 'kg'),
('Soybean Meal', 'Protein', 'kg'),
('Mineral Mix', 'Supplement', 'kg'),
('Salt Lick', 'Supplement', 'piece');

-- Insert sample cows (20 cows) - Tamil Nadu / India context
INSERT INTO cows (tag_number, name, breed, date_of_birth, gender, weight, color, status) VALUES
('TN001', 'Kaveri', 'Kangayam', '2020-03-15', 'female', 430.00, 'Grey', 'active'),
('TN002', 'Vaigai', 'Kangayam', '2019-07-22', 'female', 450.00, 'Grey', 'active'),
('TN003', 'Bhavani', 'Hallikar', '2020-01-10', 'female', 410.00, 'Brown', 'active'),
('TN004', 'Amudha', 'HF Cross', '2019-11-05', 'female', 520.00, 'Black and White', 'active'),
('TN005', 'Selvi', 'Jersey Cross', '2020-05-18', 'female', 400.00, 'Brown', 'active'),
('TN006', 'Meenakshi', 'Sahiwal', '2018-09-12', 'female', 480.00, 'Reddish Brown', 'active'),
('TN007', 'Lakshmi', 'Gir', '2020-02-28', 'female', 460.00, 'Red and White', 'active'),
('TN008', 'Abirami', 'Kangayam', '2019-12-15', 'female', 440.00, 'Grey', 'active'),
('TN009', 'Nandhini', 'HF Cross', '2020-04-20', 'female', 530.00, 'Black and White', 'active'),
('TN010', 'Ponni', 'Kangayam', '2019-08-30', 'female', 445.00, 'Grey', 'active'),
('TN011', 'Anjali', 'Jersey Cross', '2020-06-10', 'female', 395.00, 'Brown', 'active'),
('TN012', 'Malar', 'HF Cross', '2018-10-25', 'female', 540.00, 'Black and White', 'active'),
('TN013', 'Dharani', 'Sahiwal', '2020-01-05', 'female', 420.00, 'Reddish Brown', 'active'),
('TN014', 'Yamuna', 'Kangayam', '2019-11-20', 'female', 435.00, 'Grey', 'active'),
('TN015', 'Sandhya', 'Gir', '2020-03-08', 'female', 455.00, 'Red and White', 'active'),
('TN016', 'Revathi', 'Jersey Cross', '2019-09-14', 'female', 405.00, 'Brown', 'active'),
('TN017', 'Shanthi', 'Kangayam', '2020-05-25', 'female', 438.00, 'Grey', 'active'),
('TN018', 'Karpagam', 'HF Cross', '2018-12-18', 'female', 525.00, 'Black and White', 'active'),
('TN019', 'Padma', 'Sahiwal', '2020-02-12', 'female', 415.00, 'Reddish Brown', 'active'),
('TN020', 'Devi', 'Kangayam', '2019-10-08', 'female', 432.00, 'Grey', 'active');

-- Insert sample users (Tamil Nadu names)
INSERT INTO users (username, email, password, full_name, role, phone, status) VALUES
('vet1', 'vet@cowfarm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Karthik Kumar', 'vet', '+91-98430-00001', 'active'),
('manager1', 'manager@cowfarm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Priya Ramesh', 'manager', '+91-98430-00002', 'active'),
('staff1', 'staff@cowfarm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Suresh Babu', 'staff', '+91-98430-00003', 'active');

-- Insert sample milk production records
INSERT INTO milk_production (cow_id, production_date, session, morning_yield, evening_yield, total_yield, recorded_by) VALUES
(1, CURDATE(), 'both', 12.5, 10.8, 23.3, 1),
(2, CURDATE(), 'both', 14.2, 12.5, 26.7, 1),
(3, CURDATE(), 'both', 8.5, 7.2, 15.7, 1),
(4, CURDATE(), 'both', 15.0, 13.8, 28.8, 1),
(5, CURDATE(), 'both', 7.8, 6.5, 14.3, 1),
(1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'both', 12.0, 10.5, 22.5, 1),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'both', 14.0, 12.0, 26.0, 1);

-- Insert sample vaccinations
INSERT INTO vaccinations (cow_id, vaccine_name, vaccination_date, next_due_date, administered_by) VALUES
(1, 'FMD Vaccine', DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_ADD(CURDATE(), INTERVAL 335 DAY), 'Dr. John Smith'),
(2, 'FMD Vaccine', DATE_SUB(CURDATE(), INTERVAL 25 DAY), DATE_ADD(CURDATE(), INTERVAL 340 DAY), 'Dr. John Smith'),
(3, 'Brucellosis', DATE_SUB(CURDATE(), INTERVAL 60 DAY), NULL, 'Dr. John Smith');

-- Insert sample breeding records
INSERT INTO breeding_records (cow_id, breeding_type, breeding_date, expected_calving_date, pregnancy_status) VALUES
(1, 'AI', DATE_SUB(CURDATE(), INTERVAL 120 DAY), DATE_ADD(CURDATE(), INTERVAL 165 DAY), 'pregnant'),
(2, 'AI', DATE_SUB(CURDATE(), INTERVAL 90 DAY), DATE_ADD(CURDATE(), INTERVAL 195 DAY), 'pregnant'),
(4, 'natural', DATE_SUB(CURDATE(), INTERVAL 200 DAY), DATE_ADD(CURDATE(), INTERVAL 85 DAY), 'pregnant');

-- Insert sample expenses (Indian context, INR)
INSERT INTO expenses (expense_date, category, description, amount, vendor, payment_method) VALUES
(CURDATE(), 'feed', 'Dry fodder purchase', 5000.00, 'Coimbatore Feed Centre', 'bank'),
(DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'medicine', 'FMD & HS vaccines', 1200.00, 'Madurai Vet Pharmacy', 'cash'),
(DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'labor', 'Monthly staff wages', 30000.00, 'Farm Payroll', 'bank');

-- Insert sample sales (Indian customers, INR)
INSERT INTO sales (sale_date, customer_name, milk_quantity, unit_price, total_amount, payment_status) VALUES
(CURDATE(), 'Coimbatore Aavin Depot', 500.00, 45.00, 22500.00, 'paid'),
(DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Pollachi Local Dairy', 300.00, 45.00, 13500.00, 'paid');

-- Insert sample appointments (Tamil Nadu vet)
INSERT INTO appointments (appointment_date, appointment_time, cow_id, vet_name, purpose, status) VALUES
(DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', 1, 'Dr. Karthik Kumar', 'Pregnancy check (Kangayam)', 'scheduled'),
(DATE_ADD(CURDATE(), INTERVAL 5 DAY), '14:00:00', 4, 'Dr. Karthik Kumar', 'Pre-calving examination', 'scheduled');

