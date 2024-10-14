<?php
include 'session.php';
include 'config.php';

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT * FROM barang LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$total_stmt = $conn->prepare("SELECT COUNT(*) FROM barang");
$total_stmt->execute();
$total_stmt->bind_result($total_items);
$total_stmt->fetch();
$total_stmt->close();
$total_pages = ceil($total_items / $limit);

if ($_SESSION['user_id'] == 1) { ?>
    <h2>Selamat Datang, Admin <?php echo $username; ?></h2>
    <h2>Daftar Barang</h2>
    <a href="masukin_barang.php">Masukin Barang Baru</a><br><br>
    <table border="1">
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Actions</th>
        </tr>
        <?php
        $no = $offset + 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($no++); ?></td>
                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                <td>Rp <?php echo htmlspecialchars(number_format($row['harga'], 2, ',', '.')); ?></td>
                <td>
                    <a href="detail_barang.php?id=<?php echo urlencode($row['id']); ?>">Detail</a> |
                    <a href="update_barang.php?id=<?php echo urlencode($row['id']); ?>">Update</a> |
                    <a href="index.php?delete=<?php echo urlencode($row['id']); ?>" onclick="return confirm('Yakinnn Mau Hapus?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <br>
    <div>
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php } ?>
    </div>
    <a href="logout.php">Logout</a>
<?php } else { ?>
    <h2>Selamat Datang, <?php echo $username; ?></h2>;
    <a href="list_barang.php">Lihat Daftar Barang</a><br><br>;
    <a href="logout.php">Logout</a>;
<?php }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>

</body>
</html>
