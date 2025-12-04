<?php
// Dynamic portfolio details page — fetches single portfolio item by ID from DB
include __DIR__ . '/admin/config.php';

// Get portfolio ID from URL
$portfolio_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch portfolio item from DB
$portfolio = null;
if ($portfolio_id > 0) {
    $result = $conn->query("SELECT * FROM portfolio WHERE id = $portfolio_id");
    if ($result && $result->num_rows > 0) {
        $portfolio = $result->fetch_assoc();
    }
}

// Fallback if not found
if (!$portfolio) {
    $portfolio = [
        'title' => 'Portfolio Item',
        'category' => 'Web Design',
        'description' => 'Exercitationem repudiandae officiis neque suscipit non officia eaque itaque enim.',
        'image' => 'app-1.jpg',
        'link' => 'http://www.example.com'
    ];
}

$title = htmlspecialchars($portfolio['title']);
$category = htmlspecialchars($portfolio['category']);
$desc = htmlspecialchars($portfolio['description']);
$img = htmlspecialchars($portfolio['image']);
$link = htmlspecialchars($portfolio['link']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>CV Citra Arsitama</title>
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

<body class="portfolio-details-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <img src="assets/img/company-logo.png" alt="">
        <h1 class="sitename">CV.Citra Arsitama</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="project.php" class="active">Project</a></li>
          <li><a href="about.php">About</a></li>
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
        <h1>Details</h1>
        <p><?php echo $title; ?></p>
      </div>
    </div><!-- End Page Title -->

    <!-- Portfolio Details Section -->
    <section id="portfolio-details" class="portfolio-details section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-8">
            <div class="portfolio-details-slider swiper init-swiper">

              <script type="application/json" class="swiper-config">
                {
                  "loop": true,
                  "speed": 600,
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": "auto",
                  "pagination": {
                    "el": ".swiper-pagination",
                    "type": "bullets",
                    "clickable": true
                  }
                }
              </script>

              <div class="swiper-wrapper align-items-center">

                <div class="swiper-slide">
                  <img src="assets/img/portfolio/<?php echo $img; ?>" alt="">
                </div>

                <div class="swiper-slide">
                  <img src="assets/img/portfolio/<?php echo $img; ?>" alt="">
                </div>

                <div class="swiper-slide">
                  <img src="assets/img/portfolio/<?php echo $img; ?>" alt="">
                </div>

                <div class="swiper-slide">
                  <img src="assets/img/portfolio/<?php echo $img; ?>" alt="">
                </div>

              </div>
              <div class="swiper-pagination"></div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="portfolio-info" data-aos="fade-up" data-aos-delay="200">
              <h3>Project information</h3>
              <ul>
                <li><strong>Category</strong>: <?php echo $category; ?></li>
                <li><strong>Client</strong>: Citra Arsitama</li>
                <li><strong>Project date</strong>: <?php echo date('d M, Y'); ?></li>
                <li><strong>Project URL</strong>: <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></li>
              </ul>
            </div>
            <div class="portfolio-description" data-aos="fade-up" data-aos-delay="300">
              <h2><?php echo $title; ?></h2>
              <p>
                <?php echo $desc; ?>
              </p>
            </div>
          </div>

        </div>

      </div>

    </section><!-- /Portfolio Details Section -->

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
