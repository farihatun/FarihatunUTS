<?php
include 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$pesan = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alamat = $_POST['alamat'];
    $tipe = $_POST['tipe'];
    $harga = $_POST['harga'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $tipe_file = mime_content_type($tmp);

        if (strpos($tipe_file, 'image/') === 0) {
            $nama_gambar = time() . "_" . basename($gambar);
            move_uploaded_file($tmp, "uploads/" . $nama_gambar);

            $stmt = $conn->prepare("INSERT INTO rumah (alamat, tipe, harga, gambar) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $alamat, $tipe, $harga, $nama_gambar);
            if ($stmt->execute()) {
                $pesan = "✅ Rumah berhasil disimpan!";
            } else {
                $pesan = "❌ Gagal menyimpan data rumah.";
            }
        } else {
            $pesan = "❌ File harus berupa gambar!";
        }
    } else {
        $pesan = "❌ Harap upload gambar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Rumah</title>
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
    <h2>🏠 Input Data Rumah</h2>
    <?php if (!empty($pesan)): ?>
        <div class="message <?= strpos($pesan, '✅') !== false ? 'success' : 'error' ?>">
            <?= $pesan ?>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="alamat">Alamat</label>
        <input type="text" id="alamat" name="alamat" required placeholder="Jl. Contoh No. 123">
        <label for="tipe">Tipe Rumah</label>
        <select id="tipe" name="tipe" required>
            <option value="">-- Pilih Tipe Rumah --</option>
            <option value="Tipe 36">Tipe 36</option>
            <option value="Tipe 45">Tipe 45</option>
            <option value="Tipe 70">Tipe 70</option>
            <option value="Subsidi">Subsidi</option>
            <option value="Cluster">Cluster</option>
            <option value="Ruko">Ruko</option>
        </select>
        <label for="harga">Harga (Rp)</label>
        <input type="number" id="harga" name="harga" required placeholder="100000000">
        <label for="gambar">Upload Gambar</label>
        <input type="file" id="gambar" name="gambar" accept="image/*" required>
        <button type="submit">💾 Simpan Data</button>
    </form>
    <a href="index.php" class="back-link">← Kembali ke Beranda</a>
</div>
</body>
</html>
