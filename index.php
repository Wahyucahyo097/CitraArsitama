<?php
// Dynamic frontpage - mirrors index.html layout but loads Portfolio and Clients from database
include __DIR__ . '/admin/config.php';
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

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="assets/img/company-logo.png" alt="">
        <h1 class="sitename">CV.Citra Arsitama</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="project.php" class="active">Project</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="news.php">News</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="cta-btn" href="admin/login.php">Login</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section (dynamic) -->
    <section id="hero" class="hero section dark-background">
      <div class="hero-slideshow" data-aos="fade-in">
        <?php
        // load hero images from settings (JSON array)
        $hero_images = [];
        $r = $conn->query("SELECT value FROM settings WHERE `key`='hero_images' LIMIT 1");
        if ($r && $r->num_rows) {
            $hero_images = json_decode($r->fetch_assoc()['value'], true) ?: [];
        }
        if (!empty($hero_images)) {
            $first = true;
            foreach ($hero_images as $hi) {
                $safe = htmlspecialchars($hi);
                echo '<img src="assets/img/hero/' . $safe . '" class="slide' . ($first ? ' active' : '') . '" alt="Hero">\n';
                $first = false;
            }
        } else {
            // fallback to existing static images
            echo '<img src="assets/img/1.jpg" class="slide active" alt="Hero image 1">\n';
            echo '<img src="assets/img/hero-bg.jpg" class="slide" alt="Hero image 2">\n';
        }
        ?>
      </div>
    </section><!-- /Hero Section -->


    <!-- Services Section -->
    <section id="services" class="services section">
      <div class="container" data-aos="fade-up">
        <div class="section-title text-center mb-4">
          <h2>Services</h2>
          <p>Kami menawarkan layanan berikut</p>
        </div>

        <div class="row gy-4">
<?php
$services_q = $conn->query("SELECT * FROM services ORDER BY id ASC");
if ($services_q && $services_q->num_rows > 0) {
    while ($s = $services_q->fetch_assoc()) {
        $title = htmlspecialchars($s['title']);
        $desc = htmlspecialchars($s['description']);
        $icon = htmlspecialchars($s['icon']);
        ?>
          <div class="col-lg-4 col-md-6">
            <div class="service-box h-100 text-center p-4">
              <div class="icon mb-3"><i class="bi <?php echo $icon; ?> fs-2"></i></div>
              <h4><?php echo $title; ?></h4>
              <p><?php echo $desc; ?></p>
            </div>
          </div>
        <?php
    }
} else {
    // fallback static services
    $fallback = [
        ['icon' => 'bi-gear', 'title' => 'Design', 'desc' => 'Lorem ipsum dolor sit amet.'],
        ['icon' => 'bi-phone', 'title' => 'Development', 'desc' => 'Consectetur adipiscing elit.'],
        ['icon' => 'bi-brush', 'title' => 'Branding', 'desc' => 'Integer nec odio.']
    ];
    foreach ($fallback as $s) {
        ?>
          <div class="col-lg-4 col-md-6">
            <div class="service-box h-100 text-center p-4">
              <div class="icon mb-3"><i class="bi <?php echo $s['icon']; ?> fs-2"></i></div>
              <h4><?php echo $s['title']; ?></h4>
              <p><?php echo $s['desc']; ?></p>
            </div>
          </div>
        <?php
    }
}
?>
        </div>

      </div>
    </section><!-- /Services Section -->


    <!-- Clients Section -->
    <section id="clients" class="clients section light-background">

      <div class="container" data-aos="fade-up">

        <div class="row gy-4">

<?php
// Try to load clients from DB; if none, fall back to existing static images
$clients_q = $conn->query("SELECT * FROM clients ORDER BY id ASC");
if ($clients_q && $clients_q->num_rows > 0) {
  while ($c = $clients_q->fetch_assoc()) {
    $img = htmlspecialchars($c['image']);
    if (!$img) continue;
    ?>
      <div class="col-xl-2 col-md-3 col-6 client-logo">
      <img src="assets/img/clients/<?php echo $img; ?>" class="img-fluid" alt="">
      </div><!-- End Client Item -->

    <?php
  }
} else {
  // fallbacks (original static set)
  $static = [
    'assets/img/clients/client-2.png',
    'assets/img/clients/client-1.png',
    'assets/img/clients/client-3.png',
    'assets/img/clients/client-4.png',
    'assets/img/clients/client-5.png',
    'assets/img/clients/client-6.png',
  ];
  foreach ($static as $s) {
    ?>
      <div class="col-xl-2 col-md-3 col-6 client-logo">
      <img src="<?php echo $s; ?>" class="img-fluid" alt="">
      </div><!-- End Client Item -->

    <?php
  }
}
?>

        </div>

      </div>

    </section><!-- /Clients Section -->



    <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio section">

        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

          <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
            <li data-filter="*" class="filter-active">All</li>
            <li data-filter=".filter-app">App</li>
            <li data-filter=".filter-product">Product</li>
            <li data-filter=".filter-branding">Branding</li>
            <li data-filter=".filter-books">Books</li>
          </ul><!-- End Portfolio Filters -->

          <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

