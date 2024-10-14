<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Username sudah digunakan. Silakan coba lagi.";
        $check_stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $error = "Registrasi berhasil. Silakan login.";
        } else {
            $error = "Registrasi gagal. Silakan coba lagi.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi</title>
</head>
<body>
<h2>Registrasi</h2>
<form action="register.php" method="POST">
    Username: <input type="text" name="username"  placeholder="Username"><br><br>
    Password: <input type="password" name="password" required placeholder="Password" minlength="8"><br><br>
    <button type="submit">Register</button>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</form>
<p>Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
</body>
</html>
