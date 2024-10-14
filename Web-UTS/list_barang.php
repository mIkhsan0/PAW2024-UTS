<?php
include 'session.php';
require 'config.php';

if ($_SESSION['user_id'] == 1) {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Barang</title>
</head>
<body>
<h2>Daftar Barang</h2>
<a href="index.php">Home</a> | <a href="logout.php">Logout</a>
<br><br>
<?php
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT * FROM barang LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<ol start='" . (($page - 1) * $limit + 1) . "'>";;
    while ($row = $result->fetch_assoc()) {
        echo "<li><a href='detail_barang.php?id={$row['id']}'><strong>{$row['nama_barang']}</strong> - Rp {$row['harga']}</a></li>";
    }
    echo "<ol>";

    $stmt->close();

    $total_stmt = $conn->prepare("SELECT COUNT(*) FROM barang");
    $total_stmt->execute();
    $total_stmt->bind_result($total_items);
    $total_stmt->fetch();
    $total_stmt->close();
    $total_pages = ceil($total_items / $limit);
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<a href='list_barang.php?page=$i'>$i</a> ";
    }
?>
</body>
</html>
