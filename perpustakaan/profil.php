<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

// Handle profile and password update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update Profile
    if (isset($_POST['update_profile'])) {
        $nama = htmlspecialchars($_POST['nama']);
        $email = htmlspecialchars($_POST['email']);

        // Handle profile picture upload
        $foto_profil = $_FILES['foto_profil'];

        // Check if a file is uploaded
        if ($foto_profil['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profile_pictures/';
            $ext = pathinfo($foto_profil['name'], PATHINFO_EXTENSION);
            $new_filename = $user_id . '.' . $ext;
            $upload_file = $upload_dir . $new_filename;

            // Move the uploaded file to the server directory
            if (move_uploaded_file($foto_profil['tmp_name'], $upload_file)) {
                // Update the profile picture in the database
                $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, foto_profil = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nama, $email, $new_filename, $user_id);
            }
        } else {
            // If no file is uploaded, just update name and email
            $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nama, $email, $user_id);
        }

        $stmt->execute();
        header("Location: profil.php");
        exit();
    }

    // Update Password
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if current password is correct
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();

        if (password_verify($current_password, $user_data['password'])) {
            // Check if new password and confirm password match
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                // Update the password in the database
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                $stmt->execute();
                $password_message = "Password berhasil diubah!";
                // Keep the modal open after successful password change
            } else {
                $password_error = "Password baru dan konfirmasi tidak cocok!";
            }
        } else {
            $password_error = "Kata sandi saat ini salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="./img/asset.png" alt="Logo" style="height: 40px;" class="me-2">
        Perpustakaan
    </a>
    <div class="mx-auto">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        </ul>
    </div>
    <div class="d-flex">
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                👤 <?= htmlspecialchars($user['nama']) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item active" href="profil.php">Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <center><h3>Profil Pengguna</h3></center>
    <div class="card mt-3" style="max-width: 400px; margin: 0 auto;">
        <div class="card-body">
            <div class="text-center mb-4">
                <!-- Display current profile picture -->
                <?php if (isset($user['foto_profil']) && !empty($user['foto_profil'])): ?>
                    <img src="uploads/profile_pictures/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Profile Picture" class="img-fluid rounded-circle" width="100">
                <?php else: ?>
                    <img src="uploads/profile_pictures/default.jpg" alt="Profile Picture" class="img-fluid rounded-circle" width="100">
                <?php endif; ?>
            </div>
            <p><strong>Nama:</strong> <?= htmlspecialchars($user['nama']) ?></p>
            <?php if (!empty($user['email'])): ?>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <?php endif; ?>
            <p><strong>ID Pengguna:</strong> <?= $user['id'] ?></p>
            <!-- Button to trigger the profile edit modal -->
            <button type="button" class="btn btn-warning mt-3" data-bs-toggle="modal" data-bs-target="#editModal">Edit Profil</button>
            <!-- Button to trigger the change password modal -->
            <button type="button" class="btn btn-secondary mt-3 ms-2" data-bs-toggle="modal" data-bs-target="#passwordModal">Ganti Password</button>
        </div>
    </div>
</div>

<!-- Modal for editing profile -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for updating profile information -->
                <form action="profil.php" method="POST" enctype="multipart/form-data">
                    <!-- Profile Picture Section -->
                    <div class="mb-3 text-center">
                        <label for="foto_profil" class="form-label">Foto Profil</label><br>
                        <img src="uploads/profile_pictures/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Current Profile Picture" class="img-fluid rounded-circle mb-3" width="100">
                        <input type="file" class="form-control" name="foto_profil">
                    </div>
                    <!-- Name Section -->
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>
                    <!-- Email Section -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_profile">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for changing password -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for updating password -->
                <form action="profil.php" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Kata Sandi Baru</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_password">Ganti Password</button>
                </form>
                <!-- Display error or success message -->
                <?php if (isset($password_message)): ?>
                    <div class="alert alert-success mt-3"><?= $password_message ?></div>
                <?php elseif (isset($password_error)): ?>
                    <div class="alert alert-danger mt-3"><?= $password_error ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
