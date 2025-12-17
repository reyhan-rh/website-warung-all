<?php
session_start();
include "config/koneksi.php";

if(!isset($_SESSION['id_pengunjung'])) {
    header("Location: index.php");
    exit;
}

// Ambil data keranjang
$keranjang = mysqli_query($koneksi, "
    SELECT k.id, k.jumlah, p.nama_produk, p.harga 
    FROM keranjang k 
    JOIN produk p ON k.id_produk=p.id 
    WHERE k.session_id='{$_SESSION['id_pengunjung']}'"
);

$grandTotal = 0;
while($k=mysqli_fetch_assoc($keranjang)){
    $grandTotal += $k['harga'] * $k['jumlah'];
}

// Simpan ke DB order (opsional) atau kirim WA
$waPesan = "Pesanan:\n";
$keranjang = mysqli_query($koneksi, "
    SELECT k.jumlah, p.nama_produk, p.harga 
    FROM keranjang k 
    JOIN produk p ON k.id_produk=p.id 
    WHERE k.session_id='{$_SESSION['id_pengunjung']}'"
);
while($k=mysqli_fetch_assoc($keranjang)){
    $waPesan .= "{$k['nama_produk']} x {$k['jumlah']} = Rp ".number_format($k['harga']*$k['jumlah'])."\n";
}
$waPesan .= "Total: Rp ".number_format($grandTotal);
$waLink = "https://wa.me/6281234567890?text=".urlencode($waPesan);

// Hapus keranjang setelah checkout
mysqli_query($koneksi, "DELETE FROM keranjang WHERE session_id='{$_SESSION['id_pengunjung']}'");
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<style>
body{font-family:Arial; background:#0f0f0f; color:#fff; text-align:center; padding-top:50px;}
a.btn{display:inline-block;padding:12px 20px;background:#25d366;color:#fff;text-decoration:none;border-radius:10px;font-weight:bold;}
</style>
</head>
<body>
<h2>Pesanan berhasil dibuat!</h2>
<p>Klik tombol di bawah untuk mengirim pesanan melalui WhatsApp:</p>
<a href="<?= $waLink ?>" class="btn" target="_blank">Kirim ke WhatsApp</a>
</body>
</html>
