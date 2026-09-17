<?php
include '../../config/koneksi.php';
if (!isset($_SESSION['id_pengguna'])) exit(header("Location: ../../auth/login.php"));
if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];

if (isset($_POST['tambah_barang'])) {
    $id_p = $_POST['id_produk'];
    $qty  = $_POST['jumlah'];
    $p    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id_p'"));
    if ($p && $p['stok'] >= $qty) {
        $_SESSION['keranjang'][] = [
            'id_produk'   => $p['id_produk'],
            'nama_produk' => $p['nama_produk'],
            'harga'       => $p['harga'],
            'qty'         => $qty,
            'subtotal'    => $p['harga'] * $qty
        ];
    }
}

if (isset($_POST['simpan_transaksi']) && !empty($_SESSION['keranjang'])) {
    $id_pel = $_POST['id_pelanggan'];
    $id_pen = $_SESSION['id_pengguna'];
    $total  = array_sum(array_column($_SESSION['keranjang'], 'subtotal'));

    mysqli_query($koneksi, "INSERT INTO penjualan (id_pelanggan, id_pengguna, total_harga) VALUES ('$id_pel', '$id_pen', '$total')");
    $id_tx = mysqli_insert_id($koneksi);

    foreach ($_SESSION['keranjang'] as $item) {
        mysqli_query($koneksi, "INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah, harga, subtotal) VALUES ('$id_tx', '{$item['id_produk']}', '{$item['qty']}', '{$item['harga']}', '{$item['subtotal']}')");
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - {$item['qty']} WHERE id_produk='{$item['id_produk']}'");
    }

    $_SESSION['keranjang'] = [];
    exit(header("Location: ../laporan/index.php?faktur_id=" . $id_tx));
}
?>
<!DOCTYPE html>
<html>
<head><title>Form Transaksi Penjualan</title><link rel="stylesheet" href="../../assets/css/style.css"></head>
<body>
    <div class="navbar">
        <h2>PENGELOLAAN MANAJEMEN PENJUALAN</h2>
        <div><a href="../../auth/logout.php"><?= $_SESSION['peran'] == 'Admin' ? 'Administrator' : 'Petugas Penjualan'; ?> / Logout</a></div>
    </div>
    <div class="sub-nav">
        <a href="../../dashboard.php">Dashboard</a>
        <a href="../produk/index.php">Data Produk</a>
        <a href="../pelanggan/index.php">Data Pelanggan</a>
        <a href="transaksi.php" class="active">[ Form Transaksi Penjualan ]</a>
        <a href="../laporan/index.php">Laporan</a>
    </div>
    <div class="container">
        <div class="card">
            <h3 style="margin-bottom:15px;">FORM TRANSAKSI PENJUALAN & POTONG STOK REAL-TIME</h3>
            <form method="POST">
                <div style="display:flex;gap:15px;align-items:flex-end;margin-bottom:15px;">
                    <div class="form-group" style="flex:1;margin:0;">
                        <label>Cari Pelanggan :</label>
                        <select name="id_pelanggan" class="form-control" required>
                            <?php
                            $q_plg = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                            while($plg = mysqli_fetch_assoc($q_plg)) {
                                echo "<option value='".$plg['id_pelanggan']."'>".$plg['nama_pelanggan']." (PEL-00".$plg['id_pelanggan'].")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;margin:0;">
                        <label>Pilih Produk :</label>
                        <select name="id_produk" class="form-control">
                            <?php
                            $q_p = mysqli_query($koneksi, "SELECT * FROM produk");
                            while($p = mysqli_fetch_assoc($q_p)) {
                                echo "<option value='".$p['id_produk']."'>".$p['nama_produk']." [Stok: ".$p['stok']."]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="display:flex;gap:15px;align-items:center;margin-bottom:15px;">
                    <div class="form-group" style="display:flex;align-items:center;gap:10px;margin:0;">
                        <label style="margin:0;">Jumlah Qty :</label>
                        <input type="number" name="jumlah" class="form-control" value="2" min="1" style="width:70px;">
                    </div>
                    <button type="submit" name="tambah_barang" class="btn btn-primary">+ Tambah Barang</button>
                </div>
                <table>
                    <thead>
                        <tr><th>No</th><th>Nama Barang</th><th>Harga Jual</th><th>Qty</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; $total = 0;
                        if (!empty($_SESSION['keranjang'])):
                            foreach ($_SESSION['keranjang'] as $item): $total += $item['subtotal']; ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $item['nama_produk']; ?></td>
                            <td>Rp <?= number_format($item['harga'],0,',','.'); ?></td>
                            <td><?= $item['qty']; ?></td>
                            <td>Rp <?= number_format($item['subtotal'],0,',','.'); ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5" style="text-align:center;">Belum ada barang ditambahkan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <h3 style="text-align:right;margin:20px 0 15px 0;">TOTAL PENJUALAN : Rp <?= number_format($total,0,',','.'); ?></h3>
                <button type="submit" name="simpan_transaksi" class="btn btn-success" style="float:right;padding:10px 20px;">SIMPAN TRANSAKSI & CETAK FAKTUR</button>
                <div style="clear:both;"></div>
            </form>
        </div>
    </div>
</body>
</html>