<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

// Ambil nama pengguna dari database
$user_id = $_SESSION['user_id'];
$user_result = $conn->query("SELECT nama FROM users WHERE id = $user_id");
$user = $user_result->fetch_assoc();
$nama = $user['nama'];

// Ambil data buku
$buku = $conn->query("SELECT * FROM buku ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="./img/asset.png" alt="Logo" style="height: 40px;" class="me-2">
        Perpustakaan
    </a>
    <div class="mx-auto">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
        </ul>
    </div>
    <div class="d-flex">
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                👤 <?= htmlspecialchars($nama) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="profil.php">Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Konten -->
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Daftar Buku</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Buku</button>
    </div>

    <!-- Search -->
    <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari buku..." class="form-control mb-3">

    <!-- Tabel Buku -->
    <table class="table table-bordered table-striped" id="dataTable">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = $buku->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= htmlspecialchars($row['penulis']) ?></td>
                    <td><?= htmlspecialchars($row['penerbit']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">Edit</button>
                        <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus buku ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="tambah.php">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Penulis</label>
            <input type="text" name="penulis" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Penerbit</label>
            <input type="text" name="penerbit" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit per Buku -->
<?php
$buku = $conn->query("SELECT * FROM buku ORDER BY id DESC");
foreach ($buku as $row): ?>
<div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="edit.php">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <div class="modal-header">
        <h5 class="modal-title">Edit Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($row['judul']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Penulis</label>
            <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($row['penulis']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Penerbit</label>
            <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($row['penerbit']) ?>">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<!-- Script -->
<script>
function liveSearch() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#dataTable tbody tr");
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
