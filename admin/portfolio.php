<?php
session_start();
include 'config.php';
check_login();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM portfolio WHERE id = $id");
    header('Location: portfolio.php?msg=deleted');
    exit();
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $link = sanitize($_POST['link']);
    $client = sanitize($_POST['client']);
    $project_date = sanitize($_POST['project_date']);

    if ($action === 'add') {
        $thumbnail = '';
        if (!empty($_FILES['thumbnail']['name'])) {
            $target_dir = "../assets/img/portfolio/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            $thumbnail = basename($_FILES['thumbnail']['name']);
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_dir . $thumbnail);
        }
        
        $details_images = [];
        if (!empty($_FILES['details_images']['name'][0])) {
            $target_dir = "../assets/img/portfolio/details/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            foreach ($_FILES['details_images']['name'] as $key => $name) {
                if (!empty($name)) {
                    $filename = basename($name);
                    move_uploaded_file($_FILES['details_images']['tmp_name'][$key], $target_dir . $filename);
                    $details_images[] = $filename;
                }
            }
        }
        $details_images_json = json_encode($details_images);
        
        $conn->query("INSERT INTO portfolio (title, description, category, thumbnail, details_images, link, client, project_date) VALUES ('$title', '$description', '$category', '$thumbnail', '$details_images_json', '$link', '$client', '$project_date')");
        $message = 'Portfolio berhasil ditambahkan!';
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $thumbnail = sanitize($_POST['old_thumbnail']);
        
        if (!empty($_FILES['thumbnail']['name'])) {
            $target_dir = "../assets/img/portfolio/";
            $thumbnail = basename($_FILES['thumbnail']['name']);
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_dir . $thumbnail);
        }
        
        $details_images = json_decode($_POST['old_details_images'], true) ?: [];
        if (!empty($_FILES['details_images']['name'][0])) {
            $target_dir = "../assets/img/portfolio/details/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            foreach ($_FILES['details_images']['name'] as $key => $name) {
                if (!empty($name)) {
                    $filename = basename($name);
                    move_uploaded_file($_FILES['details_images']['tmp_name'][$key], $target_dir . $filename);
                    $details_images[] = $filename;
                }
            }
        }
        $details_images_json = json_encode($details_images);
        
        $conn->query("UPDATE portfolio SET title='$title', description='$description', category='$category', thumbnail='$thumbnail', details_images='$details_images_json', link='$link', client='$client', project_date='$project_date' WHERE id=$id");
        $message = 'Portfolio berhasil diubah!';
    }
    
    header('Location: portfolio.php?msg=' . urlencode($message));
    exit();
}

// Get data for edit
$edit_data = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM portfolio WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

// Get all portfolio items
$portfolio_list = $conn->query("SELECT * FROM portfolio ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Citra Arsitama</title>
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
            <!-- Sidebar -->
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
                        <a class="nav-link active" href="portfolio.php"><i class="bi bi-collection"></i> Portfolio</a>
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

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Portfolio Management</h1>
                        <p class="text-muted">Kelola proyek dan portfolio perusahaan</p>
                    </div>
                    <?php if ($action !== 'add' && $action !== 'edit'): ?>
                        <a href="portfolio.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Portfolio
                        </a>
                    <?php else: ?>
                        <img src="../assets/img/company-logo.png" alt="Logo" height="45" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo htmlspecialchars($_GET['msg']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                    <!-- Portfolio List -->
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Tahun</th>
                                        <th>Thumbnail</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $portfolio_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                                            <td><?php echo $row['thumbnail'] ? '<img src="../assets/img/portfolio/' . htmlspecialchars($row['thumbnail']) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">' : '-'; ?></td>
                                            <td>
                                                <a href="portfolio.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="portfolio.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin dihapus?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($action === 'add' || $action === 'edit'): ?>
                    <!-- Form Add/Edit -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><?php echo $action === 'add' ? 'Tambah Portfolio' : 'Edit Portfolio'; ?></h5>
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                    <input type="hidden" name="old_thumbnail" value="<?php echo htmlspecialchars($edit_data['thumbnail']); ?>">
                                    <input type="hidden" name="old_details_images" value="<?php echo htmlspecialchars($edit_data['details_images']); ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required><?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tahun</label>
                                        <input type="number" name="category" class="form-control" min="2000" max="2030" value="<?php echo $edit_data ? htmlspecialchars($edit_data['category']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Link</label>
                                        <input type="url" name="link" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['link']) : ''; ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Client</label>
                                        <input type="text" name="client" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['client']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Project Date</label>
                                        <input type="date" name="project_date" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['project_date']) : ''; ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Thumbnail Image</label>
                                    <input type="file" name="thumbnail" class="form-control" accept="image/*" <?php echo $action === 'add' ? 'required' : ''; ?>>
                                    <?php if ($edit_data && $edit_data['thumbnail']): ?>
                                        <small class="text-muted">Current thumbnail: <?php echo htmlspecialchars($edit_data['thumbnail']); ?></small>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Details Images (Multiple)</label>
                                    <input type="file" name="details_images[]" class="form-control" accept="image/*" multiple>
                                    <?php if ($edit_data && $edit_data['details_images']): ?>
                                        <small class="text-muted">Current details images: <?php echo htmlspecialchars($edit_data['details_images']); ?></small>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> <?php echo $action === 'add' ? 'Tambah' : 'Update'; ?>
                                    </button>
                                    <a href="portfolio.php" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Batal
                                    </a>
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
