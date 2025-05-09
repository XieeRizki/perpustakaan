<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

if (isset($_POST['id'], $_POST['judul'], $_POST['penulis'])) {
    $id = intval($_POST['id']);
    $judul = htmlspecialchars($_POST['judul']);
    $penulis = htmlspecialchars($_POST['penulis']);
    $penerbit = htmlspecialchars($_POST['penerbit']);

    $stmt = $conn->prepare("UPDATE buku SET judul = ?, penulis = ?, penerbit = ? WHERE id = ?");
    $stmt->bind_param("sssi", $judul, $penulis, $penerbit, $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Gagal mengedit buku.";
    }
} else {
    echo "Data tidak lengkap.";
}
?>
