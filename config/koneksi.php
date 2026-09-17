<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_penjualan");
if (!$koneksi) die("Koneksi Database Gagal: " . mysqli_connect_error());
?>