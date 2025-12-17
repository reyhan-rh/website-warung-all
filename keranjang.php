<?php
session_start();
include "config/koneksi.php";

// Tambah item ke keranjang
if(isset($_GET['add'])){
    $id = $_GET['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header("Location: keranjang.php");
    exit;
}

// Kurangi item dari keranjang
if(isset($_GET['remove'])){
    $id = $_GET['remove'];
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]--;
        if($_SESSION['cart'][$id] <= 0){
            unset($_SESSION['cart'][$id]);
        }
    }
    header("Location: keranjang.php");
    exit;
}

// Ambil data produk
$cart_items = [];
$total = 0;
if(!empty($_SESSION['cart'])){
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id IN ($ids)");
    while($row = mysqli_fetch_assoc($query)){
        $row['qty'] = $_SESSION['cart'][$row['id']];
        $row['subtotal'] = $row['qty'] * $row['harga'];
        $total += $row['subtotal'];
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Keranjang Belanja</title>
<style>
body{font-family:Arial;background:#0f0f0f;color:#fff;margin:0;padding:20px;}
.container{max-width:800px;margin:0 auto;}
table{width:100%;border-collapse:collapse;margin-bottom:20px;}
th,td{padding:12px;border-bottom:1px solid #333;text-align:center;}
th{color:#aaa;}
a.button{padding:8px 12px;border-radius:8px;text-decoration:none;color:#000;font-weight:bold;margin:0 2px;}
.add{background:#ff9800;}
.remove{background:#ff5252;}
.checkout{background:#00c853;margin-top:10px;display:inline-block;}
.back{background:#ffaa33;margin-top:10px;display:inline-block;}
#struk{display:none;}
</style>
</head>
<body>

<div class="container">
<h2>Keranjang Belanja</h2>

<?php if(empty($cart_items)): ?>
<p>Keranjang kosong.</p>
<a href="index.php" class="button back">← Kembali Belanja</a>
<?php else: ?>
<table>
<tr>
<th>Produk</th>
<th>Harga</th>
<th>Qty</th>
<th>Subtotal</th>
<th>Aksi</th>
</tr>
<?php foreach($cart_items as $item): ?>
<tr>
<td><?= $item['nama_produk'] ?></td>
<td>Rp <?= number_format($item['harga']) ?></td>
<td><?= $item['qty'] ?></td>
<td>Rp <?= number_format($item['subtotal']) ?></td>
<td>
    <a href="?add=<?= $item['id'] ?>" class="button add">+</a>
    <a href="?remove=<?= $item['id'] ?>" class="button remove">-</a>
</td>
</tr>
<?php endforeach; ?>
<tr>
<td colspan="3"><strong>Total</strong></td>
<td colspan="2"><strong>Rp <?= number_format($total) ?></strong></td>
</tr>
</table>

<!-- Tombol Cetak Struk -->
<button onclick="printStruk()" class="checkout">🖨 Cetak Struk</button>
<a href="index.php" class="button back">← Kembali Belanja</a>

<!-- Struk untuk dicetak -->
<div id="struk">
<h3>Warung Kita</h3>
<p><?= date('d-m-Y H:i') ?></p>
<table>
<tr><th>Produk</th><th>Qty</th><th>Subtotal</th></tr>
<?php foreach($cart_items as $item): ?>
<tr>
<td><?= $item['nama_produk'] ?></td>
<td><?= $item['qty'] ?></td>
<td>Rp <?= number_format($item['subtotal']) ?></td>
</tr>
<?php endforeach; ?>
<tr>
<td colspan="2"><strong>Total</strong></td>
<td><strong>Rp <?= number_format($total) ?></strong></td>
</tr>
</table>
<p>Terima kasih telah berbelanja!</p>
</div>

<script>
function printStruk(){
    let struk = document.getElementById('struk').innerHTML;
    let w = window.open();
    w.document.write('<pre style="font-family:monospace;">' + struk + '</pre>');
    w.print();
    w.close();
}
</script>
<?php endif; ?>

</div>

</body>
</html>
