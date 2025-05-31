<?php
include 'koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input tidak kosong
    if (empty($username) || empty($password)) {
        $message = "❌ Username dan password wajib diisi.";
    } else {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "❌ Username sudah digunakan.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Simpan user baru
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                header("Location: register.php?reg=success");
                exit;
            } else {
                $message = "❌ Terjadi kesalahan saat registrasi.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun</title>
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
            background-color: var(--dark-blue);
            color: var(--dark-blue);
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: var(--white);
            border-radius: 16px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        .container:hover {
            transform: translateY(-5px);
        }
        h2 {
            text-align: center;
            margin: 0 0 30px 0;
            font-size: 2rem;
            color: var(--dark-blue);
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
            margin: 20px 0 8px;
            font-weight: 600;
            color: var(--dark-blue);
        }
        form input {
            width: 100%;
            padding: 14px;
            margin-bottom: 10px;
            border: 2px solid var(--medium-blue);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        form input:focus {
            outline: none;
            border-color: var(--dark-blue);
            box-shadow: 0 0 12px rgba(15, 23, 42, 0.2);
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
            transition: all 0.3s ease;
        }
        button:hover {
            background: var(--medium-blue);
            transform: scale(1.02);
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #f87171;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #34d399;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--dark-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .login-link:hover {
            color: var(--medium-blue);
        }
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📝 Registrasi Akun</h2>

    <?php if (!empty($message)): ?>
        <div class="message error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['reg']) && $_GET['reg'] === 'success'): ?>
        <div class="message success">✅ Registrasi berhasil! Silakan <a href="login.php">login</a>.</div>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required placeholder="Masukkan username">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Masukkan password">

        <button type="submit">Daftar</button>
    </form>

    <a href="login.php" class="login-link">Sudah punya akun? Login</a>
</div>

</body>
</html>
