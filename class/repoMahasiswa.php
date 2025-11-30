<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/mahasiswaEntity.php';

class RepoMahasiswa {
    private $conn;
    private $table_name = "mahasiswa";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // CREATE - Tambah data mahasiswa
    public function create(Mahasiswa $mahasiswa) {
        $errors = $mahasiswa->validate();
        if (!empty($errors)) {
            throw new Exception(implode(", ", $errors));
        }

        if ($this->isNimExists($mahasiswa->nim)) {
            throw new Exception("NIM sudah terdaftar");
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (nim, nama, prodi, angkatan, foto, keterangan) 
                  VALUES (:nim, :nama, :prodi, :angkatan, :foto, :keterangan)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nim', $mahasiswa->nim);
        $stmt->bindParam(':nama', $mahasiswa->nama);
        $stmt->bindParam(':prodi', $mahasiswa->prodi);
        $stmt->bindParam(':angkatan', $mahasiswa->angkatan);
        $stmt->bindParam(':foto', $mahasiswa->foto);
        $stmt->bindParam(':keterangan', $mahasiswa->keterangan);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ - Ambil semua data mahasiswa
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY angkatan DESC, nim";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Ambil data by NIM
    public function getByNim($nim) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nim = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $nim);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Mahasiswa(
                $row['nim'],
                $row['nama'],
                $row['prodi'],
                $row['angkatan'],
                $row['foto'],
                $row['keterangan']
            );
        }
        return null;
    }

    // UPDATE - Update data mahasiswa
    public function update($nim_lama, Mahasiswa $mahasiswa, $delete_old_photo, $old_photo) {
    $errors = $mahasiswa->validate();
    if (!empty($errors)) {
        throw new Exception(implode(", ", $errors));
    }

    if ($this->isNimExists($mahasiswa->nim, $nim_lama)) {
        throw new Exception("NIM sudah terdaftar");
    }

    $query = "UPDATE " . $this->table_name . " 
              SET nim = :nim, nama = :nama, prodi = :prodi, angkatan = :angkatan, 
                  foto = :foto, keterangan = :keterangan 
              WHERE nim = :nim_lama";
    
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':nim', $mahasiswa->nim);
    $stmt->bindParam(':nama', $mahasiswa->nama);
    $stmt->bindParam(':prodi', $mahasiswa->prodi);
    $stmt->bindParam(':angkatan', $mahasiswa->angkatan);
    $stmt->bindParam(':foto', $mahasiswa->foto);
    $stmt->bindParam(':keterangan', $mahasiswa->keterangan);
    $stmt->bindParam(':nim_lama', $nim_lama);

    if ($stmt->execute()) {
    // ✅ PERBAIKAN: Tambahkan debug dan pastikan kondisi benar
    error_log("🔄 Checking photo deletion:");
    error_log("Delete flag: " . ($delete_old_photo ? 'TRUE' : 'FALSE'));
    error_log("Old photo: " . $old_photo);
    error_log("New photo: " . $mahasiswa->foto);

    if ($delete_old_photo && !empty($old_photo) && $old_photo != $mahasiswa->foto) {
            error_log("🗑️ Deleting old photo: " . $old_photo);
            $this->deletePhotoFile($old_photo);
        } else {
            error_log("⏩ Skipping photo deletion");
        }
        return true;
    }
    return false;
    }

    // DELETE - Hapus data mahasiswa
    public function delete($nim) {
        $data = $this->getByNim($nim);
        $photo_file = $data ? $data->foto : null;

        $query = "DELETE FROM " . $this->table_name . " WHERE nim = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $nim);
        
        if ($stmt->execute()) {
            if($photo_file){
                $this->deletePhotoFile($photo_file);
            }
            return true;
        }
        return false;
    }

    // Validasi - Cek apakah NIM sudah ada
    public function isNimExists($nim, $excludeNim = null) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE nim = ?";
        $params = [$nim];
        
        if ($excludeNim) {
            $query .= " AND nim != ?";
            $params[] = $excludeNim;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

private function deletePhotoFile($filename) {
    if (!empty($filename)) {
        // ✅ PERBAIKAN: Pastikan path sesuai dengan lokasi uploads Anda
        $file_path = __DIR__ . '/../public/uploads/' . $filename;
        
        error_log("🔍 Checking file: " . $file_path);
        error_log("File exists: " . (file_exists($file_path) ? 'YES' : 'NO'));
        
        if (file_exists($file_path) && is_file($file_path)) {
            $result = unlink($file_path);
            error_log("🗑️ Delete result: " . ($result ? 'SUCCESS' : 'FAILED'));
            return $result;
        } else {
            error_log("❌ File not found or not a file: " . $file_path);
        }  
    } else {
        error_log("❌ Empty filename provided");
        }
    return false;
    }
}

?>