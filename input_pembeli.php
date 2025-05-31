<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$message = '';
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);

    if (!empty($nama) && !empty($email) && !empty($no_hp)) {
        $sql = "INSERT INTO pembeli (nama, email, no_hp) VALUES ('$nama', '$email', '$no_hp')";
        if ($conn->query($sql)) {
            $message = "✅ Data pembeli berhasil disimpan!";
        } else {
            $message = "❌ Gagal menyimpan data pembeli: " . $conn->error;
        }
    } else {
        $message = "❌ Mohon lengkapi semua data!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Input Pembeli</title>
    <style>
        :root {
            --dark-blue: #0f172a;
            --medium-blue: #1e293b;
            --white: #ffffff;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--dark-blue);
            color: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            background: var(--white);
            color: var(--dark-blue);
            border-radius: 16px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            position: relative;
        }
        h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: var(--dark-blue);
            margin: 10px auto 0;
        }
        form label {
            display: block;
            margin-top: 20px;
            font-weight: 600;
        }
        form input {
            width: 100%;
            padding: 14px;
            border: 2px solid var(--medium-blue);
            border-radius: 10px;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 16px;
            margin-top: 30px;
            background: var(--dark-blue);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            text-decoration: none;
            color: var(--dark-blue);
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📝 Input Data Pembeli</h2>
    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" required placeholder="Contoh: Budi Santoso">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="contoh@email.com">
        <label for="no_hp">Nomor HP</label>
        <input type="tel" id="no_hp" name="no_hp" required placeholder="081234567890">
        <button type="submit">💾 Simpan Data</button>
    </form>
    <a href="index.php" class="back-link">← Kembali ke Beranda</a>
</div>
</body>
</html>
