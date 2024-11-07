<?php
include '../config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM anggota WHERE AnggotaID = $id";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['AnggotaID'];
    $namaAnggota = $_POST['NamaAnggota'];
    $alamat = $_POST['Alamat']; 
    $tanggalLahir = $_POST['TanggalLahir'];
    $kontak = $_POST['Kontak'];

    $sql = "UPDATE anggota SET 
            NamaAnggota = '$namaAnggota',
            Alamat = '$alamat',
            TanggalLahir = '$tanggalLahir',
            Kontak = '$kontak'
            WHERE AnggotaID = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data anggota berhasil diperbarui!');
                window.location.href='list.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href='edit.php?id=$id';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - Perpustakaan</title>
    
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
                <i class="fas fa-user-edit text-4xl text-gray-800 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800">Edit Data Anggota</h2>
            </div>

            <form action="edit.php" method="POST">
                <input type="hidden" name="AnggotaID" value="<?= $data['AnggotaID'] ?>">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Anggota</label>
                        <input type="text" name="NamaAnggota" value="<?= $data['NamaAnggota'] ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
                        <textarea name="Alamat" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required><?= $data['Alamat'] ?></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Lahir</label>
                        <input type="date" name="TanggalLahir" value="<?= $data['TanggalLahir'] ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Kontak</label>
                        <input type="text" name="Kontak" value="<?= $data['Kontak'] ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-gray-800" required>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="list.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </div>
