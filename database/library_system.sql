-- Database Perpustakaan MTs Muhamadyah
-- Script SQL untuk MySQL Workbench

-- Buat database
CREATE DATABASE IF NOT EXISTS library_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Gunakan database
USE library_system;

-- Tabel books
CREATE TABLE books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(13) UNIQUE NOT NULL,
    description TEXT,
    publisher VARCHAR(255),
    publication_year INT,
    quantity INT NOT NULL DEFAULT 1,
    category VARCHAR(100),
    location VARCHAR(100),
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Tabel members
CREATE TABLE members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    birth_date DATE,
    member_id VARCHAR(20) UNIQUE NOT NULL,
    registration_date DATE NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Tabel borrowings
CREATE TABLE borrowings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('borrowed', 'returned', 'overdue') DEFAULT 'borrowed',
    notes TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Tabel users untuk authentication (jika diperlukan)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Tabel sessions untuk Laravel
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel migrations untuk Laravel
CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
);

-- Tabel failed_jobs untuk Laravel
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data untuk books
INSERT INTO books (title, author, isbn, description, publisher, publication_year, quantity, category, location, created_at, updated_at) VALUES
('Laskar Pelangi', 'Andrea Hirata', '9789793062792', 'Novel inspiratif tentang perjuangan pendidikan di Belitung', 'Bentang Pustaka', 2005, 5, 'Novel', 'Rak A-1', NOW(), NOW()),
('Bumi Manusia', 'Pramoedya Ananta Toer', '9789793062793', 'Novel sejarah Indonesia era kolonial', 'Hasta Mitra', 1980, 3, 'Novel Sejarah', 'Rak A-2', NOW(), NOW()),
('Fisika Dasar', 'Giancoli', '9789793062794', 'Buku pelajaran fisika untuk SMA', 'Erlangga', 2010, 8, 'Pendidikan', 'Rak B-1', NOW(), NOW()),
('Matematika SMA Kelas 10', 'Sukino', '9789793062795', 'Buku pelajaran matematika SMA', 'Erlangga', 2015, 10, 'Pendidikan', 'Rak B-2', NOW(), NOW()),
('Kamus Inggris-Indonesia', 'John M. Echols', '9789793062796', 'Kamus lengkap Inggris-Indonesia', 'Gramedia', 2000, 4, 'Referensi', 'Rak C-1', NOW(), NOW()),
('Sejarah Indonesia', 'Vlekke', '9789793062797', 'Sejarah lengkap Indonesia dari masa prasejarah', 'Kompas', 2008, 6, 'Sejarah', 'Rak D-1', NOW(), NOW()),
('Biologi SMA', 'Campbell', '9789793062798', 'Buku pelajaran biologi untuk SMA', 'Erlangga', 2012, 7, 'Pendidikan', 'Rak B-3', NOW(), NOW()),
('Kimia Dasar', 'Raymond Chang', '9789793062799', 'Buku pelajaran kimia untuk SMA', 'Erlangga', 2011, 6, 'Pendidikan', 'Rak B-4', NOW(), NOW()),
('Ekonomi SMA', 'Sukwiaty', '9789793062800', 'Buku pelajaran ekonomi untuk SMA', 'Erlangga', 2013, 5, 'Pendidikan', 'Rak B-5', NOW(), NOW()),
('Sosiologi SMA', 'Soerjono Soekanto', '9789793062801', 'Buku pelajaran sosiologi untuk SMA', 'Rajawali Pers', 2014, 4, 'Pendidikan', 'Rak B-6', NOW(), NOW());

-- Insert sample data untuk members
INSERT INTO members (name, email, phone, address, birth_date, member_id, registration_date, status, created_at, updated_at) VALUES
('Ahmad Rizki', 'ahmad.rizki@email.com', '081234567890', 'Jl. Merdeka No. 123, Jakarta', '1995-03-15', 'MBR001', '2024-01-15', 'active', NOW(), NOW()),
('Siti Nurhaliza', 'siti.nurhaliza@email.com', '081234567891', 'Jl. Sudirman No. 456, Bandung', '1998-07-22', 'MBR002', '2024-01-16', 'active', NOW(), NOW()),
('Budi Santoso', 'budi.santoso@email.com', '081234567892', 'Jl. Thamrin No. 789, Surabaya', '1993-11-08', 'MBR003', '2024-01-17', 'active', NOW(), NOW()),
('Dewi Sartika', 'dewi.sartika@email.com', '081234567893', 'Jl. Asia Afrika No. 321, Bandung', '1997-05-12', 'MBR004', '2024-01-18', 'active', NOW(), NOW()),
('Rudi Hermawan', 'rudi.hermawan@email.com', '081234567894', 'Jl. Gatot Subroto No. 654, Jakarta', '1994-09-30', 'MBR005', '2024-01-19', 'active', NOW(), NOW());

