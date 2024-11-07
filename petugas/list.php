<?php
include '../config.php';
session_start();

if(!isset($_SESSION['logged_in'])) {
    header("Location: auth/login.php");
    exit();
}

// Query untuk mengambil data petugas
$sql = "SELECT * FROM petugas ORDER BY PetugasID DESC";
$result = mysqli_query($conn, $sql);

// Cek jika query error
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Petugas - Perpustakaan</title>
    
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
                        <i class="fas fa-users mr-2"></i>Daftar Petugas
                    </h2>
                    <p class="text-gray-600 mt-1">Kelola data petugas perpustakaan</p>
                </div>
                <a href="add.php" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>Tambah Petugas
                </a>
            </div>

            <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="overflow-x-auto bg-white rounded-lg">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">No</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nama Petugas</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Jabatan</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Kontak</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr class="table-row-hover border-b" data-aos="fade-up" data-aos-delay="<?= $no * 50 ?>">
                            <td class="px-6 py-4 text-sm text-gray-700"><?= $no++ ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($row['NamaPetugas']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="px-3 py-1 rounded-full text-xs <?= $row['Jabatan'] == 'Administrator' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= htmlspecialchars($row['Jabatan']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($row['Kontak']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="edit.php?id=<?= $row['PetugasID'] ?>" class="action-button inline-block text-blue-500 hover:text-blue-700 mx-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="action-button inline-block text-red-500 hover:text-red-700 mx-1" 
                                   onclick="confirmDelete(<?= $row['PetugasID'] ?>)" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-8" data-aos="fade-up">
                <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500">Belum ada data petugas</p>
                <a href="add.php" class="inline-block mt-4 text-blue-500 hover:text-blue-700">
                    Tambah Petugas Baru
                </a>
            </div>
            <?php endif; ?>

            <div class="mt-6" data-aos="fade-up">
                <a href="../masterdata.php" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Master Data
                </a>
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
                text: "Data petugas akan dihapus permanen!",
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
