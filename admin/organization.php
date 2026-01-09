<?php
session_start();
include 'config.php';
check_login();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM organization_structure WHERE id = $id");
    header('Location: organization.php?msg=Anggota organisasi berhasil dihapus!');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $position = sanitize($_POST['position']);
    $level = (int)$_POST['level'];
    $order_in_level = (int)$_POST['order_in_level'];

    if ($action === 'add') {
        $conn->query("INSERT INTO organization_structure (name, position, level, order_in_level) VALUES ('$name', '$position', $level, $order_in_level)");
        $message = 'Anggota organisasi berhasil ditambahkan!';
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $conn->query("UPDATE organization_structure SET name='$name', position='$position', level=$level, order_in_level=$order_in_level WHERE id=$id");
        $message = 'Anggota organisasi berhasil diubah!';
    }
    
    header('Location: organization.php?msg=' . urlencode($message));
    exit();
}

$edit_data = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM organization_structure WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

$org_list = $conn->query("SELECT * FROM organization_structure ORDER BY level ASC, order_in_level ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Susunan Organisasi - Citra Arsitama</title>
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

        /* Form Styling */
        .form-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            background: var(--accent-color);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-cancel {
            background: #6b7280;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #4b5563;
        }

        /* Table Styling */
        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .table {
            margin: 0;
        }

        .table th {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            margin-right: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        /* Alert Styling */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
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
                    <a class="nav-link active" href="organization.php"><i class="bi bi-diagram-3"></i> Organization</a>
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
                    <h1 class="h3 fw-bold mb-1">Organization Management</h1>
                    <p class="text-muted">Kelola struktur organisasi perusahaan</p>
                </div>
                <?php if ($action !== 'add' && $action !== 'edit'): ?>
                    <a href="organization.php?action=add" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Anggota
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

            <?php if ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Form -->
                <div class="form-container">
                    <h4><?php echo $action === 'add' ? 'Tambah' : 'Edit'; ?> Anggota Organisasi</h4>
                    <form method="POST">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['name']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="position" class="form-control" required
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['position']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Level (1 = Direktur, 2 = Manajemen, 3 = Operasional, 4 = Tenaga Ahli)</label>
                            <select name="level" class="form-control" required>
                                <option value="1" <?php echo ($edit_data && $edit_data['level'] == 1) ? 'selected' : ''; ?>>1 - Direktur</option>
                                <option value="2" <?php echo ($edit_data && $edit_data['level'] == 2) ? 'selected' : ''; ?>>2 - Manajemen</option>
                                <option value="3" <?php echo ($edit_data && $edit_data['level'] == 3) ? 'selected' : ''; ?>>3 - Operasional</option>
                                <option value="4" <?php echo ($edit_data && $edit_data['level'] == 4) ? 'selected' : ''; ?>>4 - Tenaga Ahli</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Urutan dalam Level</label>
                            <input type="number" name="order_in_level" class="form-control" required min="1"
                                   value="<?php echo $edit_data ? $edit_data['order_in_level'] : '1'; ?>">
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-circle"></i> Simpan
                        </button>
                        <a href="organization.php" class="btn-cancel">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </form>
                </div>
            <?php else: ?>
                <!-- List Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Level</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($org = $org_list->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($org['name']); ?></td>
                                    <td><?php echo htmlspecialchars($org['position']); ?></td>
                                    <td>
                                        <?php
                                        $level_names = [
                                            1 => 'Direktur',
                                            2 => 'Manajemen',
                                            3 => 'Operasional',
                                            4 => 'Tenaga Ahli'
                                        ];
                                        echo $level_names[$org['level']] ?? 'Level ' . $org['level'];
                                        ?>
                                    </td>
                                    <td><?php echo $org['order_in_level']; ?></td>
                                    <td>
                                        <a href="?action=edit&id=<?php echo $org['id']; ?>" class="action-btn btn-edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="?action=delete&id=<?php echo $org['id']; ?>"
                                           class="action-btn btn-delete"
                                           onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>