CREATE DATABASE IF NOT EXISTS crud_mahasiswa;
USE crud_mahasiswa;

-- Buat tabel mahasiswa tanpa id, menggunakan NIM sebagai primary key
CREATE TABLE mahasiswa (
    nim VARCHAR(10) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    prodi ENUM('1', '2', '3') NOT NULL COMMENT '1=Sistem Komputer, 2=Teknologi Komputer, 3=Sistem Informasi',
    angkatan YEAR NOT NULL,
    foto VARCHAR(255),
    keterangan ENUM('aktif', 'tidak') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);