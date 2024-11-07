<?php
$host = "localhost";
$port = "3050";
$database = "Perpustakaan";
$username = "root";
$password = "Panzerfaust6187";

$conn = mysqli_connect($host, $username, $password, $database, $port);

// Cek koneksi
if (!$conn) {
    die("<script>console.log(`Koneksi Gagal`)</script>" . mysqli_connect_error());
}
echo "<script>console.log(`Koneksi Berhasul`)</script>";
?>