-- Insert sample data untuk borrowings
INSERT INTO borrowings (book_id, member_id, borrow_date, due_date, return_date, status, notes, created_at, updated_at) VALUES
(1, 1, '2024-01-20', '2024-02-20', NULL, 'borrowed', 'Pinjam untuk tugas sekolah', NOW(), NOW()),
(2, 2, '2024-01-21', '2024-02-21', NULL, 'borrowed', 'Pinjam untuk referensi', NOW(), NOW()),
(3, 3, '2024-01-22', '2024-02-22', '2024-02-15', 'returned', 'Sudah dikembalikan tepat waktu', NOW(), NOW()),
(4, 4, '2024-01-23', '2024-02-23', NULL, 'borrowed', 'Pinjam untuk belajar', NOW(), NOW()),
(5, 5, '2024-01-24', '2024-02-24', NULL, 'borrowed', 'Pinjam untuk tugas bahasa', NOW(), NOW()),
(6, 1, '2024-01-25', '2024-02-25', NULL, 'borrowed', 'Pinjam untuk referensi sejarah', NOW(), NOW()),
(7, 2, '2024-01-26', '2024-02-26', '2024-02-20', 'returned', 'Dikembalikan terlambat 6 hari', NOW(), NOW()),
(8, 3, '2024-01-27', '2024-02-27', NULL, 'borrowed', 'Pinjam untuk praktikum', NOW(), NOW()),
(9, 4, '2024-01-28', '2024-02-28', NULL, 'borrowed', 'Pinjam untuk tugas ekonomi', NOW(), NOW()),
(10, 5, '2024-01-29', '2024-02-29', NULL, 'borrowed', 'Pinjam untuk belajar sosiologi', NOW(), NOW()),
(1, 3, '2024-01-30', '2024-03-01', NULL, 'borrowed', 'Pinjam untuk dibaca lagi', NOW(), NOW()),
(2, 4, '2024-02-01', '2024-03-03', NULL, 'borrowed', 'Pinjam untuk referensi novel', NOW(), NOW()),
(3, 5, '2024-02-02', '2024-03-04', NULL, 'borrowed', 'Pinjam untuk tugas fisika', NOW(), NOW()),
(4, 1, '2024-02-03', '2024-03-05', NULL, 'borrowed', 'Pinjam untuk belajar matematika', NOW(), NOW()),
(5, 2, '2024-02-04', '2024-03-06', NULL, 'borrowed', 'Pinjam untuk tugas bahasa Inggris', NOW(), NOW());

-- Insert sample user untuk admin
INSERT INTO users (name, email, password, created_at, updated_at) VALUES
('Admin Perpustakaan', 'admin@perpustakaan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Insert migration records
INSERT INTO migrations (migration, batch) VALUES
('2024_01_01_000000_create_books_table', 1),
('2024_01_01_000000_create_members_table', 1),
('2024_01_01_000000_create_borrowings_table', 1),
('2024_01_01_000000_create_users_table', 1),
('2024_01_01_000000_create_sessions_table', 1),
('2024_01_01_000000_create_migrations_table', 1),
('2024_01_01_000000_create_failed_jobs_table', 1);

-- Buat index untuk optimasi query
CREATE INDEX idx_books_isbn ON books(isbn);
CREATE INDEX idx_books_category ON books(category);
CREATE INDEX idx_members_email ON members(email);
CREATE INDEX idx_members_member_id ON members(member_id);
CREATE INDEX idx_borrowings_book_id ON borrowings(book_id);
CREATE INDEX idx_borrowings_member_id ON borrowings(member_id);
CREATE INDEX idx_borrowings_status ON borrowings(status);
CREATE INDEX idx_borrowings_due_date ON borrowings(due_date);
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- Tampilkan data yang sudah diinsert
SELECT 'Books' as table_name, COUNT(*) as total_records FROM books
UNION ALL
SELECT 'Members' as table_name, COUNT(*) as total_records FROM members
UNION ALL
SELECT 'Borrowings' as table_name, COUNT(*) as total_records FROM borrowings
UNION ALL
SELECT 'Users' as table_name, COUNT(*) as total_records FROM users;
