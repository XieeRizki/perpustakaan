<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];

    $query = "INSERT INTO buku (judul, penulis, penerbit) 
              VALUES ('$judul', '$penulis', '$penerbit')";
    mysqli_query($conn, $query);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
</head>
<body>
    <h2>Tambah Buku</h2>
    <form method="POST">
        Judul: <input type="text" name="judul" required><br>
        Penulis: <input type="text" name="penulis" required><br>
        Penerbit: <input type="text" name="penerbit" required><br>
        <button type="submit">Simpan</button>
    </form>
    <a href="dashboard.php">← Kembali</a>
</body>
</html>
