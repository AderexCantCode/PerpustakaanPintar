<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bukuID = $_POST['BukuID'];
    $anggotaID = $_POST['AnggotaID']; 
    $petugasID = $_POST['PetugasID'];
    $tanggalPinjam = $_POST['TanggalPeminjaman'];
    $tanggalKembali = $_POST['TanggalPengembalian'];

    // Cek stok buku
    $cekStok = mysqli_query($conn, "SELECT JumlahStok FROM buku WHERE BukuID = '$bukuID'");
    $stok = mysqli_fetch_assoc($cekStok);

    if($stok['JumlahStok'] > 0) {
        // Kurangi stok
        mysqli_query($conn, "UPDATE buku SET JumlahStok = JumlahStok - 1 WHERE BukuID = '$bukuID'");
        
        // Insert data peminjaman
        $sql = "INSERT INTO peminjaman (BukuID, AnggotaID, PetugasID, TanggalPinjam, TanggalKembali) 
                VALUES ('$bukuID', '$anggotaID', '$petugasID', '$tanggalPinjam', '$tanggalKembali')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>
                    alert('Peminjaman berhasil ditambahkan!');
                    window.location.href='list.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.location.href='add.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Stok buku tidak tersedia!');
                window.location.href='add.php';
              </script>";
    }
}

// Query untuk dropdown - hanya tampilkan buku dengan stok > 0
$bukuQuery = mysqli_query($conn, "SELECT * FROM buku WHERE JumlahStok > 0");
$anggotaQuery = mysqli_query($conn, "SELECT * FROM anggota");
$petugasQuery = mysqli_query($conn, "SELECT * FROM petugas");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman - Perpustakaan</title>
    
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="text-center mb-8">
                <i class="fas fa-book-reader text-4xl text-gray-800 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800">Tambah Peminjaman Baru</h2>
            </div>
            <form action="add.php" method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Buku</label>
                        <select name="BukuID" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                            <option value="">Pilih Buku</option>
                            <?php while($buku = mysqli_fetch_assoc($bukuQuery)) { ?>
                                <option value="<?= $buku['BukuID'] ?>"><?= $buku['JudulBuku'] ?> (Stok: <?= $buku['JumlahStok'] ?>)</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Anggota</label>
                        <select name="AnggotaID" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                            <option value="">Pilih Anggota</option>
                            <?php while($anggota = mysqli_fetch_assoc($anggotaQuery)) { ?>
                                <option value="<?= $anggota['AnggotaID'] ?>"><?= $anggota['NamaAnggota'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Petugas</label>
                        <select name="PetugasID" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                            <option value="">Pilih Petugas</option>
                            <?php while($petugas = mysqli_fetch_assoc($petugasQuery)) { ?>
                                <option value="<?= $petugas['PetugasID'] ?>"><?= $petugas['NamaPetugas'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Peminjaman</label>
                        <input type="date" name="TanggalPeminjaman" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Pengembalian</label>
                        <input type="date" name="TanggalPengembalian" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <a href="list.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                        <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition duration-300">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
