<?php
include '../config/koneksi.php';
$pesan = '';
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $pass  = md5($_POST['password']);
    $peran = mysqli_real_escape_string($koneksi, $_POST['peran']);
    $res   = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE LOWER(email)=LOWER('$email') AND password='$pass' AND LOWER(peran)=LOWER('$peran')");
    if ($res && mysqli_num_rows($res) > 0) {
        $r = mysqli_fetch_assoc($res);
        $_SESSION['id_pengguna']   = $r['id_pengguna'];
        $_SESSION['nama_pengguna'] = $r['nama_pengguna'];
        $_SESSION['peran']         = $r['peran'];
        header("Location: ../dashboard.php"); exit();
    } else { $pesan = "Email, Password, atau Peran tidak cocok!"; }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - Manajemen Penjualan</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body style="display:flex;justify-content:center;align-items:center;min-height:100vh;background:#f1f5f9;">
    <div class="card" style="width:340px;">
        <h3 style="text-align:center;margin-bottom:5px;">PENGELOLAAN PENJUALAN</h3>
        <p style="text-align:center;font-size:11px;color:#64748b;margin-bottom:15px;">Sistem Informasi Manajemen Penjualan</p>
        <?php if($pesan): ?><p style="color:#dc2626;font-size:12px;margin-bottom:10px;text-align:center;font-weight:bold;"><?= $pesan; ?></p><?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" placeholder="admin@penjualan.com" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" placeholder="••••••••" required></div>
            <div class="form-group">
                <label>Peran (Role)</label>
                <select name="peran" class="form-control" required>
                    <option value="Admin">Administrator</option>
                    <option value="Petugas">Petugas Penjualan</option>
                </select>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%;margin-top:10px;">LOG IN</button>
        </form>
    </div>
</body>
</html>