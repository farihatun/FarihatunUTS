<?php
// Mulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Redirect ke login jika user belum login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
// Koneksi database
include 'koneksi.php';

// Inisialisasi variabel pencarian dan pesan
$keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
$message = '';

// Gunakan prepared statement untuk keamanan
if (!empty($keyword)) {
    $stmt = $conn->prepare("SELECT * FROM rumah WHERE alamat LIKE ? OR tipe LIKE ?");
    $param = "%{$keyword}%";
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM rumah");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Rumah</title>
    <style>
        :root {
            --primary-dark: #001a33;
            --primary-light: #003366;
            --white: #ffffff;
            --light-gray: #f0f4f8;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 960px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        h2 {
            text-align: center;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
        }
        .top-actions {
            text-align: right;
            margin-bottom: 1.5rem;
        }
        form {
            text-align: center;
            margin: 2rem 0;
        }
        input[type="text"] {
            padding: 12px 16px;
            width: 300px;
            border: 2px solid var(--primary-light);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus {
            border-color: var(--primary-dark);
            outline: none;
        }
        button, .button {
            padding: 12px 20px;
            border: none;
            background-color: var(--primary-light);
            color: var(--white);
            border-radius: 8px;
            cursor: pointer;
            margin-left: 8px;
            font-size: 1rem;
            transition: background-color 0.2s;
            font-weight: bold;
        }
        button:hover, .button:hover {
            background-color: var(--primary-dark);
        }
        .button.secondary {
            background-color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background-color: var(--white);
            box-shadow: var(--shadow);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: var(--primary-dark);
            color: var(--white);
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 0 3px rgba(0,0,0,0.1);
        }
        .no-data {
            text-align: center;
            color: #777;
            padding: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 2rem;
        }
        .button {
            display: inline-block;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Dashboard - Daftar Rumah</h2>

    <div class="top-actions">
        <a href="input_rumah.php" class="button">+ Tambah Rumah</a>
        <a href="logout.php" class="button secondary">Logout</a>
    </div>

    <form method="GET">
        <input type="text" name="cari" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari alamat / tipe...">
        <button type="submit">Cari</button>
        <a href="index.php" class="button secondary">Reset</a>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Alamat</th>
            <th>Tipe</th>
            <th>Harga</th>
            <th>Gambar</th>
        </tr>
        <?php if (isset($result) && $result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_rumah'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['tipe'] ?></td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <?php if (!empty($row['gambar']) && file_exists("uploads/{$row['gambar']}")): ?>
                            <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Rumah">
                        <?php else: ?>
                            <em>Tidak ada gambar</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="no-data">Data tidak ditemukan.</td></tr>
        <?php endif; ?>
    </table>

    <div class="footer">
        <a href="input_rumah.php" class="button">Tambah Rumah Baru</a>
    </div>
</div>
</body>
</html>
