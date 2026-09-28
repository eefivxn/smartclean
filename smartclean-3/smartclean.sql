CREATE DATABASE IF NOT EXISTS smartclean CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartclean;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT DEFAULT 3,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    order_date DATE NOT NULL,
    pickup_date DATE,
    total_price DECIMAL(10,2) NOT NULL,
    notes TEXT,
    status ENUM('menunggu', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, phone, role) VALUES
('Administrator', 'admin@shinningclean.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456789', 'admin');

INSERT INTO services (name, description, price, duration_days) VALUES
('Regular Cleaning', 'Cuci sepatu standar dengan sabun khusus dan sikat lembut. Cocok untuk perawatan rutin.', 25000, 2),
('Deep Clean', 'Pembersihan mendalam hingga ke sela-sela sepatu. Menghilangkan noda membandel.', 45000, 3),
('Premium Clean', 'Cuci + conditioning + waterproofing. Perawatan menyeluruh untuk sepatu premium.', 75000, 4),
('Repaint', 'Pengecatan ulang sepatu dengan cat khusus sepatu. Tersedia berbagai pilihan warna.', 120000, 7),
('Unyellowing', 'Menghilangkan kekuningan pada sol sepatu. Membuat sol kembali putih bersih.', 55000, 3),
('Hydran / Suede Care', 'Perawatan khusus untuk material suede dan nubuck menggunakan produk premium.', 65000, 4);
