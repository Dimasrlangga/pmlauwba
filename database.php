<?php
$servername = "localhost";
$username = "root"; // Default Laragon/XAMPP biasanya root
$password = "";     // Default Laragon/XAMPP biasanya kosong
$dbname = "pmlauwba"; // Sesuaikan dengan nama database Anda

// Membuat koneksi
$koneksi = mysqli_connect($servername, $username, $password, $dbname);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>