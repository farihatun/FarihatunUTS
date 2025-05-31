<?php
include 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Ambil data pembeli dan rumah
$pembeli = $conn->query("SELECT id_pembeli, nama FROM pembeli");
$rumah = $conn->query("SELECT id_rumah, alamat FROM rumah");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pembeli = $conn->real_escape_string($_POST['id_pembeli']);
    $id_rumah = $conn->real_escape_string($_POST['id_rumah']);
    $tanggal = $conn->real_escape_string($_POST['tanggal_transaksi']);
    $total = $conn->real_escape_string($_POST['total_pembayaran']);

    $sql = "INSERT INTO transaksi (id_pembeli, id_rumah, tanggal_transaksi, total_pembayaran)
            VALUES ('$id_pembeli', '$id_rumah', '$tanggal', '$total')";

    if ($conn->query($sql)) {
        $message = "✅ Transaksi berhasil disimpan!";
    } else {
        $message = "❌ Gagal menyimpan transaksi: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Transaksi</title>
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
            max-width: 600px;
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
        form input, form select {
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
    <h2>💼 Input Transaksi</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label for="id_pembeli">Pembeli</label>
        <select name="id_pembeli" id="id_pembeli" required>
            <option value="">-- Pilih Pembeli --</option>
            <?php while ($row = $pembeli->fetch_assoc()): ?>
                <option value="<?= $row['id_pembeli'] ?>">
                    <?= $row['nama'] ?> (ID: <?= $row['id_pembeli'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="id_rumah">Rumah</label>
        <select name="id_rumah" id="id_rumah" required>
            <option value="">-- Pilih Rumah --</option>
            <?php while ($row = $rumah->fetch_assoc()): ?>
                <option value="<?= $row['id_rumah'] ?>">
                    <?= $row['alamat'] ?> (ID: <?= $row['id_rumah'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="tanggal_transaksi">Tanggal Transaksi</label>
        <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" required>

        <label for="total_pembayaran">Total Pembayaran (Rp)</label>
        <input type="number" name="total_pembayaran" id="total_pembayaran" required placeholder="100000000">

        <button type="submit">💾 Simpan Transaksi</button>
    </form>

    <a href="index.php" class="back-link">← Kembali ke Beranda</a>
</div>
</body>
</html>
