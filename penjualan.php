<?php
include "koneksi.php";

$err = "";
$ok  = false;
$sub = 0;
$dp  = 0;
$pot = 0;
$tb  = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nf = mysqli_real_escape_string($kon, $_POST['nf']);
    $kp = mysqli_real_escape_string($kon, $_POST['kp']);
    $kb = mysqli_real_escape_string($kon, $_POST['kb']);
    $jb = (int) $_POST['jb'];
    $qb = mysqli_query($kon, "SELECT * FROM barang WHERE kd_barang='$kb'");
    $db = mysqli_fetch_assoc($qb);

    if (!$db) {
        $err = "Data barang tidak ditemukan.";
    } elseif ($jb > $db['stok']) {
        $err = "Stok Barang Tidak Mencukupi";
    } else {
        $sub = $db['harga'] * $jb;

        if ($sub >= 5000000) {
            $dp = 15;
        } elseif ($sub >= 3000000) {
            $dp = 10;
        } elseif ($sub >= 1000000) {
            $dp = 5;
        } else {
            $dp = 0;
        }

        $pot = $sub * $dp / 100;
        $tb  = $sub - $pot;

        $tgl = date("Y-m-d");
        $qi = "INSERT INTO penjualan (no_faktur, kd_pelanggan, kd_barang, jumlah_beli, total_harga, tanggal) 
               VALUES ('$nf','$kp','$kb','$jb','$tb','$tgl')";
        mysqli_query($kon, $qi);

        $sb = $db['stok'] - $jb;
        mysqli_query($kon, "UPDATE barang SET stok='$sb' WHERE kd_barang='$kb'");

        $ok = true;
    }
}

$lp = mysqli_query($kon, "SELECT * FROM pelanggan");
$lb = mysqli_query($kon, "SELECT * FROM barang");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table.layout { width: 100%; border-collapse: collapse; }
        table.layout td { vertical-align: top; width: 50%; padding: 20px; border: 1px solid #333; }
        label { display: inline-block; width: 100px; }
        input, select { padding: 5px; margin: 5px 0; }
        .error { color: red; font-weight: bold; }
        .btn { padding: 5px 15px; }
    </style>
</head>
<body>

<table class="layout">
<tr>
    <td>
        <h2>Transaksi Penjualan</h2>
        <form method="POST" action="penjualan.php">
            <label>No Faktur</label>
            <input type="text" name="nf" maxlength="10" required><br>

            <label>Pelanggan</label>
            <select name="kp" required>
                <?php while ($p = mysqli_fetch_assoc($lp)) { ?>
                    <option value="<?php echo $p['kd_pelanggan']; ?>">
                        <?php echo $p['nama_pelanggan']; ?>
                    </option>
                <?php } ?>
            </select><br>

            <label>Barang</label>
            <select name="kb" required>
                <?php while ($b = mysqli_fetch_assoc($lb)) { ?>
                    <option value="<?php echo $b['kd_barang']; ?>">
                        <?php echo $b['nama_barang']; ?>
                    </option>
                <?php } ?>
            </select><br>

            <label>Jumlah Beli</label>
            <input type="number" name="jb" min="1" required><br>

            <button type="submit" class="btn">Simpan</button>
        </form>
    </td>
    <td>
        <?php if ($err) { ?>
            <h2 class="error"><?php echo $err; ?></h2>
        <?php } elseif ($ok) { ?>
            <h2>Transaksi Berhasil</h2>
            <p>
                Subtotal&nbsp;&nbsp;: Rp <?php echo number_format($sub, 0, ',', '.'); ?><br>
                Diskon&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $dp; ?>%<br>
                Total Bayar : Rp <?php echo number_format($tb, 0, ',', '.'); ?>
            </p>
        <?php } else { ?>
            <h2>Output</h2>
            <p>Silakan isi form transaksi di sebelah kiri.</p>
        <?php } ?>
    </td>
</tr>
</table>

</body>
</html>