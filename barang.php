<?php
include "koneksi.php";

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if (isset($_POST['bok'])) {
    $kb = $_POST['tkode'];
    $nb = $_POST['tnama'];
    $hg = $_POST['tharga'];
    $st = $_POST['tstok'];

    if (isset($_POST['tmode']) && $_POST['tmode'] == 'edit') {
        $q = "UPDATE barang SET
              nama_barang='$nb',
              harga='$hg',
              stok='$st'
              WHERE kd_barang='$kb'";
    } else {
        $q = "INSERT INTO barang
              (kd_barang,nama_barang,harga,stok)
              VALUES
              ('$kb','$nb','$hg','$st')";
    }

    mysqli_query($kon, $q);
    header("Location:barang.php");
}

$pesan = '';
if ($aksi == 'hapus') {
    $kb = $_GET['k'];

    // Cek dulu apakah barang ini masih dipakai di tabel penjualan
    $cek = mysqli_query($kon, "SELECT * FROM penjualan WHERE kd_barang='$kb'");

    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Barang tidak bisa dihapus karena sudah memiliki riwayat transaksi penjualan.";
    } else {
        $q = "DELETE FROM barang
              WHERE kd_barang='$kb'";

        mysqli_query($kon, $q);
        header("Location:barang.php");
        exit;
    }
}

if ($aksi == 'edit') {
    $kb = $_GET['k'];

    $q = "SELECT * FROM barang
          WHERE kd_barang='$kb'";

    $ex = mysqli_query($kon, $q);
    $r = mysqli_fetch_array($ex);
}
?>

<h2>Data Barang</h2>

<?php if ($pesan) { ?>
<p style="color:red;"><?php echo $pesan; ?></p>
<?php } ?>

<?php if ($aksi == 'tambah' || $aksi == 'edit') { ?>

<h3><?php echo $aksi == 'edit' ? 'Ubah Data Barang' : 'Input Data Barang'; ?></h3>

<form action="#" method="post">
    <?php if ($aksi == 'edit') { ?>
    <input type="hidden" name="tmode" value="edit">
    <?php } ?>

    Kode Barang :
    <input type="text" name="tkode"
    value="<?php echo $aksi=='edit' ? $r['kd_barang'] : ''; ?>"
    <?php echo $aksi=='edit' ? 'readonly' : ''; ?>><br>

    Nama Barang :
    <input type="text" name="tnama"
    value="<?php echo $aksi=='edit' ? $r['nama_barang'] : ''; ?>"><br>

    Harga :
    <input type="text" name="tharga"
    value="<?php echo $aksi=='edit' ? $r['harga'] : ''; ?>"><br>

    Stok :
    <input type="text" name="tstok"
    value="<?php echo $aksi=='edit' ? $r['stok'] : ''; ?>"><br>

    <input type="submit" name="bok"
    value="<?php echo $aksi=='edit' ? 'Ubah Data' : 'Simpan'; ?>">
</form>

<a href="barang.php">Kembali</a>

<?php } else { ?>

<a href="barang.php?aksi=tambah">Tambah Barang</a>

<table border="1">
<tr>
    <th>Kode</th>
    <th>Nama</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Aksi</th>
</tr>

<?php
$q = "SELECT * FROM barang";
$ex = mysqli_query($kon, $q);

while ($r = mysqli_fetch_array($ex)) {
    echo "<tr>";
    echo "<td>".$r['kd_barang']."</td>";
    echo "<td>".$r['nama_barang']."</td>";
    echo "<td>".$r['harga']."</td>";
    echo "<td>".$r['stok']."</td>";
    echo "<td>
            <a href='barang.php?aksi=edit&k=".$r['kd_barang']."'>Ubah</a> |
            <a href='barang.php?aksi=hapus&k=".$r['kd_barang']."' onclick=\"return confirm('Yakin hapus data ini?')\">Hapus</a>
          </td>";
    echo "</tr>";
}
?>
</table>

<?php } ?>