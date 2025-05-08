<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perpustakaan Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <!-- Custom Style -->
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            background: linear-gradient(to right, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        h1 {
            font-size: 3.5rem;
            animation: fadeInDown 1s ease-out;
        }
        p {
            font-size: 1.2rem;
            margin-top: 20px;
            animation: fadeInUp 1s ease-out;
        }
        .btn-login {
            margin-top: 30px;
            font-size: 1.1rem;
            padding: 12px 30px;
            border-radius: 30px;
            animation: fadeIn 1.5s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div>
        <h1>Selamat Datang</h1>
        <p>Sistem Informasi Perpustakaan Digital</p>
        <a href="login.php" class="btn btn-light btn-login">Masuk Sekarang</a>
    </div>
</body>
</html>
