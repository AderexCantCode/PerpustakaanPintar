<?php
include '../config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query untuk menghapus data petugas
    $sql = "DELETE FROM petugas WHERE PetugasID = $id";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data petugas berhasil dihapus!');
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
