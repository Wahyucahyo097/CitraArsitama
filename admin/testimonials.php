<?php
session_start();
include 'config.php';
check_login();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM testimonials WHERE id = $id");
    header('Location: testimonials.php?msg=deleted');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = sanitize($_POST['client_name']);
    $position = sanitize($_POST['position']);
    $company = sanitize($_POST['company']);
    $content = sanitize($_POST['content']);
    $rating = sanitize($_POST['rating']);

    if ($action === 'add') {
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../assets/img/testimonials/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
        }
        $conn->query("INSERT INTO testimonials (client_name, position, company, content, image, rating) VALUES ('$client_name', '$position', '$company', '$content', '$image', '$rating')");
        $message = 'Testimonial berhasil ditambahkan!';
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $image = sanitize($_POST['old_image']);
        
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../assets/img/testimonials/";
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
        }
        
        $conn->query("UPDATE testimonials SET client_name='$client_name', position='$position', company='$company', content='$content', image='$image', rating='$rating' WHERE id=$id");
        $message = 'Testimonial berhasil diubah!';
    }
    
    header('Location: testimonials.php?msg=' . urlencode($message));
    exit();
}

$edit_data = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM testimonials WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

$testimonials_list = $conn->query("SELECT * FROM testimonials ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - Admin Panel</title>
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
                    <li class="nav-item"><a class="nav-link" href="clients.php"><i class="bi bi-building"></i> Clients</a></li>
                    <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-sliders"></i> Settings</a></li>
                    <li class="nav-item border-top mt-3 pt-3"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Testimonials Management</h1>
                    <?php if ($action !== 'add' && $action !== 'edit'): ?>
                        <a href="testimonials.php?action=add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Testimonial</a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($_GET['msg']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr><th>ID</th><th>Client</th><th>Company</th><th>Rating</th><th>Image</th><th>Aksi</th></tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $testimonials_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['company']); ?></td>
                                            <td><span class="badge bg-warning"><?php echo $row['rating']; ?>/5</span></td>
                                            <td><?php echo $row['image'] ? '<img src="../assets/img/testimonials/' . htmlspecialchars($row['image']) . '" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">' : '-'; ?></td>
                                            <td>
                                                <a href="testimonials.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                                <a href="testimonials.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin dihapus?')"><i class="bi bi-trash"></i></a>
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
                            <h5 class="card-title mb-4"><?php echo $action === 'add' ? 'Tambah Testimonial' : 'Edit Testimonial'; ?></h5>
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                    <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($edit_data['image']); ?>">
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Client Name</label>
                                        <input type="text" name="client_name" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['client_name']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Position</label>
                                        <input type="text" name="position" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['position']) : ''; ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Company</label>
                                        <input type="text" name="company" class="form-control" value="<?php echo $edit_data ? htmlspecialchars($edit_data['company']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rating</label>
                                        <select name="rating" class="form-control" required>
                                            <option value="">-- Select Rating --</option>
                                            <option value="5" <?php echo $edit_data && $edit_data['rating'] == 5 ? 'selected' : ''; ?>>5 Stars</option>
                                            <option value="4" <?php echo $edit_data && $edit_data['rating'] == 4 ? 'selected' : ''; ?>>4 Stars</option>
                                            <option value="3" <?php echo $edit_data && $edit_data['rating'] == 3 ? 'selected' : ''; ?>>3 Stars</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Content</label>
                                    <textarea name="content" class="form-control" rows="4" required><?php echo $edit_data ? htmlspecialchars($edit_data['content']) : ''; ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" <?php echo $action === 'add' ? 'required' : ''; ?>>
                                    <?php if ($edit_data && $edit_data['image']): ?>
                                        <small class="text-muted">Current image: <?php echo htmlspecialchars($edit_data['image']); ?></small>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> <?php echo $action === 'add' ? 'Tambah' : 'Update'; ?></button>
                                    <a href="testimonials.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Batal</a>
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
