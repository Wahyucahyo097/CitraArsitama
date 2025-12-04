<?php
session_start();
include 'config.php';
check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        
        $conn->query("UPDATE admin_users SET name='$name', email='$email' WHERE id={$_SESSION['admin_id']}");
        header('Location: settings.php?msg=Profile updated');
        exit();
    }
    
    if ($_POST['action'] === 'change_password') {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        
        $result = $conn->query("SELECT password FROM admin_users WHERE id={$_SESSION['admin_id']}");
        $user = $result->fetch_assoc();
        
        if (!password_verify($current_pass, $user['password'])) {
            header('Location: settings.php?error=Password salah');
            exit();
        }
        
        if ($new_pass !== $confirm_pass) {
            header('Location: settings.php?error=Password baru tidak cocok');
            exit();
        }
        
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $conn->query("UPDATE admin_users SET password='$hashed' WHERE id={$_SESSION['admin_id']}");
        header('Location: settings.php?msg=Password updated');
        exit();
    }
    
    // Handle hero images upload/replace
    if ($_POST['action'] === 'update_hero') {
        // ensure uploads dir exists
        $uploadsDir = __DIR__ . '/../assets/img/hero/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

        $existing = [];
        $res = $conn->query("SELECT value FROM settings WHERE `key`='hero_images' LIMIT 1");
        if ($res && $res->num_rows) {
            $existing = json_decode($res->fetch_assoc()['value'], true) ?: [];
        }

        $newFiles = [];
        if (isset($_FILES['hero_images'])) {
            foreach ($_FILES['hero_images']['name'] as $i => $name) {
                if (empty($name)) continue;
                $tmp = $_FILES['hero_images']['tmp_name'][$i];
                $err = $_FILES['hero_images']['error'][$i];
                if ($err !== UPLOAD_ERR_OK) continue;
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (!in_array($ext, $allowed)) continue;
                $base = preg_replace('/[^a-z0-9._-]/i', '_', pathinfo($name, PATHINFO_FILENAME));
                $filename = $base . '_' . time() . '_' . $i . '.' . $ext;
                if (move_uploaded_file($tmp, $uploadsDir . $filename)) {
                    $newFiles[] = $filename;
                }
            }
        }

        // If replace flag set, replace list; otherwise append
        if (isset($_POST['replace_hero']) && $_POST['replace_hero'] === '1') {
            $final = $newFiles;
        } else {
            $final = array_values(array_unique(array_merge($existing, $newFiles)));
        }

        $finalJson = json_encode($final);
        $check = $conn->query("SELECT * FROM settings WHERE `key`='hero_images'");
        if ($check->num_rows) {
            $conn->query("UPDATE settings SET `value` = '" . $conn->real_escape_string($finalJson) . "' WHERE `key`='hero_images'");
        } else {
            $conn->query("INSERT INTO settings (`key`,`value`) VALUES ('hero_images', '" . $conn->real_escape_string($finalJson) . "')");
        }

        header('Location: settings.php?msg=Hero updated');
        exit();
    }
}

// Handle remove hero image via GET
if (isset($_GET['action']) && $_GET['action'] === 'remove_hero' && isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $res = $conn->query("SELECT value FROM settings WHERE `key`='hero_images' LIMIT 1");
    if ($res && $res->num_rows) {
        $arr = json_decode($res->fetch_assoc()['value'], true) ?: [];
        $arr = array_values(array_filter($arr, function($v) use ($file){ return $v !== $file; }));
        $conn->query("UPDATE settings SET `value`='" . $conn->real_escape_string(json_encode($arr)) . "' WHERE `key`='hero_images'");
        $path = __DIR__ . '/../assets/img/hero/' . $file;
        if (file_exists($path)) @unlink($path);
    }
    header('Location: settings.php?msg=Hero image removed');
    exit();
}

$admin = $conn->query("SELECT * FROM admin_users WHERE id={$_SESSION['admin_id']}")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
                    <li class="nav-item"><a class="nav-link active" href="settings.php"><i class="bi bi-sliders"></i> Settings</a></li>
                    <li class="nav-item border-top mt-3 pt-3"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Settings</h1>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($_GET['msg']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><?php echo htmlspecialchars($_GET['error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="bi bi-person-circle"></i> Profile Settings</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_profile">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="bi bi-lock"></i> Change Password</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="change_password">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary"><i class="bi bi-lock-fill"></i> Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                
                    <!-- Hero Backgrounds -->
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="bi bi-image"></i> Hero Backgrounds</h5>
                                <?php
                                // load current hero images
                                $hero_images = [];
                                $r = $conn->query("SELECT value FROM settings WHERE `key`='hero_images' LIMIT 1");
                                if ($r && $r->num_rows) {
                                    $hero_images = json_decode($r->fetch_assoc()['value'], true) ?: [];
                                }
                                ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_hero">
                                    <div class="mb-3">
                                        <label class="form-label">Upload Hero Images (multiple allowed)</label>
                                        <input type="file" name="hero_images[]" class="form-control" multiple>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="replace_hero" name="replace_hero" value="1">
                                        <label class="form-check-label" for="replace_hero">Replace existing hero images</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Upload / Update</button>
                                </form>

                                <?php if (!empty($hero_images)): ?>
                                    <hr>
                                    <h6>Current hero images</h6>
                                    <div class="d-flex gap-3 flex-wrap mt-2">
                                        <?php foreach ($hero_images as $hi): ?>
                                            <div style="width:180px;">
                                                <img src="../assets/img/hero/<?php echo htmlspecialchars($hi); ?>" style="width:100%;height:auto;border:1px solid #ddd;padding:4px;" alt="">
                                                <div class="mt-1 d-flex justify-content-between">
                                                    <small class="text-muted"><?php echo htmlspecialchars($hi); ?></small>
                                                    <a href="settings.php?action=remove_hero&file=<?php echo urlencode($hi); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus gambar ini?')"><i class="bi bi-trash"></i></a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
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
