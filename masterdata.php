<?php
include 'config.php';
session_start();

if(!isset($_SESSION['logged_in'])) {
    header("Location: auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data - Perpustakaan</title>
    
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

        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                <a href="index.php" class="nav-link"><i class="fas fa-home mr-2"></i>Beranda</a>
                <a href="katalog.php" class="nav-link"><i class="fas fa-book mr-2"></i>Katalog</a>
                <a href="masterdata.php" class="nav-link"><i class="fas fa-database mr-2"></i>Master Data</a>
                <a href="peminjaman/list.php" class="nav-link"><i class="fas fa-clipboard-list mr-2"></i>Peminjaman</a>
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
                    <a href="auth/logout.php" class="block mt-4 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="auth/login.php" class="block mt-4 bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

                    
    <!-- Header -->
    <div class="container mx-auto py-8 px-4">
        <div class="text-center" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Master Data Smart Library</h2>
            <p class="text-gray-600">Kelola semua data perpustakaan dalam satu tempat</p>
        </div>
    </div>

    <!-- Master Data Cards -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Buku -->
            <div class="card bg-white rounded-lg shadow p-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <i class="fas fa-book text-4xl text-gray-800 mb-4"></i>
                    <h3 class="text-xl font-bold mb-4">Data Buku</h3>
                    <p class="text-gray-600 mb-6">Kelola data buku perpustakaan</p>
                    <div class="flex justify-center space-x-2">
                        <a href="buku/add.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i>Tambah
                        </a>
                        <a href="buku/list.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
                            <i class="fas fa-list mr-2"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Anggota -->
            <div class="card bg-white rounded-lg shadow p-6" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <i class="fas fa-users text-4xl text-gray-800 mb-4"></i>
                    <h3 class="text-xl font-bold mb-4">Data Anggota</h3>
                    <p class="text-gray-600 mb-6">Kelola data anggota perpustakaan</p>
                    <div class="flex justify-center space-x-2">
                        <a href="anggota/add.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i>Tambah
                        </a>
                        <a href="anggota/list.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
                            <i class="fas fa-list mr-2"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Petugas -->
            <div class="card bg-white rounded-lg shadow p-6" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <i class="fas fa-user-tie text-4xl text-gray-800 mb-4"></i>
                    <h3 class="text-xl font-bold mb-4">Data Petugas</h3>
                    <p class="text-gray-600 mb-6">Kelola data petugas perpustakaan</p>
                    <div class="flex justify-center space-x-2">
                        <a href="petugas/add.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i>Tambah
                        </a>
                        <a href="petugas/list.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
                            <i class="fas fa-list mr-2"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; 2023 Perpustakaan. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });

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
