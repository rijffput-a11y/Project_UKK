<?php
include '../../config/koneksi.php';
$id = $_GET['id'];
$tx = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT p.*, pl.nama_pelanggan, pg.nama_pengguna FROM penjualan p JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan JOIN pengguna pg ON p.id_pengguna=pg.id_pengguna WHERE p.id_penjualan='$id'"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Faktur TRX-00<?= $tx['id_penjualan']; ?></title>
    <style>
        body { font-family: monospace; font-size: 12px; padding: 20px; }
        .receipt { width: 260px; border: 1px dashed #000; padding: 12px; margin: auto; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
    <div class="receipt">
        <div style="text-align:center;"><strong>FAKTUR PENJUALAN</strong><br>------------------------</div>
        No : TRX-00<?= $tx['id_penjualan']; ?><br>
        Tgl: <?= date('d/m/Y H:i', strtotime($tx['tanggal'])); ?><br>
        Plg: <?= $tx['nama_pelanggan']; ?><br>
        Kasir: <?= $tx['nama_pengguna']; ?><br>
        ------------------------<br>
        <?php
        $qd = mysqli_query($koneksi, "SELECT d.*, pr.nama_produk FROM detail_penjualan d JOIN produk pr ON d.id_produk=pr.id_produk WHERE d.id_penjualan='$id'");
        while($d = mysqli_fetch_assoc($qd)): ?>
        <?= $d['jumlah']; ?>x <?= $d['nama_produk']; ?><span style="float:right;">Rp <?= number_format($d['subtotal'],0,',','.'); ?></span><br>
        <?php endwhile; ?>
        ------------------------<br>
        <strong>TOTAL: <span style="float:right;">Rp <?= number_format($tx['total_harga'],0,',','.'); ?></span></strong><br><br>
        <div style="text-align:center;">Terima Kasih</div>
        <button onclick="window.print()" style="margin-top:10px; width:100%; padding:6px; background:#10b981; color:#fff; border:none; cursor:pointer;">[ PRINT FAKTUR ]</button>
    </div>
</body>
</html>