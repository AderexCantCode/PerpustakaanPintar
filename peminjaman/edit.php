<?php
include '../config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil data peminjaman yang akan diedit
    $query = "SELECT p.*, b.JudulBuku, a.NamaAnggota, pt.NamaPetugas 
              FROM peminjaman p
              JOIN buku b ON p.BukuID = b.BukuID
              JOIN anggota a ON p.AnggotaID = a.AnggotaID
              JOIN petugas pt ON p.PetugasID = pt.PetugasID 
              WHERE p.PeminjamanID = '$id'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    // Ambil data untuk dropdown
    $bukuQuery = "SELECT * FROM buku WHERE JumlahStok > 0 OR BukuID = '{$data['BukuID']}'";
    $anggotaQuery = "SELECT * FROM anggota";
    $petugasQuery = "SELECT * FROM petugas";
    
    $bukuResult = mysqli_query($conn, $bukuQuery);
    $anggotaResult = mysqli_query($conn, $anggotaQuery);
    $petugasResult = mysqli_query($conn, $petugasQuery);
}

if(isset($_POST['submit'])) {
    $bukuID = $_POST['bukuID'];
    $anggotaID = $_POST['anggotaID'];
    $petugasID = $_POST['petugasID'];
    $tglPinjam = $_POST['tglPinjam'];
    $tglKembali = $_POST['tglKembali'];
    
    // Update stok buku lama (tambah 1)
    mysqli_query($conn, "UPDATE buku SET JumlahStok = JumlahStok + 1 WHERE BukuID = '{$data['BukuID']}'");
    
    // Update stok buku baru (kurang 1)
    mysqli_query($conn, "UPDATE buku SET JumlahStok = JumlahStok - 1 WHERE BukuID = '$bukuID'");
    
    $updateQuery = "UPDATE peminjaman SET 
                   BukuID = '$bukuID',
                   AnggotaID = '$anggotaID', 
                   PetugasID = '$petugasID',
                   TanggalPinjam = '$tglPinjam',
                   TanggalKembali = '$tglKembali'
                   WHERE PeminjamanID = '$id'";

    if(mysqli_query($conn, $updateQuery)) {
        echo "<script>
                alert('Data peminjaman berhasil diupdate!');
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
    <title>Edit Peminjaman - Perpustakaan</title>
    
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
    <nav class="bg-white text-gray-800 p-4 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="flex items-center space-x-2">
                <i class="fas fa-book-reader text-3xl text-gray-800"></i>
                <h1 class="text-2xl font-bold">Smart Library</h1>
            </a>
            <div class="space-x-6">
                <a href="../index.php" class="nav-link"><i class="fas fa-home mr-2"></i>Beranda</a>
                <a href="../katalog.php" class="nav-link"><i class="fas fa-book mr-2"></i>Katalog</a>
                <a href="../masterdata.php" class="nav-link"><i class="fas fa-database mr-2"></i>Master Data</a>
                <a href="../peminjaman/list.php" class="nav-link"><i class="fas fa-clipboard-list mr-2"></i>Peminjaman</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6" data-aos="fade-up">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-edit mr-2"></i>Edit Peminjaman
            </h2>
            
            <form action="" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bukuID">
                        Buku
                    </label>
                    <select name="bukuID" id="bukuID" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <?php while($buku = mysqli_fetch_assoc($bukuResult)) { ?>
                            <option value="<?= $buku['BukuID'] ?>" <?= ($buku['BukuID'] == $data['BukuID']) ? 'selected' : '' ?>>
                                <?= $buku['JudulBuku'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="anggotaID">
                        Anggota
                    </label>
                    <select name="anggotaID" id="anggotaID" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <?php while($anggota = mysqli_fetch_assoc($anggotaResult)) { ?>
                            <option value="<?= $anggota['AnggotaID'] ?>" <?= ($anggota['AnggotaID'] == $data['AnggotaID']) ? 'selected' : '' ?>>
                                <?= $anggota['NamaAnggota'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="petugasID">
                        Petugas
                    </label>
                    <select name="petugasID" id="petugasID" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <?php while($petugas = mysqli_fetch_assoc($petugasResult)) { ?>
                            <option value="<?= $petugas['PetugasID'] ?>" <?= ($petugas['PetugasID'] == $data['PetugasID']) ? 'selected' : '' ?>>
                                <?= $petugas['NamaPetugas'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tglPinjam">
                        Tanggal Pinjam
                    </label>
                    <input type="date" name="tglPinjam" id="tglPinjam" 
                           value="<?= $data['TanggalPinjam'] ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tglKembali">
                        Tanggal Kembali
                    </label>
                    <input type="date" name="tglKembali" id="tglKembali"
                           value="<?= $data['TanggalKembali'] ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           required>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" name="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                    <a href="list.php" class="bg-gray-500 hover:bg-gray-400 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out'
        });
    </script>
</body>
</html>
