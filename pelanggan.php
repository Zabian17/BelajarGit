<?php
include "koneksi.php";

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if (isset($_POST['bok'])) {
    $kp  = $_POST['tkode'];
    $np  = $_POST['tnama'];
    $al  = $_POST['talamat'];
    $tlp = $_POST['ttelp'];

    if (isset($_POST['tmode']) && $_POST['tmode'] == 'edit') {
        $q = "UPDATE pelanggan SET
              nama_pelanggan='$np',
              alamat='$al',
              no_telp='$tlp'
              WHERE kd_pelanggan='$kp'";
    } else {
        $q = "INSERT INTO pelanggan
              (kd_pelanggan,nama_pelanggan,alamat,no_telp)
              VALUES
              ('$kp','$np','$al','$tlp')";
    }

    mysqli_query($kon, $q);
    header("Location:pelanggan.php");
}

$pesan = '';
if ($aksi == 'hapus') {
    $kp = $_GET['k'];

    $cek = mysqli_query($kon, "SELECT * FROM penjualan WHERE kd_pelanggan='$kp'");

    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Pelanggan tidak bisa dihapus karena sudah memiliki riwayat transaksi penjualan.";
    } else {
        $q = "DELETE FROM pelanggan
              WHERE kd_pelanggan='$kp'";

        mysqli_query($kon, $q);
        header("Location:pelanggan.php");
        exit;
    }
}

if ($aksi == 'edit') {
    $kp = $_GET['k'];

    $q = "SELECT * FROM pelanggan
          WHERE kd_pelanggan='$kp'";

    $ex = mysqli_query($kon, $q);
    $r = mysqli_fetch_array($ex);
}
?>

<h2>Data Pelanggan</h2>

<?php if ($pesan) { ?>
<p style="color:red;"><?php echo $pesan; ?></p>
<?php } ?>

<?php if ($aksi == 'tambah' || $aksi == 'edit') { ?>

<h3><?php echo $aksi == 'edit' ? 'Ubah Data Pelanggan' : 'Input Data Pelanggan'; ?></h3>

<form action="#" method="post">
    <?php if ($aksi == 'edit') { ?>
    <input type="hidden" name="tmode" value="edit">
    <?php } ?>

    Kode Pelanggan :
    <input type="text" name="tkode"
    value="<?php echo $aksi=='edit' ? $r['kd_pelanggan'] : ''; ?>"
    <?php echo $aksi=='edit' ? 'readonly' : ''; ?>><br>

    Nama Pelanggan :
    <input type="text" name="tnama"
    value="<?php echo $aksi=='edit' ? $r['nama_pelanggan'] : ''; ?>"><br>

    Alamat :
    <input type="text" name="talamat"
    value="<?php echo $aksi=='edit' ? $r['alamat'] : ''; ?>"><br>

    No Telp :
    <input type="text" name="ttelp"
    value="<?php echo $aksi=='edit' ? $r['no_telp'] : ''; ?>"><br>

    <input type="submit" name="bok"
    value="<?php echo $aksi=='edit' ? 'Ubah Data' : 'Simpan'; ?>">
</form>

<a href="pelanggan.php">Kembali</a>

<?php } else { ?>

<a href="pelanggan.php?aksi=tambah">Tambah Pelanggan</a>

<table border="1">
<tr>
    <th>Kode</th>
    <th>Nama</th>
    <th>Alamat</th>
    <th>No Telp</th>
    <th>Aksi</th>
</tr>

<?php
$q = "SELECT * FROM pelanggan";
$ex = mysqli_query($kon, $q);

while ($r = mysqli_fetch_array($ex)) {
    echo "<tr>";
    echo "<td>".$r['kd_pelanggan']."</td>";
    echo "<td>".$r['nama_pelanggan']."</td>";
    echo "<td>".$r['alamat']."</td>";
    echo "<td>".$r['no_telp']."</td>";
    echo "<td>
            <a href='pelanggan.php?aksi=edit&k=".$r['kd_pelanggan']."'>Ubah</a> |
            <a href='pelanggan.php?aksi=hapus&k=".$r['kd_pelanggan']."' onclick=\"return confirm('Yakin hapus data ini?')\">Hapus</a>
          </td>";
    echo "</tr>";
}
?>
</table>

<?php } ?>