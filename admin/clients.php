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
    <title>Clients - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <link href="../assets/img/company-logo.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-md-block bg-dark sidebar">
                <div class="sidebar-header">
                    <h5 class="text-white mb-4 mt-3">CA Admin</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio.php"><i class="bi bi-images"></i> Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="bi bi-gear"></i> Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="team.php"><i class="bi bi-people"></i> Team</a></li>
                    <li class="nav-item"><a class="nav-link active" href="clients.php"><i class="bi bi-building"></i> Clients</a></li>
                    <li class="nav-item"><a class="nav-link" href="news.php"><i class="bi bi-newspaper"></i> News</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-sliders"></i> Settings</a></li>
                    <li class="nav-item border-top mt-3 pt-3"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Clients Management</h1>
                    <?php if ($action !== 'add' && $action !== 'edit'): ?>
                        <a href="clients.php?action=add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Client</a>
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
