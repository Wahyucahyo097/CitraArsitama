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
    <title>Portfolio - Admin Panel</title>
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
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="portfolio.php">
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
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Portfolio Management</h1>
                    <?php if ($action !== 'add' && $action !== 'edit'): ?>
                        <a href="portfolio.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Portfolio
                        </a>
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
                                        <th>Category</th>
                                        <th>Thumbnail</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $portfolio_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['category']); ?></span></td>
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
                                        <label class="form-label">Category</label>
                                        <select name="category" class="form-control" required>
                                            <option value="">-- Pilih Category --</option>
                                            <option value="App" <?php echo $edit_data && $edit_data['category'] === 'App' ? 'selected' : ''; ?>>App</option>
                                            <option value="Product" <?php echo $edit_data && $edit_data['category'] === 'Product' ? 'selected' : ''; ?>>Product</option>
                                            <option value="Branding" <?php echo $edit_data && $edit_data['category'] === 'Branding' ? 'selected' : ''; ?>>Branding</option>
                                            <option value="Books" <?php echo $edit_data && $edit_data['category'] === 'Books' ? 'selected' : ''; ?>>Books</option>
                                        </select>
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
