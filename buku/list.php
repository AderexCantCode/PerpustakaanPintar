<?php
include '../config.php';
session_start();

if(!isset($_SESSION['logged_in'])) {
    header("Location: auth/login.php");
    exit();
}

// Query untuk mengambil data buku
$sql = "SELECT * FROM buku ORDER BY BukuID DESC";

// Filter berdasarkan kategori jika ada
if(isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
    $sql = "SELECT * FROM buku WHERE Kategori = '$kategori' ORDER BY BukuID DESC";
}

$result = mysqli_query($conn, $sql);

// Cek jika query error
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

// Query untuk mendapatkan daftar kategori unik
$kategori_sql = "SELECT DISTINCT Kategori FROM buku ORDER BY Kategori ASC";
$kategori_result = mysqli_query($conn, $kategori_sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - Perpustakaan</title>
    
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }
        
        .table-row-hover {
            transition: all 0.3s ease;
        }
        
        .table-row-hover:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
        }
        
        .action-button {
            transition: all 0.3s ease;
        }
        
        .action-button:hover {
            transform: scale(1.2);
        }

        .table-container {
            width: 100%;
            margin: 0 auto;
            overflow: visible;
        }

        table {
            width: 100%;
            table-layout: fixed;
        }

        th, td {
            padding: 12px;
            text-align: left;
            word-wrap: break-word;
        }

        .nav-link {
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            position: relative;
        }
        
        .nav-link:hover {
            background-color: rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #1f2937;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 80%;
            left: 10%;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9999; /* Menambahkan z-index yang lebih tinggi */
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            position: relative;
            z-index: 10000; /* Menambahkan z-index yang lebih tinggi dari modal */
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); /* Menambahkan shadow */
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
            <div class="space-x-6 flex items-center">
                <a href="../index.php" class="nav-link"><i class="fas fa-home mr-2"></i>Beranda</a>
                <a href="../katalog.php" class="nav-link"><i class="fas fa-book mr-2"></i>Katalog</a>
                <a href="../masterdata.php" class="nav-link"><i class="fas fa-database mr-2"></i>Master Data</a>
                <a href="../peminjaman/list.php" class="nav-link"><i class="fas fa-clipboard-list mr-2"></i>Peminjaman</a>
                <button onclick="toggleModal()" class="nav-link"><i class="fas fa-user mr-2"></i><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></button>
            </div>
        </div>
    </nav>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Profil Pengguna</h3>
                <button onclick="toggleModal()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="text-center">
                <i class="fas fa-user-circle text-6xl text-gray-600 mb-4"></i>
                <p class="text-lg font-medium"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></p>
                <?php if(isset($_SESSION['logged_in'])): ?>
                    <a href="../auth/logout.php" class="block mt-4 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="../auth/login.php" class="block mt-4 bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6" data-aos="fade-up">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-book mr-2"></i>Daftar Buku
                    </h2>
                    <p class="text-gray-600 mt-1">Kelola data buku perpustakaan</p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Filter Kategori -->
                    <form method="GET" class="flex items-center">
                        <select name="kategori" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <?php while($kat = mysqli_fetch_assoc($kategori_result)): ?>
                                <option value="<?= $kat['Kategori'] ?>" <?= isset($_GET['kategori']) && $_GET['kategori'] == $kat['Kategori'] ? 'selected' : '' ?>>
                                    <?= $kat['Kategori'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                    <a href="add.php" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-300 transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Tambah Buku
                    </a>
                </div>
            </div>

            <div class="table-container">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 20%">Judul Buku</th>
                            <th style="width: 15%">Pengarang</th>
                            <th style="width: 15%">Penerbit</th>
                            <th style="width: 10%">Tahun Terbit</th>
                            <th style="width: 10%">Kategori</th>
                            <th style="width: 10%">Stok</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr class="table-row" data-aos="fade-up" data-aos-delay="<?= $no * 50 ?>">
                                <td><?= $no++ ?></td>
                                <td><?= $row['JudulBuku'] ?></td>
                                <td><?= $row['Pengarang'] ?></td>
                                <td><?= $row['Penerbit'] ?></td>
                                <td><?= $row['TahunTerbit'] ?></td>
                                <td><?= $row['Kategori'] ?></td>
                                <td><?= $row['JumlahStok'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $row['BukuID'] ?>" class="action-btn text-blue-600 hover:text-blue-900 mr-3 inline-block">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="action-btn text-red-600 hover:text-red-900 inline-block" onclick="confirmDelete(<?= $row['BukuID'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        AOS.init({
            duration: 800,
            once: true,
            mirror: false
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning', 
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete.php?id=' + id;
                }
            })
        }
        function toggleModal() {
            const modal = document.getElementById('userModal');
            if(modal.style.display === "block") {
                modal.style.display = "none";
            } else {
                modal.style.display = "block";
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>