<?php
session_start();
include "config/koneksi.php";

/* Tambah ke keranjang */
if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header("Location: index.php");
    exit;
}

/* Ambil kategori */
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");

/* Filter produk berdasarkan kategori */
$where = "";
if (isset($_GET['kategori'])) {
    $idk = $_GET['kategori'];
    $where = "WHERE produk.id_kategori='$idk'";
}

/* Ambil produk */
$produk = mysqli_query($koneksi, "
    SELECT produk.*, kategori.nama_kategori
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id
    $where
    ORDER BY produk.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Warung Kita</title>
<style>
body {margin:0;font-family:Arial;background:#0f0f0f;color:#fff;}
header{background:#111;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;}
header h1{color:#ff9800;margin:0;}
header a{color:#ff9800;text-decoration:none;margin-left:10px;}
.kategori{display:flex;gap:10px;padding:15px 30px;background:#151515;overflow-x:auto;}
.kategori a{padding:8px 16px;background:#1e1e1e;border-radius:20px;color:#ccc;text-decoration:none;white-space:nowrap;transition:0.2s;}
.kategori a.active{background:#ff9800;color:#000;}
.container{padding:30px;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:20px;}
.card{background:#181818;border-radius:14px;padding:16px;text-align:center;transition:transform 0.25s;}
.card:hover{transform:translateY(-6px);}
.card h3{font-size:16px;margin:10px 0 4px;}
.card span{font-size:13px;color:#aaa;display:block;margin-bottom:8px;}
.price{color:#ff9800;font-weight:bold;margin-bottom:10px;}
.btn{display:inline-block;padding:8px 12px;background:#ff9800;color:#000;border-radius:8px;text-decoration:none;font-weight:bold;transition:0.2s;}
.btn:hover{transform:translateY(-2px);box-shadow:0 6px 15px rgba(255,152,0,0.3);}
.empty{text-align:center;color:#777;padding:60px;}
</style>
</head>
<body>

<header>
    <h1>Warung Kita</h1>
    <div>
        <a href="keranjang.php">🛒 Keranjang</a>
        <a href="pages/login.php">Admin</a>
    </div>
</header>

<!-- Kategori -->
<div class="kategori">
    <a href="index.php" class="<?= !isset($_GET['kategori'])?'active':'' ?>">Semua</a>
    <?php while($k=mysqli_fetch_assoc($kategori)): ?>
        <a href="?kategori=<?= $k['id'] ?>" class="<?= (isset($_GET['kategori']) && $_GET['kategori']==$k['id'])?'active':'' ?>">
            <?= $k['nama_kategori'] ?>
        </a>
    <?php endwhile; ?>
</div>

<!-- Produk -->
<div class="container">
<?php if(mysqli_num_rows($produk) == 0): ?>
    <div class="empty">Produk belum tersedia</div>
<?php else: ?>
    <div class="grid">
        <?php while($p=mysqli_fetch_assoc($produk)): ?>
        <div class="card">
            <h3><?= $p['nama_produk'] ?></h3>
            <span><?= $p['nama_kategori'] ?></span>
            <div class="price">Rp <?= number_format($p['harga']) ?></div>
            <a class="btn" href="?add=<?= $p['id'] ?>">Tambah ke Keranjang</a>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
</div>

</body>
</html>
