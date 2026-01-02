<?php
session_start();
include 'config.php';
check_login();

// Count data from database
$portfolio_count = 0;
$services_count = 0;
$team_count = 0;
$news_count = 0;

$result = $conn->query("SELECT COUNT(*) as count FROM portfolio");
if ($result) {
    $row = $result->fetch_assoc();
    $portfolio_count = $row['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM services");
if ($result) {
    $row = $result->fetch_assoc();
    $services_count = $row['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM team");
if ($result) {
    $row = $result->fetch_assoc();
    $team_count = $row['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM news");
if ($result) {
    $row = $result->fetch_assoc();
    $news_count = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Citra Arsitama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <link href="../assets/img/company-logo.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-dark sidebar">
                <div class="sidebar-header">
                    <h5 class="text-white mb-4 mt-3">Admin</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">
                            <i class="bi bi-images"></i> Portfolio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">
                            <i class="bi bi-gear"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="team.php">
                            <i class="bi bi-people"></i> Team
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clients.php">
                            <i class="bi bi-building"></i> Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="news.php">
                            <i class="bi bi-newspaper"></i> News
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="bi bi-sliders"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item border-top mt-3 pt-3">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="text-muted">Selamat datang, <?php echo $_SESSION['admin_name']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Portfolio</h6>
                                        <h3 class="mb-0"><?php echo $portfolio_count; ?></h3>
                                    </div>
                                    <i class="bi bi-images fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Services</h6>
                                        <h3 class="mb-0"><?php echo $services_count; ?></h3>
                                    </div>
                                    <i class="bi bi-gear fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Team</h6>
                                        <h3 class="mb-0"><?php echo $team_count; ?></h3>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">News</h6>
                                        <h3 class="mb-0"><?php echo $news_count; ?></h3>
                                    </div>
                                    <i class="bi bi-newspaper fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Welcome to Admin Panel</h5>
                                <p class="card-text">Gunakan sidebar di sebelah kiri untuk mengelola konten website Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
?>
