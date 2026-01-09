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

$portfolio_count    = getCount($conn, 'portfolio');
$menu_count         = getCount($conn, 'menu');
$team_count         = getCount($conn, 'team');
$organization_count = getCount($conn, 'organization_structure');
$clients_count      = getCount($conn, 'clients');
$news_count         = getCount($conn, 'news');

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
            padding: 0 20px 25px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            color: #94a3b8;
            padding: 10px 20px;
            font-weight: 500;
            font-size: 0.875rem;
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
            padding: 30px 40px;
        }

        .page-header {
            margin-bottom: 20px;
        }

        /* Stat Card Styling */
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s;
            height: 100%;
        }

        .stat-card:hover { transform: translateY(-3px); }

        .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        /* Warna Ikon */
        .bg-soft-blue { background: #eff6ff; color: #2563eb; }
        .bg-soft-green { background: #f0fdf4; color: #16a34a; }
        .bg-soft-purple { background: #faf5ff; color: #9333ea; }
        .bg-soft-orange { background: #fff7ed; color: #ea580c; }
        .bg-soft-cyan { background: #ecfeff; color: #0891b2; }
        .bg-soft-red { background: #fee2e2; color: #dc2626; }

        .stat-value { font-size: 1.5rem; font-weight: 700; margin-bottom: 3px; }
        .stat-label { color: #64748b; font-weight: 500; font-size: 0.8rem; }

        .welcome-banner {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border-left: 5px solid var(--accent-color);
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .welcome-banner h5 {
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .welcome-banner p {
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .sidebar { position: relative; min-height: auto; width: 100%; }
            .main-content { margin-left: 0; padding: 20px; }
            
            .stat-card {
                padding: 12px;
            }
            
            .stat-value { font-size: 1.25rem; }
            .stat-label { font-size: 0.75rem; }
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
            </ul>
        </nav>

        <main class="col-md-10 ms-sm-auto main-content">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">Dashboard Overview</h1>
                    <p class="text-muted">Selamat datang di halaman dashboard, <strong><?php echo $_SESSION['admin_name']; ?></strong></p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a href="../index.php" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-globe"></i> Lihat Website
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-blue"><i class="bi bi-images"></i></div>
                        <div class="stat-value"><?php echo $portfolio_count; ?></div>
                        <div class="stat-label">Portfolio</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-green"><i class="bi bi-gear"></i></div>
                        <div class="stat-value"><?php echo $menu_count; ?></div>
                        <div class="stat-label">Layanan</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-purple"><i class="bi bi-people"></i></div>
                        <div class="stat-value"><?php echo $team_count; ?></div>
                        <div class="stat-label">Team</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-red"><i class="bi bi-diagram-3"></i></div>
                        <div class="stat-value"><?php echo $organization_count; ?></div>
                        <div class="stat-label">Organization</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card">
                        <div class="icon-box bg-soft-cyan"><i class="bi bi-building"></i></div>
                        <div class="stat-value"><?php echo $clients_count; ?></div>
                        <div class="stat-label">Clients</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
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