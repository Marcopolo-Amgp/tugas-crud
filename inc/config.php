<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'crud_mahasiswa');
define('DB_USER', 'root');
define('DB_PASS', '');

// Konfigurasi Upload
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png']);

// Mapping Prodi
$prodiOptions = [
    '1' => 'Sistem Komputer',
    '2' => 'Teknologi Komputer', 
    '3' => 'Sistem Informasi'
];

// Konfigurasi Aplikasi
define('APP_NAME', 'Sistem CRUD Mahasiswa');
define('APP_VERSION', '1.0');

?>