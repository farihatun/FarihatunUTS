<?php
include 'koneksi.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $message = "❌ Password salah.";
        }
    } else {
        $message = "❌ Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pengguna</title>
    <style>
        :root {
            --dark-blue: #0f172a;
            --medium-blue: #1e293b;
            --white: #ffffff;
            --light-gray: #f1f5f9;
            --red: #ef4444;
            --green: #10b981;
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
        .register-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--dark-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .register-link:hover {
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
    <h2>🔒 Login Pengguna</h2>

    <?php if (!empty($message)): ?>
        <div class="message error"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required placeholder="Masukkan username">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Masukkan password">

        <button type="submit">Login</button>
    </form>

    <a href="register.php" class="register-link">Belum punya akun? Daftar</a>
</div>

</body>
</html>
