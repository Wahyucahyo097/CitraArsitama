<?php
session_start();
include 'config.php';
check_login();

// Fungsi helper untuk menghitung data agar kode lebih rapi
function getCount($conn, $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    return 0;
}

$portfolio_count = getCount($conn, 'portfolio');
$menu_count      = getCount($conn, 'menu');
$team_count      = getCount($conn, 'team');
$news_count      = getCount($conn, 'news');
$clients_count   = getCount($conn, 'clients'); // Menambahkan count untuk clients
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Citra Arsitama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/img/company-logo.png" rel="icon">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --main-bg: #f8fafc;
            --accent-color: #2563eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--main-bg);
            color: #1e293b;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            padding-top: 20px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            position: fixed;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 0 25px 30px;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            color: #94a3b8;
            padding: 12px 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }

        .nav-link i { font-size: 1.1rem; }

        /* Main Content */
        .main-content {
            margin-left: 16.666667%; /* Menyesuaikan col-md-2 */
            padding: 40px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        /* Stat Card Styling */
        .stat-card {
            background: white;
            border: none;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            height: 100%;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        /* Warna Ikon */
        .bg-soft-blue { background: #eff6ff; color: #2563eb; }
        .bg-soft-green { background: #f0fdf4; color: #16a34a; }
        .bg-soft-purple { background: #faf5ff; color: #9333ea; }
        .bg-soft-orange { background: #fff7ed; color: #ea580c; }
        .bg-soft-cyan { background: #ecfeff; color: #0891b2; }

        .stat-value { font-size: 1.75rem; font-weight: 700; margin-bottom: 5px; }
        .stat-label { color: #64748b; font-weight: 500; font-size: 0.875rem; }

        .welcome-banner {
            background: white;
            border-radius: 16px;
            padding: 30px;
            border-left: 5px solid var(--accent-color);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .sidebar { position: relative; min-height: auto; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-md-block sidebar">
            <div class="sidebar-brand">
                <i class="bi bi-layers-half"></i>
                <span>Citra Arsitama</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php"><i class="bi bi-house-door"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="portfolio.php"><i class="bi bi-collection"></i> Portfolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="services.php"><i class="bi bi-briefcase"></i> Menu Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="team.php"><i class="bi bi-people"></i> Team</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="organization.php"><i class="bi bi-diagram-3"></i> Organization</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clients.php"><i class="bi bi-building"></i> Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="news.php"><i class="bi bi-newspaper"></i> News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php"><i class="bi bi-gear-wide-connected"></i> Settings</a>
                </li>
                <li class="nav-item mt-4 pt-4 border-top">
                    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a>
                </li>
            </ul>
        </nav>

        <main class="col-md-10 ms-sm-auto main-content">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">Dashboard Overview</h1>
                    <p class="text-muted">Selamat datang kembali, <strong><?php echo $_SESSION['admin_name']; ?></strong></p>
                </div>
                <img src="../assets/img/company-logo.png" alt="Logo" height="45" onerror="this.style.display='none'">
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-2 col-sm-4">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-blue"><i class="bi bi-images"></i></div>
                        <div class="stat-value"><?php echo $portfolio_count; ?></div>
                        <div class="stat-label">Portfolio</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-green"><i class="bi bi-gear"></i></div>
                        <div class="stat-value"><?php echo $menu_count; ?></div>
                        <div class="stat-label">Layanan</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-purple"><i class="bi bi-people"></i></div>
                        <div class="stat-value"><?php echo $team_count; ?></div>
                        <div class="stat-label">Team</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-cyan"><i class="bi bi-building"></i></div>
                        <div class="stat-value"><?php echo $clients_count; ?></div>
                        <div class="stat-label">Clients</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-orange"><i class="bi bi-newspaper"></i></div>
                        <div class="stat-value"><?php echo $news_count; ?></div>
                        <div class="stat-label">News</div>
                    </div>
                </div>
            </div>

            <div class="welcome-banner">
                <h5 class="fw-bold mb-2">Pusat Kontrol Citra Arsitama</h5>
                <p class="text-muted mb-0">
                    Gunakan bilah navigasi di sebelah kiri untuk mengelola konten website Anda. 
                    Anda dapat memperbarui portfolio proyek terbaru, mengelola daftar klien, hingga mempublikasikan berita arsitektur terkini.
                </p>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>