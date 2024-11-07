<?php
include '../config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil BukuID dan cek apakah kolom TanggalPengembalian ada
    $getBukuID = mysqli_query($conn, "SELECT BukuID FROM peminjaman WHERE PeminjamanID = '$id'");
    $bukuID = mysqli_fetch_assoc($getBukuID)['BukuID'];
    
    // Tambah kolom TanggalPengembalian jika belum ada
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM peminjaman LIKE 'TanggalPengembalian'");
    if(mysqli_num_rows($checkColumn) == 0) {
        mysqli_query($conn, "ALTER TABLE peminjaman ADD TanggalPengembalian DATE NULL");
    }
    
    // Kembalikan stok buku
    mysqli_query($conn, "UPDATE buku SET JumlahStok = JumlahStok + 1 WHERE BukuID = '$bukuID'");
    
    // Update tanggal pengembalian untuk menandai buku sudah dikembalikan
    $query = "UPDATE peminjaman SET TanggalPengembalian = CURRENT_DATE() WHERE PeminjamanID = '$id'";
    
    if(mysqli_query($conn, $query)) {
        echo "<script>
                alert('Buku berhasil dikembalikan!');
                window.location.href='list.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href='list.php';
              </script>";
    }
} else {
    header("Location: list.php");
}
?>