<?php
$h  = "localhost"; $u  = "root"; $p  = "12345"; $db = "uas_1661";

$kon = mysqli_connect($h, $u, $p, $db);

if (!$kon) {
    die("Koneksi gagal: " . mysqli_connect_error()); }
?>