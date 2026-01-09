<?php
session_start();
include 'config.php';
check_login();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM menu WHERE id = $id");
    header('Location: services.php?msg=deleted');
    exit();
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $url = sanitize($_POST['url']);
    $parent_id = intval($_POST['parent_id']);
    $order_by = intval($_POST['order_by']);

    if ($action === 'add') {
        $conn->query("INSERT INTO menu (title, url, parent_id, order_by) VALUES ('$title', '$url', $parent_id, $order_by)");
        $message = 'Menu berhasil ditambahkan!';
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $conn->query("UPDATE menu SET title='$title', url='$url', parent_id=$parent_id, order_by=$order_by WHERE id=$id");
        $message = 'Menu berhasil diubah!';
    }
    
    header('Location: services.php?msg=' . urlencode($message));
    exit();
}

$edit_data = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM menu WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

// Get parent menus for dropdown
$parent_menus = $conn->query("SELECT id, title FROM menu WHERE parent_id = 0 ORDER BY order_by ASC");

$menu_list = $conn->query("SELECT m1.*, m2.title as parent_title FROM menu m1 LEFT JOIN menu m2 ON m1.parent_id = m2.id ORDER BY m1.parent_id ASC, m1.order_by ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Layanan - Citra Arsitama</title>
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

        /* Table Styling */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .table {
            table-layout: fixed;
            width: 100%;
            margin-bottom: 0;
        }

        .table th {
            background-color: #f3f4f6;
            color: #374151;
            border: none;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 0.875rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .table td {
            padding: 12px 15px;
            border-color: #e5e7eb;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .sidebar { position: relative; min-height: auto; }
            .main-content { margin-left: 0; }
            .table { table-layout: auto; }
            .table th, .table td { padding: 10px 8px; font-size: 0.75rem; }
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
                        <a class="nav-link active" href="services.php"><i class="bi bi-briefcase"></i> Menu Layanan</a>
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

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Menu Layanan Management</h1>
                        <p class="text-muted">Kelola menu layanan perusahaan</p>
                    </div>
                    <?php if ($action !== 'add' && $action !== 'edit'): ?>
                        <a href="services.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Menu
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
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Parent</th>
                                        <th>Order</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $menu_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['url']); ?></td>
                                            <td><?php echo $row['parent_title'] ? htmlspecialchars($row['parent_title']) : '-'; ?></td>
                                            <td><?php echo $row['order_by']; ?></td>
                                            <td>
                                                <a href="services.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="services.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin dihapus?')">
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
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><?php echo $action === 'add' ? 'Tambah Menu' : 'Edit Menu'; ?></h5>
                            <form method="POST">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">URL</label>
                                    <input type="text" name="url" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['url']) : ''; ?>" placeholder="Contoh: # atau /page">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Parent Menu</label>
                                    <select name="parent_id" class="form-control">
                                        <option value="0">Top Level</option>
                                        <?php while ($parent = $parent_menus->fetch_assoc()): ?>
                                            <option value="<?php echo $parent['id']; ?>" <?php echo ($edit_data && $edit_data['parent_id'] == $parent['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($parent['title']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="order_by" class="form-control" value="<?php echo $edit_data ? $edit_data['order_by'] : 0; ?>" min="0">
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> <?php echo $action === 'add' ? 'Tambah' : 'Update'; ?>
                                    </button>
                                    <a href="services.php" class="btn btn-secondary">
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
