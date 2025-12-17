<?php
include "config/koneksi.php";

$q = mysqli_query($koneksi, "SELECT * FROM admin");
$data = mysqli_fetch_assoc($q);

var_dump($data);
