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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_social') {
    $social_links = [
        'whatsapp' => sanitize($_POST['whatsapp']),
        'instagram' => sanitize($_POST['instagram']),
        'youtube' => sanitize($_POST['youtube']),
        'facebook' => sanitize($_POST['facebook']),
        'twitter' => sanitize($_POST['twitter']),
        'linkedin' => sanitize($_POST['linkedin'])
    ];

    foreach ($social_links as $key => $value) {
        $check = $conn->query("SELECT * FROM settings WHERE `key`='$key'");
        if ($check->num_rows) {
            $conn->query("UPDATE settings SET `value`='$value' WHERE `key`='$key'");
        } else {
            $conn->query("INSERT INTO settings (`key`,`value`) VALUES ('$key', '$value')");
        }
    }

    header('Location: settings.php?msg=Social media links updated');
    exit();
}

$admin = $conn->query("SELECT * FROM admin_users WHERE id={$_SESSION['admin_id']}")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Citra Arsitama</title>
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
                    <a class="nav-link" href="clients.php"><i class="bi bi-building"></i> Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="news.php"><i class="bi bi-newspaper"></i> News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="settings.php"><i class="bi bi-gear-wide-connected"></i> Settings</a>
                </li>
            </ul>
        </nav>

            <main class="col-md-10 ms-sm-auto main-content">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Settings</h1>
                        <p class="text-muted">Kelola pengaturan akun dan website</p>
                    </div>
                    <img src="../assets/img/company-logo.png" alt="Logo" height="45" onerror="this.style.display='none'">
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
                
                    <!-- Social Media Links -->
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="bi bi-share"></i> Social Media Links</h5>
                                <?php
                                // Load current social links
                                $social = [];
                                $social_keys = ['whatsapp', 'instagram', 'youtube', 'facebook', 'twitter', 'linkedin'];
                                foreach ($social_keys as $key) {
                                    $r = $conn->query("SELECT value FROM settings WHERE `key`='$key' LIMIT 1");
                                    $social[$key] = ($r && $r->num_rows) ? $r->fetch_assoc()['value'] : '';
                                }
                                ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_social">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">WhatsApp Number (e.g. 6281226215789)</label>
                                            <input type="text" name="whatsapp" class="form-control" value="<?php echo htmlspecialchars($social['whatsapp']); ?>" placeholder="6281226215789">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Instagram URL</label>
                                            <input type="url" name="instagram" class="form-control" value="<?php echo htmlspecialchars($social['instagram']); ?>" placeholder="https://www.instagram.com/username">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">YouTube URL</label>
                                            <input type="url" name="youtube" class="form-control" value="<?php echo htmlspecialchars($social['youtube']); ?>" placeholder="https://www.youtube.com/channel/...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Facebook URL</label>
                                            <input type="url" name="facebook" class="form-control" value="<?php echo htmlspecialchars($social['facebook']); ?>" placeholder="https://www.facebook.com/username">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Twitter URL</label>
                                            <input type="url" name="twitter" class="form-control" value="<?php echo htmlspecialchars($social['twitter']); ?>" placeholder="https://twitter.com/username">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">LinkedIn URL</label>
                                            <input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($social['linkedin']); ?>" placeholder="https://www.linkedin.com/in/username">
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Social Links</button>
                                </form>
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
