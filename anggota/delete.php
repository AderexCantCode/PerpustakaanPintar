<?php
include '../config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query untuk menghapus data anggota
    $sql = "DELETE FROM anggota WHERE AnggotaID = $id";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data anggota berhasil dihapus!');
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
