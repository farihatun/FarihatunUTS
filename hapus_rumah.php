<?php
include 'koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
if ($id) {
    // Ambil data rumah untuk hapus gambarnya
    $stmt = $conn->prepare("SELECT gambar FROM rumah WHERE id_rumah = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $rumah = $stmt->get_result()->fetch_assoc();

    // Hapus file gambar jika ada
    if ($rumah && $rumah['gambar'] && file_exists("uploads/" . $rumah['gambar'])) {
        unlink("uploads/" . $rumah['gambar']);
    }

    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM rumah WHERE id_rumah = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: index.php");
exit;
?>
