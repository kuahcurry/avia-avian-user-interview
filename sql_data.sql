-- ===================================================================
-- DATABASE SETUP & DUMMY DATA
-- PT Avia Avian Tbk. - Spare Parts Inventory Management System
-- ===================================================================
-- Durasi Tes: 60-90 menit
-- Tech Stack: Laravel + MySQL/MariaDB + jQuery
-- ===================================================================

-- Drop database jika sudah ada (HATI-HATI!)
DROP DATABASE IF EXISTS avia_avian_inventory;

-- Buat database baru
CREATE DATABASE avia_avian_inventory
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE avia_avian_inventory;

-- ===================================================================
-- TABLE: machines
-- ===================================================================
CREATE TABLE machines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    machine_code VARCHAR(20) UNIQUE NOT NULL,
    machine_name VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    status ENUM('active', 'maintenance', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================================
-- TABLE: spare_parts
-- ===================================================================
CREATE TABLE spare_parts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    part_code VARCHAR(50) UNIQUE NOT NULL,
    part_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    current_stock INT DEFAULT 0,
    minimum_stock INT NOT NULL,
    price DECIMAL(12,2),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================================
-- TABLE: spare_part_usage
-- ===================================================================
CREATE TABLE spare_part_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    machine_id INT NOT NULL,
    spare_part_id INT NOT NULL,
    quantity_used INT NOT NULL,
    usage_date DATE NOT NULL,
    notes VARCHAR(255),
    recorded_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE CASCADE,
    FOREIGN KEY (spare_part_id) REFERENCES spare_parts(id) ON DELETE CASCADE,
    INDEX idx_machine_date (machine_id, usage_date),
    INDEX idx_part_date (spare_part_id, usage_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================================
-- TABLE: stock_history
-- ===================================================================
CREATE TABLE stock_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    spare_part_id INT NOT NULL,
    old_stock INT,
    new_stock INT,
    reason ENUM('usage', 'purchase', 'adjustment') NOT NULL,
    quantity_changed INT NOT NULL,
    changed_by VARCHAR(100),
    changed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (spare_part_id) REFERENCES spare_parts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================================
-- INSERT DATA: MACHINES
-- ===================================================================
INSERT INTO machines (machine_code, machine_name, location, status, created_at) VALUES
('MC001', 'Mesin Press Hidrolik A', 'Hall Produksi 1', 'active', '2024-09-01 08:00:00'),
('MC002', 'Mesin CNC Milling B', 'Hall Produksi 1', 'active', '2024-09-05 10:30:00'),
('MC003', 'Mesin Welding Robot C', 'Hall Produksi 2', 'active', '2024-09-10 09:15:00'),
('MC004', 'Mesin Assembly Line D', 'Hall Produksi 2', 'maintenance', '2024-09-12 14:00:00'),
('MC005', 'Mesin Packaging E', 'Hall Pengemasan', 'active', '2024-09-15 11:30:00'),
('MC006', 'Mesin Cutting Laser F', 'Hall Produksi 3', 'active', '2024-09-20 08:45:00'),
('MC007', 'Mesin Drilling G', 'Hall Produksi 1', 'inactive', '2024-08-15 07:00:00'),
('MC008', 'Mesin Quality Control H', 'Hall Kontrol Kualitas', 'active', '2024-10-01 09:00:00');

-- ===================================================================
-- INSERT DATA: SPARE_PARTS
-- Beberapa item dengan stock kritis untuk menantang query
-- ===================================================================
INSERT INTO spare_parts (part_code, part_name, category, current_stock, minimum_stock, price, last_updated) VALUES
-- Critical Stock Items
('BP-001', 'Hydraulic Seal Kit', 'Hydraulic', 2, 10, 450000.00, '2025-10-08 14:30:00'),
('BR-002', 'Ball Bearing SKF 6205', 'Bearing', 3, 15, 250000.00, '2025-10-07 10:15:00'),
('OL-001', 'Industrial Lubricant 5L', 'Lubricant', 5, 8, 150000.00, '2025-10-08 11:45:00'),
('PN-001', 'Pneumatic Cylinder Rod', 'Pneumatic', 1, 6, 320000.00, '2025-10-06 16:20:00'),

-- Safe Stock Items
('FL-001', 'Hydraulic Filter HD', 'Filter', 25, 10, 85000.00, '2025-10-08 13:00:00'),
('BP-002', 'Piston Rod Assembly', 'Hydraulic', 18, 8, 750000.00, '2025-10-07 15:45:00'),
('BR-003', 'Roller Bearing NSK 6206', 'Bearing', 22, 12, 280000.00, '2025-10-05 09:30:00'),
('BR-004', 'Deep Groove Ball Bearing 6308', 'Bearing', 30, 15, 320000.00, '2025-10-04 14:15:00'),
('ST-001', 'Steel Belt Conveyor', 'Conveyor', 8, 4, 1200000.00, '2025-10-08 10:00:00'),
('MT-001', 'Electric Motor 2HP', 'Motor', 5, 2, 2500000.00, '2025-10-02 11:30:00'),

-- Additional items with moderate stock
('GB-001', 'Gearbox Lubricant 10L', 'Lubricant', 12, 5, 280000.00, '2025-10-08 12:00:00'),
('SR-001', 'Hydraulic Solenoid Valve', 'Solenoid', 7, 4, 580000.00, '2025-10-07 14:30:00'),
('GL-001', 'Welding Cable 50m', 'Electrical', 6, 3, 350000.00, '2025-10-06 13:15:00'),
('SP-001', 'Cutting Nozzle Assembly', 'Cutting Tool', 9, 5, 420000.00, '2025-10-05 10:45:00'),
('VP-001', 'Vacuum Pump Oil', 'Lubricant', 14, 6, 195000.00, '2025-10-08 09:30:00'),
('RB-001', 'Rubber Gasket Kit', 'Gasket', 40, 20, 95000.00, '2025-10-03 15:00:00'),
('CP-001', 'Coupling Flexible', 'Coupling', 11, 5, 380000.00, '2025-10-07 12:45:00'),
('FN-001', 'Fan Belt Motor', 'Fan', 16, 8, 160000.00, '2025-10-04 16:30:00');

-- ===================================================================
-- INSERT DATA: SPARE_PART_USAGE
-- Data penggunaan dalam 30 hari terakhir untuk mendukung analisis
-- Current date assumed: 2025-10-09
-- ===================================================================
INSERT INTO spare_part_usage (machine_id, spare_part_id, quantity_used, usage_date, notes, recorded_by) VALUES
-- September - Early October (30 days ago dari 2025-10-09)
(1, 1, 2, '2025-09-09', 'Penggantian seal terkapar panas', 'Budi Santoso'),
(2, 2, 1, '2025-09-10', 'Bearing gesekan tinggi', 'Ahmad Wijaya'),
(3, 3, 2, '2025-09-12', 'Maintenance rutin welding robot', 'Siti Nurhaliza'),
(1, 4, 1, '2025-09-15', 'Pneumatic system maintenance', 'Budi Santoso'),

-- Mid-September
(2, 5, 1, '2025-09-18', 'Filter hydraulic perlu penggantian', 'Ahmad Wijaya'),
(4, 6, 2, '2025-09-20', 'Assembly line piston replacement', 'Wawan Gunawan'),
(5, 7, 1, '2025-09-22', 'Packaging machine bearing issue', 'Rini Susanti'),
(6, 8, 2, '2025-09-24', 'Laser cutting maintenance', 'Bambang Irawan'),

-- Late September
(1, 9, 1, '2025-09-26', 'Conveyor belt replacement', 'Budi Santoso'),
(2, 10, 1, '2025-09-27', 'Motor replacement CNC', 'Ahmad Wijaya'),
(3, 11, 2, '2025-09-28', 'Welding robot gearbox service', 'Siti Nurhaliza'),
(4, 1, 3, '2025-09-29', 'Multiple seal replacement', 'Wawan Gunawan'),
(5, 2, 2, '2025-09-30', 'Packaging line bearing', 'Rini Susanti'),

-- Early October (Recent)
(1, 3, 1, '2025-10-01', 'Preventive maintenance', 'Budi Santoso'),
(2, 12, 1, '2025-10-02', 'CNC solenoid valve replacement', 'Ahmad Wijaya'),
(6, 13, 2, '2025-10-03', 'Laser machine cable replacement', 'Bambang Irawan'),
(3, 14, 1, '2025-10-04', 'Cutting nozzle wear out', 'Siti Nurhaliza'),
(1, 15, 2, '2025-10-05', 'Vacuum pump maintenance', 'Budi Santoso'),
(4, 2, 1, '2025-10-06', 'Assembly line bearing check', 'Wawan Gunawan'),

-- Very Recent (Last 3 days)
(2, 1, 2, '2025-10-07', 'CNC press urgent repair', 'Ahmad Wijaya'),
(5, 16, 1, '2025-10-07', 'Gasket replacement', 'Rini Susanti'),
(6, 17, 1, '2025-10-08', 'Coupling maintenance', 'Bambang Irawan'),
(1, 18, 2, '2025-10-08', 'Fan belt motor replacement', 'Budi Santoso'),
(3, 5, 1, '2025-10-08', 'Welding robot filter change', 'Siti Nurhaliza'),
(2, 6, 1, '2025-10-09', 'CNC piston issue', 'Ahmad Wijaya');

-- ===================================================================
-- INSERT DATA: STOCK_HISTORY
-- Tracking perubahan stock untuk setiap spare part
-- ===================================================================
INSERT INTO stock_history (spare_part_id, old_stock, new_stock, reason, quantity_changed, changed_by, changed_date) VALUES
-- Purchase history
(1, 0, 15, 'purchase', 15, 'Supplier Management', '2025-08-01 10:00:00'),
(2, 0, 20, 'purchase', 20, 'Supplier Management', '2025-08-05 09:30:00'),
(3, 0, 18, 'purchase', 18, 'Supplier Management', '2025-08-08 14:15:00'),
(4, 0, 10, 'purchase', 10, 'Supplier Management', '2025-08-10 11:00:00'),
(5, 0, 30, 'purchase', 30, 'Supplier Management', '2025-08-12 13:45:00'),
(6, 0, 25, 'purchase', 25, 'Supplier Management', '2025-08-15 10:30:00'),
(7, 0, 28, 'purchase', 28, 'Supplier Management', '2025-08-18 09:00:00'),
(8, 0, 35, 'purchase', 35, 'Supplier Management', '2025-08-20 15:30:00'),
(9, 0, 10, 'purchase', 10, 'Supplier Management', '2025-08-22 11:15:00'),
(10, 0, 6, 'purchase', 6, 'Supplier Management', '2025-08-25 14:00:00'),
(11, 0, 16, 'purchase', 16, 'Supplier Management', '2025-08-28 10:45:00'),
(12, 0, 10, 'purchase', 10, 'Supplier Management', '2025-09-01 09:30:00'),
(13, 0, 8, 'purchase', 8, 'Supplier Management', '2025-09-02 13:20:00'),
(14, 0, 12, 'purchase', 12, 'Supplier Management', '2025-09-05 11:00:00'),
(15, 0, 20, 'purchase', 20, 'Supplier Management', '2025-09-08 10:15:00'),
(16, 0, 45, 'purchase', 45, 'Supplier Management', '2025-09-10 14:30:00'),
(17, 0, 15, 'purchase', 15, 'Supplier Management', '2025-09-12 09:45:00'),
(18, 0, 20, 'purchase', 20, 'Supplier Management', '2025-09-15 11:20:00'),

-- Usage history (matches spare_part_usage table)
(1, 15, 13, 'usage', 2, 'Budi Santoso', '2025-09-09 10:00:00'),
(2, 20, 19, 'usage', 1, 'Ahmad Wijaya', '2025-09-10 11:30:00'),
(3, 18, 16, 'usage', 2, 'Siti Nurhaliza', '2025-09-12 14:45:00'),
(4, 10, 9, 'usage', 1, 'Budi Santoso', '2025-09-15 09:15:00'),
(5, 30, 29, 'usage', 1, 'Ahmad Wijaya', '2025-09-18 10:20:00'),
(6, 25, 23, 'usage', 2, 'Wawan Gunawan', '2025-09-20 13:00:00'),
(7, 28, 27, 'usage', 1, 'Rini Susanti', '2025-09-22 15:30:00'),
(8, 35, 33, 'usage', 2, 'Bambang Irawan', '2025-09-24 09:45:00'),
(9, 10, 9, 'usage', 1, 'Budi Santoso', '2025-09-26 11:15:00'),
(10, 6, 5, 'usage', 1, 'Ahmad Wijaya', '2025-09-27 14:30:00'),
(11, 16, 14, 'usage', 2, 'Siti Nurhaliza', '2025-09-28 10:00:00'),
(1, 13, 10, 'usage', 3, 'Wawan Gunawan', '2025-09-29 15:45:00'),
(2, 19, 17, 'usage', 2, 'Rini Susanti', '2025-09-30 09:30:00'),
(3, 16, 15, 'usage', 1, 'Budi Santoso', '2025-10-01 11:00:00'),
(12, 10, 9, 'usage', 1, 'Ahmad Wijaya', '2025-10-02 13:15:00'),
(13, 8, 6, 'usage', 2, 'Bambang Irawan', '2025-10-03 10:45:00'),
(14, 12, 11, 'usage', 1, 'Siti Nurhaliza', '2025-10-04 14:20:00'),
(15, 20, 18, 'usage', 2, 'Budi Santoso', '2025-10-05 09:00:00'),
(2, 17, 16, 'usage', 1, 'Wawan Gunawan', '2025-10-06 15:30:00'),
(1, 10, 8, 'usage', 2, 'Ahmad Wijaya', '2025-10-07 10:15:00'),
(16, 45, 44, 'usage', 1, 'Rini Susanti', '2025-10-07 13:45:00'),
(17, 15, 14, 'usage', 1, 'Bambang Irawan', '2025-10-08 11:30:00'),
(18, 20, 18, 'usage', 2, 'Budi Santoso', '2025-10-08 14:00:00'),
(5, 29, 28, 'usage', 1, 'Siti Nurhaliza', '2025-10-08 15:20:00'),
(6, 23, 22, 'usage', 1, 'Ahmad Wijaya', '2025-10-09 10:45:00'),

-- Stock adjustment history
(1, 8, 5, 'adjustment', -3, 'Inventory Manager', '2025-10-06 16:00:00'),
(4, 9, 1, 'adjustment', -8, 'Inventory Manager', '2025-10-08 14:30:00');

-- ===================================================================
-- CREATE INDEXES untuk performance optimization
-- ===================================================================
CREATE INDEX idx_spare_parts_category ON spare_parts(category);
CREATE INDEX idx_spare_parts_status ON spare_parts(current_stock, minimum_stock);
CREATE INDEX idx_stock_history_part ON stock_history(spare_part_id);
CREATE INDEX idx_stock_history_reason ON stock_history(reason, changed_date);
CREATE INDEX idx_stock_history_date ON stock_history(changed_date);

-- ===================================================================
-- VERIFIKASI DATA
-- ===================================================================
-- Jalankan query berikut untuk memastikan data sudah terisi dengan benar:
-- SELECT 'Machines' as table_name, COUNT(*) as total FROM machines
-- UNION ALL
-- SELECT 'Spare Parts', COUNT(*) FROM spare_parts
-- UNION ALL
-- SELECT 'Usage Records', COUNT(*) FROM spare_part_usage
-- UNION ALL
-- SELECT 'Stock History', COUNT(*) FROM stock_history;

-- ===================================================================
-- END OF SETUP SCRIPT
-- ===================================================================
-- Total data:
-- - Machines: 8 records
-- - Spare Parts: 18 records
-- - Usage Records: 26 records (untuk 30 hari terakhir)
-- - Stock History: 44 records
-- 
-- Data siap digunakan untuk test teknis. 
-- Beberapa spare parts dalam kondisi kritis untuk testing query dan logic.
-- ===================================================================