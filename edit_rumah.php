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
$id = $_GET['id'] ?? 0;
$rumah = [];

// Ambil data rumah berdasarkan ID
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM rumah WHERE id_rumah = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $rumah = $stmt->get_result()->fetch_assoc();
    if (!$rumah) {
        $pesan = "❌ Data rumah tidak ditemukan!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $rumah) {
    $alamat = $_POST['alamat'];
    $tipe = $_POST['tipe'];
    $harga = $_POST['harga'];
    $gambarLama = $rumah['gambar'];

    // Jika ada file gambar baru diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $tipe_file = mime_content_type($tmp);

        if (strpos($tipe_file, 'image/') === 0) {
            $nama_gambar = time() . "_" . basename($gambar);
            move_uploaded_file($tmp, "uploads/" . $nama_gambar);
            // Hapus gambar lama jika ada
            if ($gambarLama && file_exists("uploads/" . $gambarLama)) {
                unlink("uploads/" . $gambarLama);
            }
            $gambar = $nama_gambar;
        } else {
            $pesan = "❌ File harus berupa gambar!";
            $gambar = $gambarLama;
        }
    } else {
        $gambar = $gambarLama; // Pakai gambar lama jika tidak ada upload baru
    }

    // Update data rumah
    $stmt = $conn->prepare("UPDATE rumah SET alamat=?, tipe=?, harga=?, gambar=? WHERE id_rumah=?");
    $stmt->bind_param("ssdsi", $alamat, $tipe, $harga, $gambar, $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $pesan = "❌ Gagal update data rumah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Rumah</title>
    <style>
        :root {
            --dark-blue: #0f172a;
            --medium-blue: #1e293b;
            --white: #ffffff;
            --light-gray: #f1f5f9;
            --green: #10b981;
            --red: #ef4444;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light-gray);
            margin: 0;
            padding: 20px;
        }
        header {
            background-color: var(--dark-blue);
            color: var(--white);
            padding: 20px;
            text-align: center;
            border-radius: 10px;
        }
        .logout {
            position: absolute;
            right: 20px;
            top: 20px;
            background: var(--red);
            color: var(--white);
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .container {
            margin-top: 30px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
            color: var(--dark-blue);
        }
        form input, form select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        form input[type="file"] {
            padding: 5px;
        }
        button {
            background: var(--dark-blue);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #34d399;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #f87171;
        }
        img.thumb {
            width: 100px;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <header>
        <h1>🏠 Edit Rumah</h1>
        <a href="logout.php" class="logout">Logout</a>
    </header>
    <div class="container">
        <?php if (!empty($pesan)): ?>
            <div class="message <?= strpos($pesan, '✅') !== false ? 'success' : 'error' ?>">
                <?= $pesan ?>
            </div>
        <?php endif; ?>
        <?php if ($rumah): ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="alamat" placeholder="Alamat rumah" value="<?= htmlspecialchars($rumah['alamat']) ?>" required>
                <select name="tipe" required>
                    <option value="Tipe 36" <?= $rumah['tipe'] == 'Tipe 36' ? 'selected' : '' ?>>Tipe 36</option>
                    <option value="Tipe 45" <?= $rumah['tipe'] == 'Tipe 45' ? 'selected' : '' ?>>Tipe 45</option>
                    <option value="Tipe 70" <?= $rumah['tipe'] == 'Tipe 70' ? 'selected' : '' ?>>Tipe 70</option>
                    <option value="Subsidi" <?= $rumah['tipe'] == 'Subsidi' ? 'selected' : '' ?>>Subsidi</option>
                    <option value="Cluster" <?= $rumah['tipe'] == 'Cluster' ? 'selected' : '' ?>>Cluster</option>
                    <option value="Ruko" <?= $rumah['tipe'] == 'Ruko' ? 'selected' : '' ?>>Ruko</option>
                </select>
                <input type="number" name="harga" placeholder="Harga rumah (Rp)" value="<?= htmlspecialchars($rumah['harga']) ?>" required>
                <img src="uploads/<?= htmlspecialchars($rumah['gambar']) ?>" alt="Gambar Rumah" class="thumb">
                <input type="file" name="gambar" accept="image/*">
                <button type="submit">Simpan Perubahan</button>
            </form>
        <?php else: ?>
            <p>Data rumah tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
