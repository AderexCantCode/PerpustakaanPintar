<?php
include 'config.php';
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
    <title>Katalog Buku - Perpustakaan</title>
    
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
        
        .book-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
            z-index: 9999;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            position: relative;
            z-index: 10000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .kategori-btn {
            transition: all 0.3s ease;
        }

        .kategori-btn:hover {
            transform: translateY(-2px);
        }

        .kategori-btn.active {
            background-color: #1f2937;
            color: white;
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

    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Katalog Buku Perpustakaan</h1>
            <p class="text-gray-600">Temukan berbagai koleksi buku menarik di perpustakaan kami</p>
        </div>

        <!-- Filter Kategori -->
        <div class="mb-8 flex flex-wrap justify-center gap-2" data-aos="fade-up">
            <a href="katalog.php" class="kategori-btn px-4 py-2 rounded-full bg-gray-200 hover:bg-gray-300 <?php echo !isset($_GET['kategori']) ? 'active' : ''; ?>">
                Semua
            </a>
            <?php while($kategori = mysqli_fetch_assoc($kategori_result)) { ?>
                <a href="?kategori=<?= urlencode($kategori['Kategori']) ?>" 
                   class="kategori-btn px-4 py-2 rounded-full bg-gray-200 hover:bg-gray-300 <?php echo isset($_GET['kategori']) && $_GET['kategori'] == $kategori['Kategori'] ? 'active' : ''; ?>">
                    <?= $kategori['Kategori'] ?>
                </a>
            <?php } ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="book-card bg-white rounded-lg shadow-lg overflow-hidden" data-aos="fade-up">
                    <div class="p-6">
                        <div class="text-4xl text-gray-400 mb-4">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= $row['JudulBuku'] ?></h3>
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-user mr-2"></i><?= $row['Pengarang'] ?>
                        </p>
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-building mr-2"></i><?= $row['Penerbit'] ?>
                        </p>
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-calendar mr-2"></i><?= $row['TahunTerbit'] ?>
                        </p>
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-tag mr-2"></i><?= $row['Kategori'] ?>
                        </p>
                        <p class="text-gray-600">
                            <i class="fas fa-layer-group mr-2"></i>Stok: <?= $row['JumlahStok'] ?>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

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