<?php
// Load portfolio items from DB; fallback to some sample items if empty
$portfolio_q = $conn->query("SELECT * FROM portfolio ORDER BY id DESC");
if ($portfolio_q && $portfolio_q->num_rows > 0) {
    while ($p = $portfolio_q->fetch_assoc()) {
        $catClass = 'filter-' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $p['category']));
        $img = htmlspecialchars($p['image']);
        $title = htmlspecialchars($p['title']);
        $desc = htmlspecialchars($p['description']);
        $pid = (int)$p['id'];
        $link = htmlspecialchars($p['link'] ?: 'portfolio-details.html');

        echo "            <div class=\"col-lg-4 col-md-6 portfolio-item isotope-item {$catClass}\">\n";
        echo "              <div class=\"portfolio-content h-100\">\n";
        echo "                <img src=\"assets/img/portfolio/{$img}\" class=\"img-fluid\" alt=\"\">\n";
        echo "                <div class=\"portfolio-info\">\n";
        echo "                  <h4>{$title}</h4>\n";
        echo "                  <p>" . ($p['category'] ? htmlspecialchars($p['category']) : '') . "</p>\n";
        echo "                  <a href=\"assets/img/portfolio/{$img}\" title=\"{$title}\" data-gallery=\"portfolio-gallery\" class=\"glightbox preview-link\"><i class=\"bi bi-zoom-in\"></i></a>\n";
        echo "                  <a href=\"portfolio-details.php?id={$pid}\" title=\"More Details\" class=\"details-link\"><i class=\"bi bi-link-45deg\"></i></a>\n";
        echo "                </div>\n";
        echo "              </div>\n";
        echo "            </div><!-- End Portfolio Item -->\n\n";
    }
} else {
    // fallback static items (kept minimal, matched original structure)
    $static_port = [
        ['img' => 'assets/img/portfolio/2.jpg','title'=>'App 1','category'=>'App','href'=>'assets/img/portfolio/2.jpg'],
        ['img' => 'assets/img/portfolio/Picture1.png','title'=>'Product 1','category'=>'Product','href'=>'assets/img/portfolio/product-1.jpg'],
        ['img' => 'assets/img/portfolio/Picture4.png','title'=>'Branding 1','category'=>'Branding','href'=>'assets/img/portfolio/branding-1.jpg'],
    ];
    foreach ($static_port as $p) {
        echo "            <div class=\"col-lg-4 col-md-6 portfolio-item isotope-item filter-app\">\n";
        echo "              <div class=\"portfolio-content h-100\">\n";
        echo "                <img src=\"{$p['img']}\" class=\"img-fluid\" alt=\"\">\n";
        echo "                <div class=\"portfolio-info\">\n";
        echo "                  <h4>{$p['title']}</h4>\n";
        echo "                  <p>{$p['category']}</p>\n";
        echo "                  <a href=\"{$p['href']}\" title=\"{$p['title']}\" data-gallery=\"portfolio-gallery\" class=\"glightbox preview-link\"><i class=\"bi bi-zoom-in\"></i></a>\n";
        echo "                  <a href=\"portfolio-details.html\" title=\"More Details\" class=\"details-link\"><i class=\"bi bi-link-45deg\"></i></a>\n";
        echo "                </div>\n";
        echo "              </div>\n";
        echo "            </div><!-- End Portfolio Item -->\n\n";
    }
}
?>

          </div><!-- End Portfolio Container -->

        </div>


    </section><!-- /Portfolio Section -->

<?php
?>
<?php
?>
       

    <a href="https://wa.me/081226215789" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
    <i class="bi bi-whatsapp"></i>
</a>


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
