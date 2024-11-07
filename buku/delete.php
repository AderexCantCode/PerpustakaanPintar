<?php
include '../config.php';

// Cek apakah ada parameter id
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query untuk menghapus buku
    $sql = "DELETE FROM buku WHERE BukuID = $id";
    
    if(mysqli_query($conn, $sql)) {
        // Jika berhasil dihapus, tampilkan pesan sukses
        echo "<script>
            window.location.href = 'list.php';
            alert('Data buku berhasil dihapus!');
        </script>";
    } else {
        // Jika gagal, tampilkan pesan error
        echo "<script>
            window.location.href = 'list.php'; 
            alert('Gagal menghapus data buku: " . mysqli_error($conn) . "');
        </script>";
    }
} else {
    // Jika tidak ada parameter id, kembali ke halaman list
    header("Location: list.php");
}
?>
