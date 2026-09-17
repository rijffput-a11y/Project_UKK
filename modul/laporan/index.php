<?php
include '../../config/koneksi.php';
if (!isset($_SESSION['id_pengguna'])) { header("Location: ../../auth/login.php"); exit(); }

$faktur_id = $_GET['faktur_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan &amp; Cetak Faktur - Pengelolaan Manajemen Penjualan</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <h2>PENGELOLAAN MANAJEMEN PENJUALAN</h2>
        <div><a href="../../auth/logout.php"><?= $_SESSION['peran'] == 'Admin' ? 'Administrator' : 'Petugas Penjualan'; ?> / Logout</a></div>
    </div>
    <div class="sub-nav">
        <a href="../../dashboard.php">Dashboard</a>
        <a href="../produk/index.php">Data Produk</a>
        <a href="../pelanggan/index.php">Data Pelanggan</a>
        <a href="../penjualan/transaksi.php">Transaksi Penjualan</a>
        <a href="index.php" class="active">[ Laporan &amp; Faktur ]</a>
    </div>
    <div class="container" style="max-width:1100px;">
        <div style="display:flex; gap:20px;">
            <div class="card" style="flex:1.4;">
                <h3>REKAPITULASI LAPORAN PENJUALAN</h3>
                <form method="GET" style="display:flex; gap:10px; align-items:center; margin:15px 0;">
                    <label style="font-size:12px; font-weight:bold;">Periode :</label>
                    <input type="text" name="tgl_awal" class="form-control" value="01/09/2026" style="width:100px;">
                    <label style="font-size:12px;">s/d</label>
                    <input type="text" name="tgl_akhir" class="form-control" value="15/09/2026" style="width:100px;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID Trans</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($koneksi, "SELECT p.id_penjualan, p.tanggal, pl.nama_pelanggan, p.total_harga 
                                                        FROM penjualan p 
                                                        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                                                        ORDER BY p.id_penjualan DESC");
                        while($r = mysqli_fetch_assoc($q)):
                        ?>
                        <tr onclick="window.location.href='index.php?faktur_id=<?= $r['id_penjualan']; ?>'" style="cursor:pointer;">
                            <td>TRX-00<?= $r['id_penjualan']; ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                            <td><?= $r['nama_pelanggan']; ?></td>
                            <td>Rp <?= number_format($r['total_harga'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card" style="flex:1;">
                <h3>PREVIEW FAKTUR</h3>
                <?php
                if ($faktur_id):
                    $q_tx = mysqli_query($koneksi, "SELECT p.*, pl.nama_pelanggan FROM penjualan p JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan WHERE p.id_penjualan='$faktur_id'");
                    $tx   = mysqli_fetch_assoc($q_tx);
                ?>
                <div style="border:1px dashed #64748b; padding:15px; margin-top:15px; font-family:monospace; font-size:12px;">
                    <div style="text-align:center; font-weight:bold;">FAKTUR PENJUALAN<br>------------------------</div>
                    No  : TRX-00<?= $tx['id_penjualan']; ?><br>
                    Tgl : <?= date('d/m/Y', strtotime($tx['tanggal'])); ?><br>
                    Plg : <?= $tx['nama_pelanggan']; ?><br>
                    ------------------------<br>
                    <?php
                    $q_det = mysqli_query($koneksi, "SELECT d.*, pr.nama_produk FROM detail_penjualan d JOIN produk pr ON d.id_produk=pr.id_produk WHERE d.id_penjualan='$faktur_id'");
                    while($d = mysqli_fetch_assoc($q_det)):
                    ?>
                    <?= $d['jumlah']; ?>x <?= $d['nama_produk']; ?> Rp <?= number_format($d['subtotal'], 0, ',', '.'); ?><br>
                    <?php endwhile; ?>
                    ------------------------<br>
                    <strong>TOTAL : Rp <?= number_format($tx['total_harga'], 0, ',', '.'); ?></strong><br><br>
                    <div style="text-align:center;">Terima Kasih</div>
                </div>
                <a href="../penjualan/faktur.php?id=<?= $faktur_id; ?>" target="_blank" style="text-decoration:none;">
                    <button class="btn btn-success" style="width:100%; margin-top:15px;">[PRINT FAKTUR]</button>
                </a>
                <?php else: ?>
                    <p style="font-size:12px; color:#64748b; margin-top:15px; text-align:center;">Klik salah satu transaksi di sebelah kiri untuk melihat preview faktur.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>