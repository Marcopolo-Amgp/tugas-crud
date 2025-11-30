<?php
require_once '../class/repoMahasiswa.php';
require_once '../inc/config.php';

$repo = new RepoMahasiswa();

// Get NIM from URL parameter
$nim = $_GET['nim'] ?? '';
if (!$nim) {
    header("Location: index.php");
    exit;
}

// Get existing data for confirmation
$existingData = $repo->getByNim($nim);
if (!$existingData) {
    header("Location: index.php");
    exit;
}

// Handle deletion
if ($_POST && isset($_POST['confirm'])) {
    if ($repo->delete($nim)) {
        header("Location: index.php?success=Data mahasiswa berhasil dihapus");
        exit;
    } else {
        $error = "Gagal menghapus data mahasiswa";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Mahasiswa - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🗑️ Hapus Mahasiswa</h1>
            <p>Konfirmasi penghapusan data mahasiswa</p>
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
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        ❌ <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="confirmation-card">
                    <div class="confirmation-icon">⚠️</div>
                    <h2 style="color: var(--danger); margin-bottom: 20px;">Konfirmasi Penghapusan</h2>
                    
                    <div style="background: var(--lighter); padding: 25px; border-radius: var(--radius); margin-bottom: 30px; text-align: left;">
                        <p style="font-size: 1.2rem; margin-bottom: 20px; text-align: center;">
                            Anda akan menghapus data mahasiswa berikut:
                        </p>
                        
                        <div style="display: grid; grid-template-columns: auto 1fr; gap: 15px; align-items: center; margin-bottom: 20px;">
                            <?php if ($existingData->foto): ?>
                                <img src="uploads/<?= htmlspecialchars($existingData->foto) ?>" 
                                     class="photo-preview" 
                                     alt="Foto <?= htmlspecialchars($existingData->nama) ?>">
                            <?php else: ?>
                                <div class="photo-placeholder">No Photo</div>
                            <?php endif; ?>
                            
                            <div>
                                <div style="font-size: 1.4rem; font-weight: bold; color: var(--dark); margin-bottom: 5px;">
                                    <?= htmlspecialchars($existingData->nama) ?>
                                </div>
                                <div style="color: var(--secondary);">
                                    <strong>NIM:</strong> <?= htmlspecialchars($existingData->nim) ?><br>
                                    <strong>Prodi:</strong> <?= htmlspecialchars($existingData->getNamaProdi()) ?><br>
                                    <strong>Angkatan:</strong> <?= htmlspecialchars($existingData->angkatan) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <strong>❗ PERHATIAN:</strong> Tindakan ini tidak dapat dibatalkan! Data yang dihapus tidak dapat dikembalikan.
                    </div>

                    <div class="confirmation-actions">
                        <form method="POST" style="display: contents;">
                            <button type="submit" name="confirm" value="1" class="btn btn-danger">
                                🗑️ Ya, Hapus Data
                            </button>
                        </form>
                        <a href="index.php" class="btn btn-secondary">
                            ❌ Batalkan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Add loading state to delete button
        document.querySelector('button[type="submit"]').addEventListener('click', function() {
            this.classList.add('loading');
            this.innerHTML = '⏳ Menghapus...';
        });
    </script>
</body>
</html>