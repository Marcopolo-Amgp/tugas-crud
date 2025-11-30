<?php
require_once '../class/repoMahasiswa.php';
require_once '../inc/config.php';

$repo = new RepoMahasiswa();
$dataMahasiswa = $repo->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎓 Data Mahasiswa</h1>
            <p>Sistem Manajemen Data Mahasiswa - <?= APP_NAME ?></p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="nav-links">
                <a href="index.php" class="btn btn-primary">📊 Dashboard</a>
                <a href="create.php" class="btn btn-success">➕ Tambah Mahasiswa</a>
            </div>
            <div class="app-info">
                <strong><?= APP_NAME ?></strong> v<?= APP_VERSION ?>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    ✅ <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2 style="margin-bottom: 20px; color: var(--dark);">Daftar Mahasiswa</h2>
                
                <?php if (empty($dataMahasiswa)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📝</div>
                        <h3>Belum ada data mahasiswa</h3>
                        <p>Mulai dengan menambahkan data mahasiswa pertama Anda</p>
                        <a href="create.php" class="btn btn-primary">Tambah Data Pertama</a>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> Foto </th>
                                    <th> NIM </th>
                                    <th> Nama </th>
                                    <th> Prodi </th>
                                    <th> Angkatan </th>
                                    <th> Status </th>
                                    <th> Aksi </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dataMahasiswa as $m): ?>
                                <tr>
                                    <td>
                                        <?php if ($m['foto']): ?>
                                            <img src="uploads/<?= htmlspecialchars($m['foto']) ?>" 
                                                 alt="Foto <?= htmlspecialchars($m['nama']) ?>" 
                                                 class="photo-preview">
                                        <?php else: ?>
                                            <div class="photo-placeholder">No Photo</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($m['nim']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($m['nama']) ?></td>
                                    <td><?= htmlspecialchars($prodiOptions[$m['prodi']]) ?></td>
                                    <td><?= htmlspecialchars($m['angkatan']) ?></td>
                                    <td>
                                        <span class="status <?= $m['keterangan'] ?>">
                                            <?= ucfirst($m['keterangan']) ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="update.php?nim=<?= $m['nim'] ?>" 
                                           class="btn btn-warning btn-sm" 
                                           title="Edit Data">
                                            ✏️ Edit
                                        </a>
                                        <a href="delete.php?nim=<?= $m['nim'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Yakin menghapus data <?= htmlspecialchars($m['nama']) ?>?')"
                                           title="Hapus Data">
                                            🗑️ Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="summary">
                        <p>📈 Total Data: <strong><?= count($dataMahasiswa) ?></strong> mahasiswa terdaftar</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 <?= APP_NAME ?>. All rights reserved. | Dibuat dengan ❤️ untuk Pengembangan Sistem Backend</p>
        </div>
    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Add loading state to buttons when clicked
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.add('loading');
            });
        });
    </script>
</body>
</html>