<?php
include 'session.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $harga = filter_var($_POST['harga'], FILTER_VALIDATE_INT);
    $deskripsi = $_POST['deskripsi'];
    $gambar = $_FILES['gambar']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($gambar);

    // Validate file upload
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['gambar']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        echo "Sorry, only JPG, PNG, and GIF files are allowed.";
        exit();
    }

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO barang (nama_barang, deskripsi, harga, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nama_barang, $deskripsi, $harga, $gambar);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Masukin Barang</title>
</head>
<body>
<h2>Masukin Barang Baru</h2>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nama_barang">Nama Barang:</label>
    <input type="text" name="nama_barang" required><br><br>
    <label for="harga">Harga:</label>
    <input type="number" name="harga" required><br><br>
    <label for="deskripsi">Description:</label><br>
    <textarea name="deskripsi" rows="4" cols="50" required></textarea><br><br>
    <label for="gambar">Image:</label>
    <input type="file" name="gambar" required><br><br>
    <input type="submit" value="Insert Item">
</form>
<br>
<a href="index.php">Back to Home</a>
</body>
</html>