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
<style>
    .sbu-poster-container {
      font-family: 'Roboto', sans-serif;
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .sbu-title { text-align: center; color: #003366; margin-bottom: 25px; border-bottom: 3px solid #0056b3; padding-bottom: 15px; }
    .sbu-title h2 { font-weight: 700; text-transform: uppercase; margin: 0; }
    .responsive-blue-table { width: 100%; border-collapse: collapse; background-color: #fff; }
    .responsive-blue-table thead tr { background-color: #0056b3; color: #ffffff; }
    .responsive-blue-table th, .responsive-blue-table td { padding: 12px 15px; border: 1px solid #dee2e6; text-align: left; }
    .responsive-blue-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
    
    @media screen and (max-width: 768px) {
      .responsive-blue-table thead { display: none; }
      .responsive-blue-table tr { display: block; margin-bottom: 15px; border: 1px solid #0056b3; }
      .responsive-blue-table td { display: block; text-align: right; padding-left: 50%; position: relative; border-bottom: 1px solid #eee; }
      .responsive-blue-table td::before { content: attr(data-label); position: absolute; left: 10px; font-weight: bold; color: #0056b3; }
    }

    .company-data-wrapper {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #0056b3;
    margin-top: 40px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .data-header {
  background: #003366; /* Biru Gelap */
  padding: 25px;
  text-align: center;
}

.data-header h3 {
  color: #ffffff !important; /* Memastikan tulisan warna putih */
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 3px;
  margin: 0;
  font-size: 1.5rem;
  /* Memberikan bayangan halus agar teks lebih tajam */
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3); 
}
  .data-body {
    padding: 30px;
  }

  .data-row {
    display: flex;
    border-bottom: 1px solid #eee;
    padding: 12px 0;
  }

  .data-row:last-child { border-bottom: none; }

  .data-label {
    flex: 0 0 35%;
    font-weight: 700;
    color: #0056b3;
    text-transform: uppercase;
    font-size: 0.9rem;
  }

  .data-value {
    flex: 1;
    color: #333;
    font-size: 1rem;
  }

  .sub-section-title {
    background: #eef6fc;
    padding: 8px 15px;
    margin: 20px 0 10px 0;
    font-weight: bold;
    color: #003366;
    border-left: 4px solid #0056b3;
  }

  @media (max-width: 768px) {
    .data-row { flex-direction: column; }
    .data-label { margin-bottom: 5px; }
  }

  /* Container Utama Bagan */
  .org-chart-container {
    padding: 50px 20px;
    background: #fdfdfd;
    text-align: center;
    overflow-x: auto; /* Agar bisa di-scroll jika layar kekecilan */
  }

  .org-chart-title {
    background: #003366;
    color: white;
    padding: 15px;
    display: inline-block;
    font-weight: bold;
    margin-bottom: 40px;
    text-transform: uppercase;
    border-radius: 5px;
  }

  /* Struktur Level */
  .org-level {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
    position: relative;
  }

  /* Kotak Anggota */
  .org-node {
    background: #4a86e8; /* Biru sesuai gambar */
    color: white;
    padding: 15px;
    border-radius: 12px;
    min-width: 200px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border: 2px dashed #ffffff; /* Garis putus-putus sesuai gambar */
    margin: 0 10px;
  }

  .org-node .role {
    font-size: 0.8rem;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(255,255,255,0.3);
    padding-bottom: 5px;
    margin-bottom: 5px;
    font-weight: bold;
  }

  .org-node .name {
    font-size: 0.95rem;
    font-weight: 500;
  }

  /* Garis Penghubung Sederhana (Vertical) */
  .line-v {
    width: 2px;
    height: 30px;
    background: #333;
    margin: 0 auto;
  }

  /* Khusus Grid Tenaga Ahli (Bagian Bawah) */
  .expert-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto;
  }

  @media (max-width: 992px) {
    .expert-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 576px) {
    .expert-grid { grid-template-columns: 1fr; }
  }
  </style>
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
          <li><a href="news.php">News</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <a class="cta-btn" href="index.php#about">Home</a>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/back1.jpg);">
      <div class="container position-relative">
        <h1>About</h1>
        <p><?php echo $about_title; ?></p>
      </div>
    </div><!-- End Page Title -->


    <!-- Team Section -->
    </section><!-- /Team Section -->
    <section id="company-data" class="company-data section" style="padding-top: 0;">
      <div class="container" data-aos="fade-up">
        <div class="company-data-wrapper">
          <div class="data-header">
            <h3 class="m-0">Informasi Identitas Perusahaan</h3>
          </div>
          
          <div class="data-body">
            <div class="data-row">
              <div class="data-label">Nama Perusahaan</div>
              <div class="data-value"><strong>CV. CITRA ARSITAMA</strong></div>
            </div>
            <div class="data-row">
              <div class="data-label">Bidang Usaha</div>
              <div class="data-value">Jasa Konsultasi Teknik</div>
            </div>

            <div class="sub-section-title">Akte Pendirian Perusahaan</div>
            <div class="data-row">
              <div class="data-label">Nomor / Tanggal</div>
              <div class="data-value">01 / 7 Januari 2005</div>
            </div>
            <div class="data-row">
              <div class="data-label">Notaris</div>
              <div class="data-value">R.M. Soetomo Soeprapto, SH.</div>
            </div>

            <div class="sub-section-title">Akte Perubahan Terakhir</div>
            <div class="data-row">
              <div class="data-label">Nomor / Tanggal</div>
              <div class="data-value">68 / 20 Nopember 2013</div>
            </div>
            <div class="data-row">
              <div class="data-label">Notaris</div>
              <div class="data-value">Sugiharto, SH.</div>
            </div>

            <div class="sub-section-title">Perizinan Berusaha Berbasis Risiko</div>
            <div class="data-row">
              <div class="data-label">NIB (Nomor Induk Berusaha)</div>
              <div class="data-value">0220106510565</div>
            </div>
            <div class="data-row">
              <div class="data-label">NPWP</div>
              <div class="data-value">02.299.655.7-532.000</div>
            </div>
            
            
            <div class="sub-section-title">Informasi Perbankan</div>
            <div class="data-row">
              <div class="data-label">Bank</div>
              <div class="data-value">BPD BANK JATENG CABANG KOORDINATOR SEMARANG</div>
            </div>
            <div class="data-row">
              <div class="data-label">No. Rekening</div>
              <div class="data-value">1-021-01451-2</div>
            </div>
            <div class="data-row">
              <div class="data-label">Keanggotaan</div>
              <div class="data-value">INKINDO (DPD JAWA TENGAH) : 12427/P/0577.JT</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
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

<section class="org-chart-container">
<?php
// Fetch organization structure from database
$org_query = "SELECT name, position, level FROM organization_structure ORDER BY level ASC, order_in_level ASC";
@$org_result = $conn->query($org_query);

$org_data = [];
if ($org_result && $org_result->num_rows > 0) {
    while ($org_member = $org_result->fetch_assoc()) {
        $org_data[$org_member['level']][] = $org_member;
    }
}

// Display organization chart
foreach ($org_data as $level => $members) {
    if ($level == 4) {
        // Special handling for level 4 (TENAGA AHLI)
        echo '<p><strong>TENAGA AHLI</strong></p>';
        echo '<div class="expert-grid">';
        foreach ($members as $member) {
            echo '<div class="org-node">';
            echo '<div class="role">' . htmlspecialchars($member['position']) . '</div>';
            echo '<div class="name">' . htmlspecialchars($member['name']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="org-level">';
        foreach ($members as $member) {
            $bg_color = ($level == 1) ? '#2a5699' : (($level == 3) ? '#3c78d8' : '#4a86e8');
            echo '<div class="org-node" style="background: ' . $bg_color . ';">';
            echo '<div class="role">' . htmlspecialchars($member['position']) . '</div>';
            echo '<div class="name">' . htmlspecialchars($member['name']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
}

// If no data in database, show default static data
if (empty($org_data)) {
?>
  <div class="org-level">
    <div class="org-node" style="background: #2a5699;">
      <div class="role">Direktur</div>
      <div class="name">Rohman Eko Santoso, ST. M.Ars</div>
    </div>
  </div>

  <div class="org-level">
    <div class="org-node">
      <div class="role">Keuangan</div>
      <div class="name">Puput Fitria Setyawati, Amd.</div>
    </div>
    <div class="org-node">
      <div class="role">Administrasi</div>
      <div class="name">Ahmad Wahyudi</div>
    </div>
  </div>

  <div class="org-level">
    <div class="org-node" style="background: #3c78d8;">
      <div class="role">Bagian Teknik & Operasional (Sipil)</div>
      <div class="name">Sudiyoko, ST.</div>
    </div>
    <div class="org-node" style="background: #3c78d8;">
      <div class="role">Bagian Teknik & Operasional (Arsitektur)</div>
      <div class="name">Anjar Saptoyogo, ST.</div>
    </div>
  </div>

  <p><strong>TENAGA AHLI</strong></p>

  <div class="expert-grid">
    <div class="org-node">
      <div class="role">Ahli Sipil</div>
      <div class="name">Muhammadun, ST. MT</div>
    </div>
    <div class="org-node">
      <div class="role">Ahli M.E.P</div>
      <div class="name">Bayu Pradana, ST. MT</div>
    </div>
    <div class="org-node">
      <div class="role">Ahli Planologi</div>
      <div class="name">Fajar Nugroho, ST.</div>
    </div>
    <div class="org-node">
      <div class="role">Ahli Arsitektur</div>
      <div class="name">Ronny AB Wardhana, ST. MT</div>
    </div>
    
    <div class="org-node">
      <div class="role">Ahli Sipil</div>
      <div class="name">Mela Fitri Astuti, ST</div>
    </div>
    <div class="org-node" style="visibility: hidden;"></div> <div class="org-node">
      <div class="role">Ahli Planologi</div>
      <div class="name">Febri Dwi Astuti, S.Pwk</div>
    </div>
    <div class="org-node">
      <div class="role">Ahli Arsitektur</div>
      <div class="name">Bagas Harda Prastya, S.Ars</div>
    </div>
  </div>
<?php
}
?>

    <section id="sbu" class="sbu section">
      <div class="container" data-aos="fade-up">
        <div class="sbu-poster-container">
          <div class="sbu-title">
            <h2>Legalitas Perusahaan</h2>
            <p>Sertifikat Badan Usaha Jasa Konsultan Perencana Konstruksi</p>
          </div>
          <table class="responsive-blue-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Kualifikasi</th>
                <th>Kode</th>
                <th>Sifat</th>
                <th>KBLI</th>
                <th>Subklasifikasi</th>
                <th>Registrasi LPJK</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td data-label="No">1</td>
                <td data-label="Kualifikasi">Kecil</td>
                <td data-label="Kode">AR001</td>
                <td data-label="Sifat">Umum</td>
                <td data-label="KBLI">71101 - Aktivitas Arsitektur</td>
                <td data-label="Subklasifikasi">Jasa Arsitektural Bangunan Gedung Hunian dan Non Hunian</td>
                <td data-label="Registrasi">F.3.01.AR.K.02.2023.0053522</td>
              </tr>
              <tr>
                <td data-label="No">2</td>
                <td data-label="Kualifikasi">Kecil</td>
                <td data-label="Kode">AR002</td>
                <td data-label="Sifat">Umum</td>
                <td data-label="KBLI">71101 - Aktivitas Arsitektur</td>
                <td data-label="Subklasifikasi">Jasa Arsitektural Lainnya</td>
                <td data-label="Registrasi">F.3.01.AR.K.02.2023.0053522</td>
              </tr>
              <tr>
                <td data-label="No">3</td>
                <td data-label="Kualifikasi">Kecil</td>
                <td data-label="Kode">RK002</td>
                <td data-label="Sifat">Umum</td>
                <td data-label="KBLI">71102 - Aktivitas Keinsinyuran dan Konsultasi Teknis YBDI</td>
                <td data-label="Subklasifikasi">Jasa Rekayasa Pekerjaan TeknikSipil Sumber Daya Air</td>
                <td data-label="Registrasi">F.3.01.RK.K.02.2023.0053522</td>
              </tr>
              <tr>
                <td data-label="No">4</td>
                <td data-label="Kualifikasi">Kecil</td>
                <td data-label="Kode">AL002</td>
                <td data-label="Sifat">Umum</td>
                <td data-label="KBLI">71101 - Aktivitas Arsitektur</td>
                <td data-label="Subklasifikasi">Jasa Pengembangan Wilayah</td>
                <td data-label="Registrasi">F.3.01.AL.K.02.2023.0053522</td>
              </tr>
              <tr>
                <td data-label="No">5</td>
                <td data-label="Kualifikasi">Kecil</td>
                <td data-label="Kode">AL003</td>
                <td data-label="Sifat">Umum</td>
                <td data-label="KBLI">71101 - Aktivitas Arsitektur</td>
                <td data-label="Subklasifikasi">Jasa Pengembangan Perkotaan</td>
                <td data-label="Registrasi">F.3.01.AL.K.02.2023.0053522</td>
              </tr>
              <tr>
                <td data-label="No">6</td>
                <td data-label="Kualifikasi">Kecil</td>
                <td data-label="Kode">AL004</td>
                <td data-label="Sifat">Umum</td>
                <td data-label="KBLI">71101 - Aktivitas Arsitektur</td>
                <td data-label="Subklasifikasi">Jasa PengembanganLingkungan Bangunan dan Lanskap</td>
                <td data-label="Registrasi">F.3.01.AL.K.02.2023.0053522</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section id="sbu-non-kon" class="sbu section" style="padding-top: 0;">
      <div class="container" data-aos="fade-up">
        <div class="sbu-poster-container">
          <div class="sbu-title">
            <h2>Legalitas Perusahaan</h2>
            <p>Sertifikat Badan Usaha Jasa Konsultan Perencana Non-Konstruksi</p>
          </div>
          <table class="responsive-blue-table">
            <thead>
              <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 70%;">Sub Layanan / Bidang Pekerjaan</th>
                <th style="width: 22%;">Kode</th>
              </tr>
            </thead>
            <tbody>
              <tr class="row-category">
                <td data-label="No">1</td>
                <td data-label="Sub Layanan">TRANSPORTASI</td>
                <td data-label="Kode">1.02</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Pengembangan Sarana Transportasi</td>
                <td data-label="Kode">1.02.01</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Legislasi/Peraturan Bidang Transportasi</td>
                <td data-label="Kode">1.02.02</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Usaha Jasa Angkutan</td>
                <td data-label="Kode">1.02.03</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Penyusun Dokumen Analisis Dampak Lalu Lintas</td>
                <td data-label="Kode">1.02.04</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Sub-bidang Transportasi Lainnya</td>
                <td data-label="Kode">1.02.99</td>
              </tr>

              <tr class="row-category">
                <td data-label="No">2</td>
                <td data-label="Sub Layanan">JASA SURVEY</td>
                <td data-label="Kode">1.SS</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Survey Teristris</td>
                <td data-label="Kode">1.SS.01</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Penginderaan Jauh / Fotogrametri</td>
                <td data-label="Kode">1.SS.02</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Survey Hidrografi / Batimetri</td>
                <td data-label="Kode">1.SS.03</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Sistem Informasi Geografi</td>
                <td data-label="Kode">1.SS.04</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Survey Registrasi Kepemilikan Tanah / Kadastral</td>
                <td data-label="Kode">1.SS.05</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Survey Geologi dan Geofisika</td>
                <td data-label="Kode">1.SS.06</td>
              </tr>
               <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Survey Pertanian</td>
                <td data-label="Kode">1.SS.07</td>
              </tr>
               <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Survey non Seismik</td>
                <td data-label="Kode">1.SS.08</td>
              </tr>
               <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Survey Geologi dan Geofisika (non seismik)</td>
                <td data-label="Kode">1.SS.09</td>
              </tr>

              <tr class="row-category">
                <td data-label="No">3</td>
                <td data-label="Sub Layanan">JASA STUDI, PENELITIAN & BANTUAN TEKNIK</td>
                <td data-label="Kode">1.SI</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Studi Makro</td>
                <td data-label="Kode">1.SI.01</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Studi Kelayakan & Studi Mikro Lainnya</td>
                <td data-label="Kode">1.SI.02</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Studi Perencanaan Umum</td>
                <td data-label="Kode">1.SI.03</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Penelitian</td>
                <td data-label="Kode">1.SI.04</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Bantuan Teknik</td>
                <td data-label="Kode">1.SI.05</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Penelitian dan Pengembangan Minyak dan Gas Bumi</td>
                <td data-label="Kode">1.SI.06</td>
              </tr>

              <tr class="row-category">
                <td data-label="No">4</td>
                <td data-label="Sub Layanan">JASA KONSULTANSI MANAJEMEN</td>
                <td data-label="Kode">1.MS</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Perencanaan Sistem Akuntansi</td>
                <td data-label="Kode">1.MS.01</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Pelatihan dan Pengembangan SDM</td>
                <td data-label="Kode">1.MS.02</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Konsultasi Manajemen Fungsional</td>
                <td data-label="Kode">1.MS.03</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Konsultasi Hukum Bisnis</td>
                <td data-label="Kode">1.MS.04</td>
              </tr>

              <tr class="row-category">
                <td data-label="No">5 </td>
                <td data-label="Sub Layanan">JASA KONSULTANSI DESTINASI PARIWISATA</td>
                <td data-label="Kode">4.01</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Pemberdayaan Masyarakat</td>
                <td data-label="Kode">4.01.01</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Pembangunan Prasarana</td>
                <td data-label="Kode">4.01.03</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Penyediaan  &  Pembangunan  Fasilitas / Sarana Pariwisata</td>
                <td data-label="Kode">4.01.04</td>
              </tr>

              <tr class="row-category">
                <td data-label="No">6</td>
                <td data-label="Sub Layanan">JASA KONSULTASI PERENCANAAN KEPARIWISATAAN</td>
                <td data-label="Kode">4.SR</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Perencanaan Umum & Konsultansi Pembangunan/ Pengembangan</td>
                <td data-label="Kode">4.SR.01</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Rancang Bangun dan  Bantuan Teknik</td>
                <td data-label="Kode">4.SR.02</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Perencanaan Sistem Akuntansi dan Keuangan</td>
                <td data-label="Kode">4.SR.03</td>
              </tr>
               <tr>
                <td>&nbsp;</td>
                <td data-label="Detail">Jasa Perencanaan Informasi Teknologi</td>
                <td data-label="Kode">4.SR.04</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section id="tenaga-ahli" class="tenaga-ahli section">
       <section id="sbu-non-kon" class="sbu section" style="padding-top: 0;">
      <div class="container" data-aos="fade-up">
        <div class="sbu-poster-container">
          <div class="sbu-title">
            <h2>DAFTAR TENAGA AHLI</h2>
            <p>CV. CITRA ARSITAMA</p>
          </div>
      <table class="responsive-blue-table">
        <thead>
          <tr>
            <th style="width: 5%;">NO</th>
            <th style="width: 20%;">NAMA</th>
            <th style="width: 35%;">PENDIDIKAN</th>
            <th style="width: 15%;">TAHUN LULUS</th>
            <th style="width: 25%;">KEAHLIAN</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td data-label="No">1</td>
            <td data-label="Nama"><strong>Rohman Eko Santoso, ST. M.Ars</strong></td>
            <td data-label="Pendidikan">S1 Arsitektur Universitas Komputer Indonesia <br> S2 Arsitektur Universitas Diponegoro</td>
            <td data-label="Tahun Lulus">2005 <br> 2020</td>
            <td data-label="Keahlian">Arsitektur</td>
          </tr>
          <tr>
            <td data-label="No">2</td>
            <td data-label="Nama"><strong>Sus Andjasmara Kurmani, ST.</strong></td>
            <td data-label="Pendidikan">S1 Arsitektur Universitas Katolik Soegijapranata</td>
            <td data-label="Tahun Lulus">1995</td>
            <td data-label="Keahlian">Arsitektur</td>
          </tr>
          <tr>
            <td data-label="No">3</td>
            <td data-label="Nama"><strong>Ronny AB Wardhana, ST, MT</strong></td>
            <td data-label="Pendidikan">S1 Arsitektur Universitas Katolik Soegijapranata <br> S2 Arsitektur Universitas Diponegoro</td>
            <td data-label="Tahun Lulus">1995 <br> 1999</td>
            <td data-label="Keahlian">Arsitektur</td>
          </tr>
          <tr>
            <td data-label="No">4</td>
            <td data-label="Nama"><strong>Ktut Christy S, ST.</strong></td>
            <td data-label="Pendidikan">S1 Arsitektur Universitas Diponegoro</td>
            <td data-label="Tahun Lulus">2008</td>
            <td data-label="Keahlian">Arsitektur</td>
          </tr>
          <tr>
            <td data-label="No">5</td>
            <td data-label="Nama"><strong>Anjar Saptoyogo, ST.</strong></td>
            <td data-label="Pendidikan">S1 Arsitektur Universitas Negeri Semarang</td>
            <td data-label="Tahun Lulus">2008</td>
            <td data-label="Keahlian">Arsitektur</td>
          </tr>
          <tr>
            <td data-label="No">6</td>
            <td data-label="Nama"><strong>Ria Ripardi Wahyu Lestari, S.Ars.</strong></td>
            <td data-label="Pendidikan">S1 Arsitektur Universitas Katolik Soegijapranata</td>
            <td data-label="Tahun Lulus">2016</td>
            <td data-label="Keahlian">Arsitektur</td>
          </tr>
          <tr>
            <td data-label="No">7</td>
            <td data-label="Nama"><strong>Bagas Harda Prasetyo, S.Ars.</strong></td>
            <td data-label="Pendidikan">S1 Arsitektur Universitas Katolik Soegijapranata</td>
            <td data-label="Tahun Lulus">2017</td>
            <td data-label="Keahlian">Arsitektur</td>
          </tr>
          <tr>
            <td data-label="No">8</td>
            <td data-label="Nama"><strong>Dwi Guntoro, ST, MT</strong></td>
            <td data-label="Pendidikan">S1 Sipil Universitas Semarang <br> S2 MTPK Universitas Diponegoro</td>
            <td data-label="Tahun Lulus">1999 <br> 2004</td>
            <td data-label="Keahlian">Sipil</td>
          </tr>
          <tr>
            <td data-label="No">9</td>
            <td data-label="Nama"><strong>Muhammadun, ST, MT</strong></td>
            <td data-label="Pendidikan">S1 Sipil Universitas Sultan Agung <br> S2 Sipil Universitas Sultan Agung</td>
            <td data-label="Tahun Lulus">1994 <br> 2009</td>
            <td data-label="Keahlian">Sipil</td>
          </tr>
          <tr>
            <td data-label="No">10</td>
            <td data-label="Nama"><strong>Sudiyoko, ST</strong></td>
            <td data-label="Pendidikan">S1 Sipil Universitas 17 Agustus 1945</td>
            <td data-label="Tahun Lulus">1998</td>
            <td data-label="Keahlian">Sipil</td>
          </tr>
          <tr>
            <td data-label="No">11</td>
            <td data-label="Nama"><strong>Wahyu Bagus H, ST</strong></td>
            <td data-label="Pendidikan">S1 Sipil Universitas Diponegoro</td>
            <td data-label="Tahun Lulus">1998</td>
            <td data-label="Keahlian">Sipil</td>
          </tr>
          <tr>
            <td data-label="No">12</td>
            <td data-label="Nama"><strong>Mela Fitri Astuti, ST.</strong></td>
            <td data-label="Pendidikan">S1 Sipil Universitas 17 Agustus 1945</td>
            <td data-label="Tahun Lulus">2017</td>
            <td data-label="Keahlian">Sipil</td>
          </tr>
          <tr>
            <td data-label="No">13</td>
            <td data-label="Nama"><strong>Bayu Pradana, ST, MT</strong></td>
            <td data-label="Pendidikan">S1 T. Elektro Universitas Semarang <br> S2 M. Elektro Universitas Diponegoro</td>
            <td data-label="Tahun Lulus">2017 <br> 2021</td>
            <td data-label="Keahlian">Mechanical Electrical Plumbing</td>
          </tr>
          <tr>
            <td data-label="No">14</td>
            <td data-label="Nama"><strong>Sigit Nurmansyah, S.Si</strong></td>
            <td data-label="Pendidikan">S1 Elektronika & Instrumensi Universitas Gadjah Mada</td>
            <td data-label="Tahun Lulus">2012</td>
            <td data-label="Keahlian">Mechanical Electrical Plumbing</td>
          </tr>
          <tr>
            <td data-label="No">15</td>
            <td data-label="Nama"><strong>Fajar Nugroho, ST.</strong></td>
            <td data-label="Pendidikan">S1 Planologi Universitas Sultan Agung</td>
            <td data-label="Tahun Lulus">2017</td>
            <td data-label="Keahlian">Planologi</td>
          </tr>
          <tr>
            <td data-label="No">16</td>
            <td data-label="Nama"><strong>Farida Hidayah, S.PWK</strong></td>
            <td data-label="Pendidikan">S1 Planologi Universitas Sultan Agung</td>
            <td data-label="Tahun Lulus">2018</td>
            <td data-label="Keahlian">Planologi</td>
          </tr>
          <tr>
            <td data-label="No">17</td>
            <td data-label="Nama"><strong>Rachmanesvi Ulfa, S.PWK</strong></td>
            <td data-label="Pendidikan">S1 Planologi Universitas Sultan Agung</td>
            <td data-label="Tahun Lulus">2018</td>
            <td data-label="Keahlian">Planologi</td>
          </tr>
          <tr>
            <td data-label="No">18</td>
            <td data-label="Nama"><strong>Febri Dwi Astuti, S.PWK</strong></td>
            <td data-label="Pendidikan">S1 Planologi Universitas Sultan Agung</td>
            <td data-label="Tahun Lulus">2019</td>
            <td data-label="Keahlian">Planologi</td>
          </tr>
           <tr>
            <td data-label="No">19</td>
            <td data-label="Nama"><strong>Puput Fitria Setyawati, Amd.</strong></td>
            <td data-label="Pendidikan">D3 Ilmu Komunikasi Universitas Diponegoro</td>
            <td data-label="Tahun Lulus">2005</td>
            <td data-label="Keahlian">Pemberdayaan</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

  </main>

<footer class="footer-simple py-4" style="background-color: #f8f8f8; border-top: 1px solid #eee;">
  <div class="container position-relative">

    <p class="mb-2 text-center" style="color: #333;">
      Copyright © 2026 Citra Arsitama. All right reserved.
    </p>

    <div class="footer-social text-center mb-3"
         style="display: flex; justify-content: center; gap: 14px; font-size: 1.25rem;">
      <a href="#"><i class="bi bi-facebook"></i></a>
      <a href="https://www.instagram.com/studio_citraarsitama"><i class="bi bi-instagram"></i></a>
      <a href="#"><i class="bi bi-linkedin"></i></a>
      <a href="#"><i class="bi bi-youtube"></i></a>
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