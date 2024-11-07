<?php
include '../config.php';
session_start();

if(!isset($_SESSION['logged_in'])) {
    header("Location: auth/login.php");
    exit();
}

$query = "SELECT p.PeminjamanID, p.BukuID, p.AnggotaID, p.PetugasID, p.TanggalPinjam, p.TanggalKembali, 
          b.JudulBuku, a.NamaAnggota, pt.NamaPetugas,
          CASE 
            WHEN p.TanggalPengembalian IS NOT NULL THEN 'Dikembalikan'
            WHEN p.TanggalKembali < CURRENT_DATE() THEN 'Terlambat'
            ELSE 'Dipinjam'
          END as StatusPeminjaman
          FROM peminjaman p
          JOIN buku b ON p.BukuID = b.BukuID
          JOIN anggota a ON p.AnggotaID = a.AnggotaID 
          JOIN petugas pt ON p.PetugasID = pt.PetugasID
          ORDER BY p.TanggalPinjam DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peminjaman - Perpustakaan</title>
    
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
        <div class="flex justify-between items-center mb-6" data-aos="fade-down">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clipboard-list mr-2"></i>Daftar Peminjaman
            </h2>
            <a href="add.php" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">
                <i class="fas fa-plus mr-2"></i>Tambah Peminjaman
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-x-auto" data-aos="fade-up">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Judul Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tgl Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tgl Kembali</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap"><?= $row['JudulBuku'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $row['NamaAnggota'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $row['NamaPetugas'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($row['TanggalPinjam'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($row['TanggalKembali'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($row['StatusPeminjaman'] == 'Dikembalikan'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Dikembalikan
                                    </span>
                                <?php elseif($row['StatusPeminjaman'] == 'Terlambat'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Terlambat
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Dipinjam
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if($row['StatusPeminjaman'] != 'Dikembalikan'): ?>
                                    <a href="return.php?id=<?= $row['PeminjamanID'] ?>" class="text-green-600 hover:text-green-900 mr-3" 
                                       onclick="return confirm('Apakah buku ini sudah dikembalikan?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="edit.php?id=<?= $row['PeminjamanID'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?= $row['PeminjamanID'] ?>" class="text-red-600 hover:text-red-900" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out'
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
