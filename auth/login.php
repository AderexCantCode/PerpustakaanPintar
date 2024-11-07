<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Tidak perlu escape karena akan diverifikasi dengan password_verify

    // Ambil data user berdasarkan username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi kata sandi dengan password_verify
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true; // Tambahkan flag login
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Sekolah</title>
    
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }
        .login-form {
            transition: all 0.3s ease;
        }
        .login-form:hover {
            transform: translateY(-5px);
        }
        .input-field:focus {
            border-color: #4B5563;
            box-shadow: 0 0 0 3px rgba(75, 85, 99, 0.2);
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96 login-form" data-aos="fade-up">
            <div class="text-center mb-8">
                <i class="fas fa-book-reader text-5xl text-gray-800 mb-3"></i>
                <h1 class="text-3xl font-bold mt-2 text-gray-800">Smart Library</h1>
                <p class="text-gray-600 mt-2">Masuk ke akun Anda</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-medium"><?= $error ?></p>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="username">
                        Username
                    </label>
                    <input class="input-field shadow-sm appearance-none border rounded-lg w-full py-2.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-300" 
                           id="username" 
                           name="username"
                           type="text" 
                           required
                           placeholder="Masukkan username">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                        Password
                    </label>
                    <input class="input-field shadow-sm appearance-none border rounded-lg w-full py-2.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-300"
                           id="password"
                           name="password" 
                           type="password"
                           required
                           placeholder="Masukkan password">
                </div>

                <div>
                    <button class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2.5 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full transition duration-300" 
                            type="submit">
                        Masuk
                    </button>
                </div>

                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600">
                        Belum punya akun? 
                        <a href="signup.php" class="text-gray-800 hover:underline font-medium">Daftar di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>
