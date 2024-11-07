<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaPetugas = $_POST['NamaPetugas']; 
    $jabatan = $_POST['Jabatan'];
    $kontak = $_POST['Kontak'];

    $sql = "INSERT INTO petugas (NamaPetugas, Jabatan, Kontak) VALUES ('$namaPetugas', '$jabatan', '$kontak')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data petugas berhasil ditambahkan!');
                window.location.href='../index.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href='add.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Petugas - Perpustakaan</title>
    
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
                <i class="fas fa-user-plus text-4xl text-gray-800 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800">Tambah Petugas Baru</h2>
            </div>

            <form action="add.php" method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Petugas</label>
                        <input type="text" name="NamaPetugas" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Jabatan</label>
                        <select name="Jabatan" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Pustakawan">Pustakawan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor Telepon</label>
                        <input type="tel" name="Kontak" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                    </div>

                    <div class="flex justify-between space-x-4 mt-8">
                        <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 w-1/2 text-center">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                        <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 w-1/2">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
