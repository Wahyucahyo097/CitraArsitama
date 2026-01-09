<?php
session_start();
include 'config.php';

// Fungsi sanitize sederhana jika belum ada di config.php
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Menggunakan Prepared Statement lebih aman, tapi ini mengikuti logika awal Anda
    $sql = "SELECT * FROM admin_users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Email tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Citra Arsitama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/img/company-logo.png" rel="icon">
    <style>
        :root {
            --primary-color: #2563eb;
            --dark-blue: #1e3a8a;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            height: 100vh;
        }

        /* Sisi Kiri: Visual/Gambar */
        .login-sidebar {
            flex: 1;
            background: linear-gradient(rgba(30, 58, 138, 0.7), rgba(30, 58, 138, 0.7)), 
                        url('../assets/img/stadion2.jpg'); /* Gambar gedung arsitektur */
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
        }

        .login-sidebar h1 {
            font-weight: 700;
            font-size: 3rem;
            margin-bottom: 20px;
        }

        /* Sisi Kanan: Form */
        .login-form-section {
            width: 450px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            box-shadow: -10px 0 25px rgba(0,0,0,0.05);
        }

        .brand-logo {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #64748b;
            margin-bottom: 30px;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
        }

        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 5px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            border-color: var(--primary-color);
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: var(--dark-blue);
            transform: translateY(-1px);
        }

        .alert-custom {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fee2e2;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .login-sidebar { display: none; }
            .login-form-section { width: 100%; }
        }

        /* Sisi Kanan: Form */
        .login-form-section {
            width: 450px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Mengetengahkan secara vertikal */
            align-items: center;     /* Mengetengahkan secara horizontal */
            padding: 40px;
            box-shadow: -10px 0 25px rgba(0,0,0,0.05);
            position: relative;      /* Untuk posisi footer nantinya */
        }

        /* Container tambahan untuk membungkus isi form agar lebarnya tetap konsisten */
        .form-wrapper {
            width: 100%;
            max-width: 320px; /* Membatasi lebar form agar tidak terlalu melebar ke samping */
        }

        .brand-logo {
            margin-bottom: 24px;
            text-align: center;
        }

        .login-header {
            text-align: center; /* Membuat teks judul & subtitle ke tengah */
        }

        .footer-text {
            position: absolute;
            bottom: 20px;
            font-size: 0.75rem;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-sidebar">
        <h1>Citra Arsitama</h1>
        <p class="lead">Membangun masa depan dengan desain arsitektur yang inovatif dan presisi.</p>
    </div>

 <div class="login-form-section">
        
        <div class="form-wrapper">
            <div class="brand-logo">
                <img src="../assets/img/company-logo.png" alt="Logo" style="height: 50px;" onerror="this.style.display='none'">
            </div>

            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk untuk mengelola dashboard.</p>
            </div>

        <?php if ($error): ?>
            <div class="alert-custom">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email Admin</label>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control" placeholder="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">Masuk</button>
        </form>

        <div style="margin-top: auto; padding-top: 20px; font-size: 0.75rem; color: #94a3b8; text-align: center;">
            &copy; <?php echo date('Y'); ?> Citra Arsitama. All rights reserved.
        </div>
    </div>
</div>

</body>
</html>