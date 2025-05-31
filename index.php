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
                header("Location: index.php");
                exit;
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

// Ambil data rumah
$query = "SELECT * FROM rumah ORDER BY id_rumah DESC";
$result = $conn->query($query);
if (!$result) {
    die("Query error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Data Rumah</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table th, table td {
            border: 1px solid var(--light-gray);
            padding: 12px;
            text-align: left;
        }
        table th {
            background-color: var(--dark-blue);
            color: white;
        }
        .form-section {
            margin-top: 40px;
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
        .aksi {
            display: flex;
            gap: 10px;
        }
        .aksi a {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .aksi .edit {
            background: #6a35ff;
            color: white;
        }
        .aksi .hapus {
            background: #ef4444;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>🏠 Dashboard Rumah</h1>
        <a href="logout.php" class="logout">Logout</a>
    </header>
    <div class="container">
        <?php if (!empty($pesan)): ?>
            <div class="message <?= strpos($pesan, '✅') !== false ? 'success' : 'error' ?>">
                <?= $pesan ?>
            </div>
        <?php endif; ?>

        <h2>📋 Daftar Rumah</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Alamat</th>
                <th>Tipe</th>
                <th>Harga</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
            <?php if ($result->num_rows > 0): $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['alamat']) ?></td>
                        <td><?= htmlspecialchars($row['tipe']) ?></td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td>
                            <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar Rumah" class="thumb">
                        </td>
                        <td>
                            <div class="aksi">
                                <a href="edit_rumah.php?id=<?= $row['id_rumah'] ?>" class="edit">Edit</a>
                                <a href="hapus_rumah.php?id=<?= $row['id_rumah'] ?>" class="hapus" onclick="return confirm('Yakin hapus rumah ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Belum ada data rumah.</td></tr>
            <?php endif; ?>
        </table>
        <div class="form-section">
            <h2>➕ Tambah Rumah Baru</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="alamat" placeholder="Alamat rumah" required>
                <select name="tipe" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Tipe 36">Tipe 36</option>
                    <option value="Tipe 45">Tipe 45</option>
                    <option value="Tipe 70">Tipe 70</option>
                    <option value="Subsidi">Subsidi</option>
                    <option value="Cluster">Cluster</option>
                    <option value="Ruko">Ruko</option>
                </select>
                <input type="number" name="harga" placeholder="Harga rumah (Rp)" required>
                <input type="file" name="gambar" accept="image/*" required>
                <button type="submit">Simpan Rumah</button>
            </form>
        </div>
    </div>
</body>
</html>
