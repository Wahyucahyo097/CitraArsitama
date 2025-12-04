<?php
// Dynamic about page — loads content from DB or uses defaults
include __DIR__ . '/admin/config.php';

// Fetch about content from settings or pages table (fallback to defaults)
$about_title = 'About Us';
$about_text = 'Kami adalah tim profesional yang berdedikasi untuk memberikan solusi terbaik.';
$about_img = 'about-1.jpg';

// Try to fetch from settings if table exists (suppress errors to avoid fatal if table missing)
@$result = $conn->query("SELECT `value` FROM settings WHERE `key` = 'about_text' LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $about_text = htmlspecialchars($row['value']);
}

@$result = $conn->query("SELECT `value` FROM settings WHERE `key` = 'about_title' LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $about_title = htmlspecialchars($row['value']);
}

@$result = $conn->query("SELECT `value` FROM settings WHERE `key` = 'about_image' LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $about_img = htmlspecialchars($row['value']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>CV Citra Arsitama - About</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/company-logo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

</head>

<body class="about-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <img src="assets/img/company-logo.png" alt="">
        <h1 class="sitename">CV.Citra Arsitama</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="project.php">Project</a></li>
          <li><a href="about.php" class="active">About</a></li>
          <li><a href="news.html">News</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <a class="cta-btn" href="index.php#about">Home</a>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/P1.jpg);">
      <div class="container position-relative">
        <h1>About</h1>
        <p><?php echo $about_title; ?></p>
      </div>
    </div><!-- End Page Title -->


    <!-- Team Section -->
    <section id="team" class="team section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Susunan Organisasi</h2>
        <p>CV.Citra Arsitama</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-5">
<?php
// Fetch team members from database
$team_query = "SELECT id, name, position, image FROM team ORDER BY id ASC";
@$team_result = $conn->query($team_query);

if ($team_result && $team_result->num_rows > 0) {
    $delay = 100;
    while ($team_member = $team_result->fetch_assoc()) {
        $member_id = htmlspecialchars($team_member['id']);
        $member_name = htmlspecialchars($team_member['name']);
        $member_position = htmlspecialchars($team_member['position']);
        $member_image = htmlspecialchars($team_member['image']);
        $delay += 100;
        ?>
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
            <div class="member">
              <div class="pic"><img src="assets/img/team/<?php echo $member_image; ?>" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4><?php echo $member_name; ?></h4>
                <span><?php echo $member_position; ?></span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->
        <?php
    }
} else {
    // Fallback static data if no team in database
    ?>
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="member">
              <div class="pic"><img src="assets/img/team/team-1.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Rohman Eko Santoso, ST. M.Ars</h4>
                <span>Direktur</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="member">
              <div class="pic"><img src="assets/img/team/team-2.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Puput Fitria Setyawati, Amd.</h4>
                <span>Keuangan</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="member">
              <div class="pic"><img src="assets/img/team/team-3.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Ahmad Wahyudi</h4>
                <span>Administrasi</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->
    <?php
}
?>

        </div>

      </div>

    </section><!-- /Team Section -->

  </main>

  <footer class="footer-simple py-4" style="background-color: #f8f8f8; border-top: 1px solid #eee;">
    <div class="container text-center">

      <p class="mb-2" style="color: #333;">Copyright © 2025 Citra Arsitama. All right reserved.</p>

      <div class="footer-social" style="display: flex; justify-content: center; gap: 14px; font-size: 1.25rem;">
        <a href="#" style="text-decoration: none;"><i class="bi bi-facebook"></i></a>
        <a href="https://www.instagram.com/studio_citraarsitama?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" style="text-decoration: none;"><i class="bi bi-instagram"></i></a>
        <a href="#" style="text-decoration: none;"><i class="bi bi-linkedin"></i></a>
        <a href="#" style="text-decoration: none;"><i class="bi bi-youtube"></i></a>
      </div>

    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>