<?php
session_start();
include 'config.php';
check_login();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM news WHERE id = $id");
    header('Location: news.php?msg=deleted');
    exit();
}

// Handle Add/Edit with image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);

    // Handle uploaded image (optional)
    $uploadedImage = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $uploadsDir = __DIR__ . '/../assets/img/news/';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                $baseName = preg_replace('/[^a-z0-9._-]/i', '_', pathinfo($file['name'], PATHINFO_FILENAME));
                $newName = $baseName . '_' . time() . '.' . $ext;
                $target = $uploadsDir . $newName;
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $uploadedImage = $conn->real_escape_string($newName);
                }
            }
        }
    } else {
        // No file uploaded — for edit keep the current filename provided in hidden input
        if ($action === 'edit' && isset($_POST['current_image'])) {
            $uploadedImage = sanitize($_POST['current_image']);
        }
    }

    if ($action === 'add') {
        $imageSql = $uploadedImage ? "'" . $uploadedImage . "'" : 'NULL';
        $conn->query("INSERT INTO news (title, description, image) VALUES ('$title', '$description', $imageSql)");
        $message = 'News berhasil ditambahkan!';
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $imageSql = $uploadedImage ? ", image='" . $uploadedImage . "'" : '';
        $conn->query("UPDATE news SET title='$title', description='$description'" . $imageSql . " WHERE id=$id");
        $message = 'News berhasil diubah!';
    }
    
    header('Location: news.php?msg=' . urlencode($message));
    exit();
}

$edit_data = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM news WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

$news_list = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News - Citra Arsitama</title>
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
            margin-bottom: 30px;
        }

        /* Stat Card Styling */
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
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
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        /* Card Container */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        /* Table Styling untuk News */
        .table {
            font-size: 0.875rem;
        }

        .table th {
            background-color: #f3f4f6;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
            color: #374151;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-color: #e5e7eb;
        }

        .table-hover tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Column width management */
        .table-news {
            table-layout: fixed;
            width: 100%;
        }

        .table-news th:nth-child(1),
        .table-news td:nth-child(1) {
            width: 5%;
        }

        .table-news th:nth-child(2),
        .table-news td:nth-child(2) {
            width: 20%;
        }

        .table-news th:nth-child(3),
        .table-news td:nth-child(3) {
            width: 15%;
        }

        .table-news th:nth-child(4),
        .table-news td:nth-child(4) {
            width: 50%;
        }

        .table-news th:nth-child(5),
        .table-news td:nth-child(5) {
            width: 10%;
            text-align: center;
        }

        /* Text truncation untuk column Description */
        .desc-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-wrap: break-word;
        }

        /* Image cell styling */
        .image-cell {
            max-width: 150px;
            word-break: break-all;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .sidebar { position: relative; min-height: auto; }
            .main-content { margin-left: 0; }
            
            .table-news {
                table-layout: auto;
            }
            
            .table-news th:nth-child(3),
            .table-news td:nth-child(3) {
                display: none;
            }
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
                        <a class="nav-link active" href="news.php"><i class="bi bi-newspaper"></i> News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php"><i class="bi bi-gear-wide-connected"></i> Settings</a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold mb-1">News Management</h1>
                        <p class="text-muted">Kelola berita dan artikel perusahaan</p>
                    </div>
                    <?php if ($action !== 'add' && $action !== 'edit'): ?>
                        <a href="news.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah News
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
                    <div class="card">
                        <div class="card-body">
                            <div style="overflow-x: auto;">
                                <table class="table table-hover table-news">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>Description</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $news_list->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                                <td><span class="image-cell"><?php echo htmlspecialchars($row['image']); ?></span></td>
                                                <td><span class="desc-truncate"><?php echo htmlspecialchars($row['description']); ?></span></td>
                                                <td style="text-align: center;">
                                                    <a href="news.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="news.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin dihapus?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($action === 'add' || $action === 'edit'): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><?php echo $action === 'add' ? 'Tambah News' : 'Edit News'; ?></h5>
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Image (upload file)</label>
                                    <input type="file" name="image" class="form-control">
                                    <?php if ($edit_data && !empty($edit_data['image'])): ?>
                                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($edit_data['image']); ?>">
                                        <div class="mt-2">
                                            <img src="../assets/img/news/<?php echo htmlspecialchars($edit_data['image']); ?>" alt="current image" style="max-width:160px; height:auto; border:1px solid #ddd; padding:4px;">
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted d-block">Allowed types: jpg, jpeg, png, gif, webp. File will be saved to: <code>assets/img/news/</code></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required><?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> <?php echo $action === 'add' ? 'Tambah' : 'Update'; ?>
                                    </button>
                                    <a href="news.php" class="btn btn-secondary">
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
