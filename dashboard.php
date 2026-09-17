<?php
include 'config/koneksi.php';
if (!isset($_SESSION['id_pengguna'])) exit(header("Location: auth/login.php"));

$omzet_tot  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) AS t FROM penjualan"))['t'] ?? 0;
$produk     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS t FROM produk"))['t'] ?? 0;
$pelanggan  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS t FROM pelanggan"))['t'] ?? 0;
$omzet_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) AS t FROM penjualan WHERE DATE(tanggal)=CURDATE()"))['t'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
    <div class="navbar"><h2>PENGELOLAAN MANAJEMEN PENJUALAN</h2><div><a href="auth/logout.php"><?= $_SESSION['peran'] == 'Admin' ? 'Administrator' : 'Petugas Penjualan'; ?> / Logout</a></div></div>
    <div class="sub-nav">
        <a href="dashboard.php" class="active">[ Dashboard ]</a>
        <a href="modul/produk/index.php">Data Produk</a>
        <a href="modul/pelanggan/index.php">Data Pelanggan</a>
        <a href="modul/penjualan/transaksi.php">Transaksi Penjualan</a>
        <a href="modul/laporan/index.php">Laporan</a>
    </div>
    <div class="container">
        <!-- Urutan: 1. Total Omzet | 2. Total Produk | 3. Pelanggan | 4. Transaksi Hari Ini -->
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:20px;">
            <div class="card"><h4>TOTAL OMZET</h4><p style="font-size:18px;font-weight:bold;color:#2563eb;">Rp <?= number_format($omzet_tot,0,',','.'); ?></p></div>
            <div class="card"><h4>TOTAL PRODUK</h4><p style="font-size:18px;font-weight:bold;"><?= $produk; ?> Barang</p></div>
            <div class="card"><h4>PELANGGAN</h4><p style="font-size:18px;font-weight:bold;"><?= $pelanggan; ?> Member</p></div>
            <div class="card"><h4>TRANSAKSI HARI INI</h4><p style="font-size:18px;font-weight:bold;color:#10b981;">Rp <?= number_format($omzet_hari,0,',','.'); ?></p></div>
        </div>
        <div class="card">
            <h3>TRANSAKSI TERAKHIR (REAL-TIME)</h3>
            <table>
                <tr><th>ID Transaksi</th><th>Tanggal</th><th>Pelanggan</th><th>Total Harga</th><th>Status</th></tr>
                <?php
                $q = mysqli_query($koneksi, "SELECT p.id_penjualan, p.tanggal, pl.nama_pelanggan, p.total_harga FROM penjualan p JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan ORDER BY p.id_penjualan DESC LIMIT 5");
                while($r = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td>TRX-00<?= $r['id_penjualan']; ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($r['tanggal'])); ?></td>
                    <td><?= $r['nama_pelanggan']; ?></td>
                    <td>Rp <?= number_format($r['total_harga'],0,',','.'); ?></td>
                    <td style="color:#10b981;font-weight:bold;">Selesai</td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>