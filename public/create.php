<?php
require_once '../class/repoMahasiswa.php';
require_once '../class/mahasiswaEntity.php';
require_once '../inc/config.php';

$repo = new RepoMahasiswa();
$errors = [];

if ($_POST) {
    $nim = $_POST['nim'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $prodi = $_POST['prodi'] ?? '';
    $angkatan = $_POST['angkatan'] ?? '';
    $keterangan = $_POST['keterangan'] ?? 'aktif';

    // Handle upload foto
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $filename = $_FILES['foto']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), ALLOWED_TYPES)) {
            if ($_FILES['foto']['size'] <= MAX_FILE_SIZE) {
                $foto = uniqid() . '.' . $filetype;
                $target = UPLOAD_DIR . $foto;
                
                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
                    $errors[] = "Gagal upload foto.";
                }
            } else {
                $errors[] = "Ukuran file terlalu besar. Maksimal " . (MAX_FILE_SIZE / 1024 / 1024) . "MB.";
            }
        } else {
            $errors[] = "Hanya file " . implode(', ', ALLOWED_TYPES) . " yang diizinkan.";
        }
    }

    if (empty($errors)) {
        try {
            $mahasiswa = new Mahasiswa($nim, $nama, $prodi, $angkatan, $foto, $keterangan);
            
            if ($repo->create($mahasiswa)) {
                header("Location: index.php?success=Data mahasiswa berhasil ditambahkan");
                exit;
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>➕ Tambah Mahasiswa</h1>
            <p>Tambahkan data mahasiswa baru ke dalam sistem</p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="nav-links">
                <a href="index.php" class="btn btn-secondary">← Kembali ke Dashboard</a>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="card">
                <div class="form-container">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <strong>⚠️ Terjadi Kesalahan:</strong>
                            <ul style="margin: 10px 0 0 20px;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" id="mahasiswaForm">
                        <div class="form-group">
                            <label for="nim">📋 NIM *</label>
                            <input type="text" id="nim" name="nim" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>" 
                                   required 
                                   placeholder="Contoh: 24003001"
                                   pattern="[0-9]{8}"
                                   title="Format: 8 digit angka (Tahun + Kode Prodi + Urutan)">
                            <small style="color: var(--secondary); margin-top: 5px; display: block;">
                                Format: Tahun (2 digit) + Kode Prodi (2 digit) + Urutan (3 digit)
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="nama">👤 Nama Lengkap *</label>
                            <input type="text" id="nama" name="nama" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" 
                                   required 
                                   placeholder="Masukkan nama lengkap mahasiswa">
                        </div>

                        <div class="form-group">
                            <label for="prodi">🎓 Program Studi *</label>
                            <select id="prodi" name="prodi" class="form-control" required>
                                <option value="">Pilih Program Studi</option>
                                <?php foreach ($prodiOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" 
                                        <?= (isset($_POST['prodi']) && $_POST['prodi'] == $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="angkatan">📅 Tahun Angkatan *</label>
                            <input type="number" id="angkatan" name="angkatan" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($_POST['angkatan'] ?? '') ?>" 
                                   min="2000" max="2099" 
                                   required 
                                   placeholder="Contoh: 2024">
                        </div>

                        <div class="form-group">
                            <label for="foto">🖼️ Foto Profil</label>
                            <input type="file" id="foto" name="foto" 
                                   class="form-control form-control-file" 
                                   accept="image/jpeg, image/png">
                            <small style="color: var(--secondary); margin-top: 5px; display: block;">
                                Format: <?= implode(', ', ALLOWED_TYPES) ?> (maksimal <?= (MAX_FILE_SIZE / 1024 / 1024) ?>MB)
                            </small>
                            
                            <div id="photoPreview" class="mt-20" style="display: none;">
                                <p style="margin-bottom: 8px; font-weight: 600;">Preview:</p>
                                <img id="previewImage" class="photo-preview" src="" alt="Preview Foto">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>📊 Status Keaktifan *</label>
                            <div class="radio-group">
                                <label>
                                    <input type="radio" name="keterangan" value="aktif" 
                                        <?= (!isset($_POST['keterangan']) || $_POST['keterangan'] == 'aktif') ? 'checked' : '' ?>>
                                    🟢 Aktif
                                </label>
                                <label>
                                    <input type="radio" name="keterangan" value="tidak" 
                                        <?= (isset($_POST['keterangan']) && $_POST['keterangan'] == 'tidak') ? 'checked' : '' ?>>
                                    🔴 Tidak Aktif
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                💾 Simpan Data
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                ❌ Batalkan
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Photo preview functionality
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('photoPreview');
            const previewImage = document.getElementById('previewImage');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Form validation
        document.getElementById('mahasiswaForm').addEventListener('submit', function(e) {
            const nim = document.getElementById('nim').value;
            const nama = document.getElementById('nama').value;
            const prodi = document.getElementById('prodi').value;
            const angkatan = document.getElementById('angkatan').value;
            
            if (!nim || !nama || !prodi || !angkatan) {
                e.preventDefault();
                alert('❗ Harap lengkapi semua field yang wajib diisi!');
                return false;
            }

            // NIM validation (8 digits)
            if (!/^\d{8}$/.test(nim)) {
                e.preventDefault();
                alert('❗ NIM harus terdiri dari 8 digit angka!');
                return false;
            }
        });

        // Add loading state to form submit
        document.getElementById('mahasiswaForm').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '⏳ Menyimpan...';
        });
    </script>
</body>
</html>