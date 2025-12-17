<?php
session_start();
include "config/koneksi.php";

$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM keranjang WHERE id='$id'");
header("Location: keranjang.php");
exit;
