<?php
include '../config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil BukuID dari peminjaman yang akan dihapus
    $getBukuID = mysqli_query($conn, "SELECT BukuID FROM peminjaman WHERE PeminjamanID = '$id'");
    $bukuID = mysqli_fetch_assoc($getBukuID)['BukuID'];
    
    // Kembalikan stok buku
    mysqli_query($conn, "UPDATE buku SET JumlahStok = JumlahStok + 1 WHERE BukuID = '$bukuID'");
    
    // Hapus data peminjaman
    $query = "DELETE FROM peminjaman WHERE PeminjamanID = '$id'";
    
    if(mysqli_query($conn, $query)) {
        echo "<script>
                alert('Data peminjaman berhasil dihapus!');
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
