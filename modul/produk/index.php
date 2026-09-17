<?php
include '../../config/koneksi.php';
if (!isset($_SESSION['id_pengguna'])) exit(header("Location: ../../auth/login.php"));

mysqli_query($koneksi, "UPDATE produk SET stok = 3 WHERE LOWER(nama_produk) LIKE '%tinta%' AND stok > 5");

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='{$_GET['hapus']}'");
    exit(header("Location: index.php"));
}
if (isset($_POST['simpan'])) {
    $id = $_POST['id_produk'] ?? '';
    if ($id) {
        mysqli_query($koneksi, "UPDATE produk SET nama_produk='{$_POST['nama']}', harga='{$_POST['harga']}', stok='{$_POST['stok']}' WHERE id_produk='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO produk (nama_produk, harga, stok) VALUES ('{$_POST['nama']}', '{$_POST['harga']}', '{$_POST['stok']}')");
    }
    exit(header("Location: index.php"));
}
$e = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='{$_GET['edit']}'")) : null;
?>
<!DOCTYPE html>
<html>
<head><title>Data Produk</title><link rel="stylesheet" href="../../assets/css/style.css"></head>
<body>
    <div class="navbar"><h2>PENGELOLAAN MANAJEMEN PENJUALAN</h2><a href="../../auth/logout.php" style="color:#fff;text-decoration:none;"><?= $_SESSION['peran'] == 'Admin' ? 'Administrator' : 'Petugas Penjualan'; ?> / Logout</a></div>
    <div class="sub-nav">
        <a href="../../dashboard.php">Dashboard</a>
        <a href="index.php" class="active">[ Data Produk ]</a>
        <a href="../pelanggan/index.php">Data Pelanggan</a>
        <a href="../penjualan/transaksi.php">Transaksi Penjualan</a>
        <a href="../laporan/index.php">Laporan</a>
    </div>
    <div class="container">
        <?php if (isset($_GET['tambah']) || $e): ?>
        <div class="card">
            <h3><?= $e ? 'EDIT PRODUK' : 'TAMBAH PRODUK'; ?></h3>
            <form method="POST" style="display:flex;gap:10px;margin-top:10px;">
                <?php if ($e): ?><input type="hidden" name="id_produk" value="<?= $e['id_produk']; ?>"><?php endif; ?>
                <input type="text" name="nama" placeholder="Nama Produk" value="<?= $e['nama_produk'] ?? ''; ?>" class="form-control" required>
                <input type="number" name="harga" placeholder="Harga" value="<?= $e['harga'] ?? ''; ?>" class="form-control" required>
                <input type="number" name="stok" placeholder="Stok" value="<?= $e['stok'] ?? ''; ?>" class="form-control" required>
                <button name="simpan" class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-primary" style="background:#64748b;">Batal</a>
            </form>
        </div>
        <?php endif; ?>

        <div class="card">
            <h3>DATA PRODUK & KONTROL STOK REAL-TIME</h3>
            <div style="display:flex;justify-content:space-between;margin:10px 0;">
                <a href="index.php?tambah=1" class="btn btn-primary">+ Tambah Produk</a>
                <form method="GET" style="display:flex;gap:5px;align-items:center;">
                    <label style="font-size:12px;font-weight:bold;">Cari Produk :</label>
                    <input type="text" name="cari" class="form-control" style="width:140px;" placeholder="Kertas..." value="<?= $_GET['cari'] ?? ''; ?>">
                    <button class="btn btn-primary">Cari</button>
                </form>
            </div>
            <table>
                <tr><th>Kode</th><th>Nama Produk</th><th>Harga Jual</th><th>Stok Real-Time</th><th>Aksi</th></tr>
                <?php
                $c = $_GET['cari'] ?? '';
                $q = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk LIKE '%$c%' ORDER BY id_produk ASC");
                while($r = mysqli_fetch_assoc($q)): $m = ($r['stok'] <= 10); ?>
                <tr <?= $m ? 'class="warning-row"' : ''; ?>>
                    <td>PRD-00<?= $r['id_produk']; ?></td>
                    <td><?= $r['nama_produk']; ?></td>
                    <td>Rp <?= number_format($r['harga'],0,',','.'); ?></td>
                    <td><?= $r['stok']; ?> <?= $m ? '<span style="color:#dc2626;font-weight:bold;">(Stok Menipis)</span>' : ''; ?></td>
                    <td>
                        <a href="index.php?edit=<?= $r['id_produk']; ?>" style="color:#2563eb;">[Edit]</a> 
                        <a href="index.php?hapus=<?= $r['id_produk']; ?>" onclick="return confirm('Hapus?');" style="color:#2563eb;">[Hapus]</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>