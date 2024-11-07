<?php
include '../config.php';

// Ambil ID buku dari parameter URL
$id = $_GET['id'];

// Query untuk mengambil data buku berdasarkan ID
$sql = "SELECT * FROM buku WHERE BukuID = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judulBuku = $_POST['JudulBuku'];
    $pengarang = $_POST['Pengarang'];
    $penerbit = $_POST['Penerbit']; 
    $tahunTerbit = $_POST['TahunTerbit'];
    $kategori = $_POST['Kategori'];
    $jumlahStok = $_POST['JumlahStok'];

    $sql = "UPDATE buku SET 
            JudulBuku = '$judulBuku',
            Pengarang = '$pengarang',
            Penerbit = '$penerbit',
            TahunTerbit = '$tahunTerbit',
            Kategori = '$kategori',
            JumlahStok = '$jumlahStok'
            WHERE BukuID = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data buku berhasil diperbarui!');
                window.location.href='list.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Perpustakaan</title>
    
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="bg-white shadow-sm mb-8" data-aos="fade-down">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="text-xl font-bold text-gray-800 hover:text-gray-600 transition duration-300">
                    <i class="fas fa-book-reader mr-2"></i>
                    Smart Library
                </a>
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-600 hover:text-gray-800 transition duration-300">
                        <i class="fas fa-home mr-1"></i> Beranda
                    </a>
                    <a href="../masterdata.php" class="text-gray-600 hover:text-gray-800 transition duration-300">
                        <i class="fas fa-database mr-1"></i> Master Data
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto" data-aos="fade-up">
            <div class="flex items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-edit mr-2"></i>Edit Buku
                </h2>
            </div>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Buku</label>
                    <input type="text" name="JudulBuku" value="<?= $row['JudulBuku'] ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pengarang</label>
                    <input type="text" name="Pengarang" value="<?= $row['Pengarang'] ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                    <input type="text" name="Penerbit" value="<?= $row['Penerbit'] ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Terbit</label>
                    <input type="number" name="TahunTerbit" value="<?= $row['TahunTerbit'] ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="Kategori" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="Sains" <?= ($row['Kategori'] == 'Sains') ? 'selected' : '' ?>>Sains</option>
                        <option value="Sejarah" <?= ($row['Kategori'] == 'Sejarah') ? 'selected' : '' ?>>Sejarah</option>
                        <option value="Pelajaran" <?= ($row['Kategori'] == 'Pelajaran') ? 'selected' : '' ?>>Pelajaran</option>
                        <option value="Fiksi" <?= ($row['Kategori'] == 'Fiksi') ? 'selected' : '' ?>>Fiksi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Stok</label>
                    <input type="number" name="JumlahStok" value="<?= $row['JumlahStok'] ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="list.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>
