<?php
// Dynamic news page — loads news from database
include __DIR__ . '/admin/config.php';

// Load social media links
$social_links = [];
$social_keys = ['whatsapp', 'instagram', 'youtube', 'facebook', 'twitter', 'linkedin'];
foreach ($social_keys as $key) {
    $r = $conn->query("SELECT value FROM settings WHERE `key`='$key' LIMIT 1");
    $social_links[$key] = ($r && $r->num_rows) ? $r->fetch_assoc()['value'] : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>CV Citra Arsitama - News</title>
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

<body class="starter-page-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <img src="assets/img/company-logo.png" alt="">
        <h1 class="sitename">CV.Citra Arsitama</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="project.php">Project</a></li>
  
  <li class="dropdown">
    <a href="about.php" class="active">
      <span>About</span> 
      <i class="bi bi-chevron-down toggle-dropdown"></i>
    </a>
    <ul>
      <li><a href="about.php#company-data">Identitas Perusahaan</a></li>
      <li><a href="about.php#team">Susunan Organisasi</a></li>
      <li><a href="about.php#tenaga-ahli">Daftar Tenaga Ahli</a></li> <li class="dropdown">
      <li class="dropdown">
        <a href="#"><span>Legalitas Perusahaan</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
        <ul>
          <li><a href="about.php#sbu">SBU Konstruksi</a></li>
          <li><a href="about.php#sbu-non-kon">SBU Non-Konstruksi</a></li> 
        </ul>
      </li>
    </ul>
  </li>
         <li class="dropdown">
  <a href="#">
    <span>Layanan</span>
    <i class="bi bi-chevron-down toggle-dropdown"></i>
  </a>
  <ul>
    <?php
    // Function to build menu recursively
    function build_menu($parent_id = 0) {
        global $conn;
        $menu_q = $conn->query("SELECT * FROM menu WHERE parent_id = $parent_id ORDER BY order_by ASC");
        $output = '';
        while ($menu = $menu_q->fetch_assoc()) {
            $has_children = $conn->query("SELECT COUNT(*) as count FROM menu WHERE parent_id = {$menu['id']}")->fetch_assoc()['count'] > 0;
            if ($has_children) {
                $output .= '<li class="dropdown">';
                $output .= '<a href="' . htmlspecialchars($menu['url']) . '">';
                $output .= '<span>' . htmlspecialchars($menu['title']) . '</span>';
                $output .= '<i class="bi bi-chevron-down toggle-dropdown"></i>';
                $output .= '</a>';
                $output .= '<ul>';
                $output .= build_menu($menu['id']);
                $output .= '</ul>';
                $output .= '</li>';
            } else {
                $output .= '<li><a href="' . htmlspecialchars($menu['url']) . '">' . htmlspecialchars($menu['title']) . '</a></li>';
            }
        }
        return $output;
    }
    echo build_menu();
    ?>
  </ul>
</li>
          <li><a href="news.php" class="active">News</a></li>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </ul>
      </nav>

      <a class="cta-btn" href="index.php#about">Home</a>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/P1.jpg);">
      <div class="container position-relative">
        <h1>NEWS</h1>
        <nav class="breadcrumbs">
        </nav>
      </div>
    </div><!-- End Page Title -->

 <!-- Services Section (News) -->
    <section id="services" class="services section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-5">
<?php
// Fetch news from database
$news_query = "SELECT id, title, description, image FROM news ORDER BY created_at DESC";
@$news_result = $conn->query($news_query);

if ($news_result && $news_result->num_rows > 0) {
    $delay = 200;
    while ($news_item = $news_result->fetch_assoc()) {
        $news_id = htmlspecialchars($news_item['id']);
        $news_title = htmlspecialchars($news_item['title']);
        $news_description = htmlspecialchars($news_item['description']);
        $news_image = htmlspecialchars($news_item['image']);
        ?>

          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
            <div class="service-item">
              <div class="img">
                <img src="assets/img/news/<?php echo $news_image; ?>" class="img-fluid" alt="">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-broadcast"></i>
                </div>
                <a href="news-details.php?id=<?php echo $news_id; ?>" class="stretched-link">
                  <h3><?php echo $news_title; ?></h3>
                </a>
                <p><?php echo $news_description; ?></p>
              </div>
            </div>
          </div><!-- End Service Item -->

        <?php
        $delay += 100;
    }
} else {
    // Fallback static data if no news in database
    ?>

          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
            <div class="service-item">
              <div class="img">
                <img src="assets/img/news/news.jpg" class="img-fluid" alt="">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-broadcast"></i>
                </div>
                <a href="service-details.html" class="stretched-link">
                  <h3>Nesciunt Mete</h3>
                </a>
                <p>Provident nihil minus qui consequatur non omnis maiores. Eos accusantium minus dolores iure perferendis.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
            <div class="service-item">
              <div class="img">
                <img src="assets/img/news/news.jpg" class="img-fluid" alt="">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-broadcast"></i>
                </div>
                <a href="service-details.html" class="stretched-link">
                  <h3>Eosle Commodi</h3>
                </a>
                <p>Ut autem aut autem non a. Sint sint sit facilis nam iusto sint. Libero corrupti neque eum hic non ut nesciunt dolorem.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="400">
            <div class="service-item">
              <div class="img">
                <img src="assets/img/news/news.jpg" class="img-fluid" alt="">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-broadcast"></i>
                </div>
                <a href="service-details.html" class="stretched-link">
                  <h3>Ledo Markt</h3>
                </a>
                <p>Ut excepturi voluptatem nisi sed. Quidem fuga consequatur. Minus ea aut. Vel qui id voluptas adipisci eos earum corrupti.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

    <?php
}
?>

        </div>

      </div>

    </section><!-- /Services Section -->


  </main>

<footer class="footer-simple py-4" style="background-color: #f8f8f8; border-top: 1px solid #eee;">
  <div class="container position-relative">

    <p class="mb-2 text-center" style="color: #333;">
      Copyright © 2026 Citra Arsitama. All right reserved.
    </p>

    <div class="footer-social text-center mb-3"
         style="display: flex; justify-content: center; gap: 14px; font-size: 1.25rem;">
      <?php
      $social_icons = [
          'facebook' => 'bi-facebook',
          'instagram' => 'bi-instagram',
          'linkedin' => 'bi-linkedin',
          'youtube' => 'bi-youtube',
          'twitter' => 'bi-twitter'
      ];
      foreach ($social_icons as $key => $icon) {
          if (!empty($social_links[$key])) {
              echo '<a href="' . htmlspecialchars($social_links[$key]) . '" target="_blank" rel="noopener noreferrer"><i class="bi ' . $icon . '"></i></a>';
          }
      }
      ?>
    </div>

    <!-- Gambar kanan bawah footer -->
    <div class="footer-image">
      <img src="assets/img/footer.png" alt="Footer Image">
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
