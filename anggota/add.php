<?php
include '../config.php';

// Proses tambah data
if(isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $TanggalLahir = mysqli_real_escape_string($conn, $_POST['TanggalLahir']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    
    // Validasi input
    $errors = array();
    
    if(empty($nama)) {
        $errors[] = "Nama anggota harus diisi";
    }
    
    if(empty($alamat)) {
        $errors[] = "Alamat harus diisi";
    }
    
    if(empty($TanggalLahir)) {
        $errors[] = "Tanggal lahir harus diisi";
    }
    
    if(empty($kontak)) {
        $errors[] = "Kontak harus diisi";
    }
    
    if(empty($errors)) {
        $sql = "INSERT INTO anggota (NamaAnggota, Alamat, TanggalLahir, Kontak) 
                VALUES ('$nama', '$alamat', '$TanggalLahir', '$kontak')";
                
        if(mysqli_query($conn, $sql)) {
            echo "<script>
                    alert('Data anggota berhasil ditambahkan!');
                    window.location.href='list.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                  </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota - Perpustakaan</title>
    
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
        
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="bg-white shadow-sm mb-8">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="text-xl font-bold text-gray-800">
                    <i class="fas fa-book-reader mr-2"></i>
                    Perpustakaan
                </a>
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-600 hover:text-gray-800 transition duration-200">
                        <i class="fas fa-home mr-1"></i> Beranda
                    </a>
                    <a href="../masterdata.php" class="text-gray-600 hover:text-gray-800 transition duration-200">
                        <i class="fas fa-database mr-1"></i> Master Data
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-user-plus mr-2"></i>Tambah Anggota Baru
                </h2>
                <p class="text-gray-600 mt-1">Silakan isi data anggota perpustakaan baru</p>
            </div>
            
            <?php if(!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form action="" method="POST" class="space-y-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Anggota</label>
                    <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 form-input" required>
                </div>
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 form-input" required></textarea>
                </div>
                <div>
                    <label for="TanggalLahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" name="TanggalLahir" id="TanggalLahir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 form-input" required>
                </div>
                <div>
                    <label for="kontak" class="block text-sm font-medium text-gray-700">Kontak</label>
                    <input type="text" name="kontak" id="kontak" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 form-input" required>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <a href="list.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
