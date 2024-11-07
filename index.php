<?php
include 'config.php';
session_start();

// Cek apakah user sudah login
if(!isset($_SESSION['logged_in'])) {
    header("Location: auth/login.php");
    exit();
}

// Query untuk menghitung total buku
$bukuQuery = "SELECT COUNT(*) as total FROM buku";
$bukuResult = mysqli_query($conn, $bukuQuery);
$totalBuku = mysqli_fetch_assoc($bukuResult)['total'];

// Query untuk menghitung anggota aktif
$anggotaQuery = "SELECT COUNT(*) as total FROM anggota";
$anggotaResult = mysqli_query($conn, $anggotaQuery); 
$totalAnggota = mysqli_fetch_assoc($anggotaResult)['total'];

// Query untuk menghitung total peminjaman
$peminjamanQuery = "SELECT COUNT(*) as total FROM peminjaman";
$peminjamanResult = mysqli_query($conn, $peminjamanQuery);
$totalPeminjaman = mysqli_fetch_assoc($peminjamanResult)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Sekolah - Pusat Ilmu Pengetahuan</title>
    
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
        
        .search-input:focus {
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        
        .category-card {
            transition: all 0.3s ease;
            background-color: white;
        }
        
        .category-card:hover {
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
                <a href="#" class="nav-link"><i class="fas fa-home mr-2"></i>Beranda</a>
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

    <!-- Hero Section -->
    <div class="container mx-auto py-16 px-4">
        <div class="text-center max-w-4xl mx-auto" data-aos="fade-up">
            <i class="fas fa-book-reader text-6xl text-gray-800 mb-8"></i>
            <h2 class="text-4xl font-bold mb-6 text-gray-800">Selamat Datang di Smart Library</h2>
            <p class="text-gray-600 text-lg mb-12">Temukan ribuan koleksi buku untuk menunjang pembelajaran Anda. Mari bersama-sama membangun masa depan melalui membaca!</p>
            
            <div class="flex justify-center space-x-4 mb-12">
                <div class="bg-white p-4 rounded-lg shadow" data-aos="fade-right" data-aos-delay="100">
                    <div class="text-3xl font-bold text-gray-800"><?= $totalBuku ?>+</div>
                    <div class="text-gray-600">Koleksi Buku</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-3xl font-bold text-gray-800"><?= $totalAnggota ?>+</div>
                    <div class="text-gray-600">Anggota Aktif</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-3xl font-bold text-gray-800"><?= $totalPeminjaman ?>+</div>
                    <div class="text-gray-600">Total Peminjaman</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow" data-aos="fade-left" data-aos-delay="400">
                    <div class="text-3xl font-bold text-gray-800">24/7</div>
                    <div class="text-gray-600">Akses Digital</div>
                </div>
            </div>

            <div class="flex justify-center space-x-4" data-aos="fade-up" data-aos-delay="500">
                <a href="katalog.php" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-700">
                    <i class="fas fa-book-open mr-2"></i>Jelajahi Katalog
                </a>
                <a href="anggota/add.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- Kategori -->
    <div class="container mx-auto py-16 px-4">
        <h3 class="text-3xl font-bold text-gray-800 mb-8 text-center" data-aos="fade-up">Jelajahi Kategori Buku</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="category-card p-8 rounded-lg shadow" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <i class="fas fa-book-open text-4xl text-gray-800 mb-6"></i>
                    <h4 class="text-xl font-bold mb-3 text-gray-800">Pelajaran</h4>
                    <p class="text-gray-600">Koleksi lengkap buku mata pelajaran untuk semua tingkatan</p>
                </div>
            </div>
            <div class="category-card p-8 rounded-lg shadow" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <i class="fas fa-flask text-4xl text-gray-800 mb-6"></i>
                    <h4 class="text-xl font-bold mb-3 text-gray-800">Sains</h4>
                    <p class="text-gray-600">Eksplorasi dunia sains dan teknologi modern</p>
                </div>
            </div>
            <div class="category-card p-8 rounded-lg shadow" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <i class="fas fa-history text-4xl text-gray-800 mb-6"></i>
                    <h4 class="text-xl font-bold mb-3 text-gray-800">Sejarah</h4>
                    <p class="text-gray-600">Pelajari sejarah dan warisan budaya dunia</p>
                </div>
            </div>
            <div class="category-card p-8 rounded-lg shadow" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <i class="fas fa-book text-4xl text-gray-800 mb-6"></i>
                    <h4 class="text-xl font-bold mb-3 text-gray-800">Fiksi</h4>
                    <p class="text-gray-600">Jelajahi dunia imajinasi melalui novel dan cerita</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div data-aos="fade-right">
                    <h4 class="text-2xl font-bold mb-6">Tentang Kami</h4>
                    <p class="text-gray-300 leading-relaxed">Perpustakaan Sekolah hadir sebagai pusat pembelajaran dan pengembangan diri siswa melalui koleksi buku yang lengkap dan berkualitas.</p>
                </div>
                <div data-aos="fade-up">
                    <h4 class="text-2xl font-bold mb-6">Kontak</h4>
                    <div class="space-y-4">
                        <p class="flex items-center"><i class="fas fa-phone mr-3"></i> +62 123 4567 890</p>
                        <p class="flex items-center"><i class="fas fa-envelope mr-3"></i> perpustakaan@sekolah.ac.id</p>
                        <p class="flex items-center"><i class="fas fa-map-marker-alt mr-3"></i> Jl. Pendidikan No. 123</p>
                    </div>
                </div>
                <div data-aos="fade-left">
                    <h4 class="text-2xl font-bold mb-6">Jam Operasional</h4>
                    <div class="space-y-4">
                        <p class="flex items-center"><i class="fas fa-clock mr-3"></i> Senin - Jumat: 08:00 - 16:00</p>
                        <p class="flex items-center"><i class="fas fa-clock mr-3"></i> Sabtu: 08:00 - 12:00</p>
                        <p class="flex items-center"><i class="fas fa-times-circle mr-3"></i> Minggu: Tutup</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12 pt-8 border-t border-gray-700">
                <p class="text-gray-400">&copy; 2023 Perpustakaan Sekolah. Hak Cipta Dilindungi.</p>
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
