<?php
include 'session.php';
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $harga = filter_var($_POST['harga'], FILTER_VALIDATE_INT);
    $deskripsi = htmlspecialchars(trim($_POST['deskripsi']));
    $gambar = $_FILES['gambar']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($gambar);

    // Update without changing the image if a new one is not provided
    if (!empty($gambar)) {
        // Validate file upload
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['gambar']['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            echo "Sorry, only JPG, PNG, and GIF files are allowed.";
            exit();
        }

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE barang SET nama_barang = ?, deskripsi = ?, harga = ?, gambar = ? WHERE id = ?");
            $stmt->bind_param("sissi", $nama_barang, $deskripsi, $harga, $gambar, $id);
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    } else {
        $stmt = $conn->prepare("UPDATE barang SET nama_barang = ?, deskripsi = ?, harga = ? WHERE id = ?");
        $stmt->bind_param("ssii", $nama_barang, $deskripsi, $harga, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT nama_barang, deskripsi, harga, gambar FROM barang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nama_barang, $deskripsi, $harga, $gambar);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Item</title>
</head>
<body>
<h2>Update Item</h2>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nama_barang">Nama Barang:</label>
    <input type="text" name="nama_barang" value="<?php echo htmlspecialchars($nama_barang); ?>" required><br><br>
    <label for="harga">Harga:</label>
    <input type="number" name="harga" value="<?php echo htmlspecialchars($harga); ?>" required><br><br>
    <label for="deskripsi">Description:</label><br>
    <textarea name="deskripsi" rows="4" cols="50" required><?php echo htmlspecialchars($deskripsi); ?></textarea><br><br>
    <label for="gambar">Image:</label>
    <input type="file" name="gambar"><br><br>
    <p>Current Image: <?php echo htmlspecialchars($gambar); ?></p>
    <input type="submit" value="Update Item">
</form>
<br>
<a href="index.php">Back to Home</a>
</body>
</html>