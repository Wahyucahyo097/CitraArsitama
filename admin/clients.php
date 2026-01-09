<?php
session_start();
include 'config.php';
check_login();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM clients WHERE id = $id");
    header('Location: clients.php?msg=deleted');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    
    if ($action === 'add') {
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../assets/img/clients/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
        }
        $conn->query("INSERT INTO clients (name, image) VALUES ('$name', '$image')");
        $message = 'Client berhasil ditambahkan!';
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $image = sanitize($_POST['old_image']);
        
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../assets/img/clients/";
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
        }
        
        $conn->query("UPDATE clients SET name='$name', image='$image' WHERE id=$id");
        $message = 'Client berhasil diubah!';
    }
    
    header('Location: clients.php?msg=' . urlencode($message));
    exit();
}

$edit_data = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM clients WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

$clients_list = $conn->query("SELECT * FROM clients ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - Citra Arsitama</title>
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
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Dashboard</a>
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
                        <a class="nav-link active" href="clients.php"><i class="bi bi-building"></i> Clients</a>
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
                        <h1 class="h3 fw-bold mb-1">Clients Management</h1>
                        <p class="text-muted">Kelola daftar klien perusahaan</p>
                    </div>
                    <?php if ($action !== 'add' && $action !== 'edit'): ?>
                        <a href="clients.php?action=add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Client</a>
                    <?php else: ?>
                        <img src="../assets/img/company-logo.png" alt="Logo" height="45" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($_GET['msg']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <?php while ($row = $clients_list->fetch_assoc()): ?>
                                    <div class="col-md-3 mb-4">
                                        <div class="card h-100">
                                            <img src="../assets/img/clients/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" style="height: 200px; object-fit: contain; padding: 20px;">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                                <div class="btn-group w-100">
                                                    <a href="clients.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                                    <a href="clients.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin dihapus?')"><i class="bi bi-trash"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($action === 'add' || $action === 'edit'): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><?php echo $action === 'add' ? 'Tambah Client' : 'Edit Client'; ?></h5>
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                    <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($edit_data['image']); ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Client Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['name']) : ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Logo/Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" <?php echo $action === 'add' ? 'required' : ''; ?>>
                                    <?php if ($edit_data && $edit_data['image']): ?>
                                        <small class="text-muted">Current: <img src="../assets/img/clients/<?php echo htmlspecialchars($edit_data['image']); ?>" style="max-width: 100px;"></small>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> <?php echo $action === 'add' ? 'Tambah' : 'Update'; ?></button>
                                    <a href="clients.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
?>
