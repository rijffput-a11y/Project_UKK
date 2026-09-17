<?php
include '../../config/koneksi.php';
if (!isset($_SESSION['id_pengguna'])) exit(header("Location: ../../auth/login.php"));
?>
<!DOCTYPE html>
<html>
<head><title>Data Pelanggan</title><link rel="stylesheet" href="../../assets/css/style.css"></head>
<body>
    <div class="navbar">
        <h2>PENGELOLAAN MANAJEMEN PENJUALAN</h2>
        <div><a href="../../auth/logout.php"><?= $_SESSION['peran'] == 'Admin' ? 'Administrator' : 'Petugas Penjualan'; ?> / Logout</a></div>
    </div>
    <div class="sub-nav">
        <a href="../../dashboard.php">Dashboard</a>
        <a href="../produk/index.php">Data Produk</a>
        <a href="index.php" class="active">[ Data Pelanggan ]</a>
        <a href="../penjualan/transaksi.php">Transaksi Penjualan</a>
        <a href="../laporan/index.php">Laporan</a>
    </div>
    <div class="container">
        <div class="card">
            <h3 style="margin-bottom:15px;">DATA PELANGGAN (MEMBER)</h3>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                <button class="btn btn-primary">+ Tambah Pelanggan</button>
                <form method="GET" style="display:flex;gap:5px;align-items:center;">
                    <label style="font-size:12px;font-weight:bold;">Cari Pelanggan :</label>
                    <input type="text" name="cari" class="form-control" style="width:160px;" placeholder="Budi...">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nama Pelanggan</th><th>Alamat</th><th>No. Telepon</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php
                    $cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
                    $q = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE nama_pelanggan LIKE '%$cari%' ORDER BY id_pelanggan ASC");
                    while($r = mysqli_fetch_assoc($q)): ?>
                    <tr>
                        <td>PEL-00<?= $r['id_pelanggan']; ?></td>
                        <td><?= $r['nama_pelanggan']; ?></td>
                        <td><?= $r['alamat']; ?></td>
                        <td><?= $r['telepon']; ?></td>
                        <td><a href="#" style="color:#2563eb;font-size:12px;">[Edit]</a> <a href="#" style="color:#2563eb;font-size:12px;">[Hapus]</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>