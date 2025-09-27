<?php
// Correct path to Connections.php, assuming it's in the same root directory.
require_once 'Connections.php'; 

// Initialize variables
$featuredJob = null;
$jobs = [];

// Use the correct table `job_postings` and variable `$conn`
try {
    // Select the latest active job to be "featured"
    $stmt = $conn->prepare("SELECT * FROM job_postings WHERE status = 'active' ORDER BY date_posted DESC LIMIT 1");
    $stmt->execute();
    $featuredJob = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // For a live site, you should log this error instead of showing it to the user.
    error_log("Featured Job Error: " . $e->getMessage());
}

try {
    // Select all active jobs for the main list
    $stmtJobs = $conn->prepare("SELECT * FROM job_postings WHERE status = 'active' ORDER BY date_posted DESC");
    $stmtJobs->execute();
    $jobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("All Jobs Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="icon" href="images/logo.png" type="image/png">
  <title>Crane</title>

  <!-- Stylesheets -->
  <link rel="stylesheet" type="text/css" href="css/bootstraps.css" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Roboto:400,700&display=swap" rel="stylesheet">
  <link href="css/stylez1.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />
  <style>
    /* Additional styles for a cleaner look inspired by the video */
    .job-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border: 1px solid #eee;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }
    .job-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    .job-details p {
        margin: 0;
        line-height: 1.6;
        color: #555;
    }
    .apply-now a {
        background-color: #d37a15;
        color: white;
        padding: 10px 25px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }
    .apply-now a:hover {
        background-color: #b8650f;
    }
  </style>
</head>

<body>
  <div class="hero_area">
    <!-- header section -->
    <header class="header_section">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="landing.php">
            <span class="brand-text">Crane</span>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
            <div class="d-flex ml-auto flex-column flex-lg-row align-items-center">
              <ul class="navbar-nav">
                <li class="nav-item active">
                  <a class="nav-link" href="landing.php">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#listjobs">Jobs</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#developersquote">Developer Quotes</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="aboutus.php">About Us</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="../hr1/login.php">LogIn</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->

    <!-- slider section -->
    <section class="slider_section">
        <div class="container-fluid">
            <div class="row">
            <div class="col-md-4 offset-md-1">
                <div class="detail-box">
                <h1>
                    Find a <br>
                    Perfect job <br>
                    for you
                </h1>
                <div>
                    <a href="#listjobs">
                    Read More
                    </a>
                </div>
                </div>
            </div>
            <div class="col-md-4 ">
                <div class="img-box">
                <img src="images/firstpic.png" alt="Find a job">
                </div>
            </div>
            </div>
        </div>
    </section>
  </div>

  <!-- job section -->
  <section id="listjobs" class="job_section layout_padding-bottom">
    <div class="container">
      <div class="heading_container">
        <h2><span>Available Jobs</span></h2>
      </div>
      <div class="job_board">
        <div class="content-box">
          <div class="content layout_padding2-top">
            <!-- JOB LISTING PHP BLOCK (FIXED) -->
            <?php if (!empty($jobs)): ?>
              <?php foreach ($jobs as $job): ?>
                <div class="box job-card">
                  <div class="job-details">
                    <p><strong>Job Title:</strong> <?= htmlspecialchars($job['title']) ?></p>
                    <p><strong>Position:</strong> <?= htmlspecialchars($job['position']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                  </div>
                  <div class="apply-now">
                    <a href="login.php">Apply Now</a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="box text-center p-5 text-muted">
                <p>No available jobs at the moment. Please check back later!</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end job section -->

  <!-- feature section -->
  <section class="feature_section" id="featuredjob">
    <div class="container-fluid">
      <div class="row">
        <?php if ($featuredJob): ?>
          <div class="col-md-5 offset-md-1">
            <div class="detail-box">
              <h2>Featured Job</h2>
              <h5><strong><?= htmlspecialchars($featuredJob['title']); ?></strong></h5>
              <p><strong>Position:</strong> <?= htmlspecialchars($featuredJob['position']); ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($featuredJob['location']); ?></p>
              <a href="login.php" class="mt-3">
                Read More
              </a>
            </div>
          </div>
          <div class="col-md-6 px-0">
            <div class="img-box">
              <img src="images/secondpic.png" alt="Featured job">
            </div>
          </div>
        <?php else: ?>
          <div class="col-12 text-center p-5">
            <p class="text-muted">No featured job at the moment.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <!-- end feature section -->

  <!-- client/quotes section -->
  <section class="client_section" id="developersquote">
    <div class="container layout_padding">
      <div class="heading_container">
        <h2>Developer's Quotes</h2>
      </div>
      <div class="box">
        <p class="text-center" style="font-size: 1.2rem; font-style: italic; color: #555;">
            "A website is not just a digital presence, it's your brand's voice, 
            identity, and experience brought to life through code. As developers, 
            we don’t just build pages—we craft journeys that users can trust, enjoy, and remember."
        </p>
      </div>
    </div>
  </section>
  <!-- end client section -->

  <!-- info section -->
  <section class="info_section layout_padding2-bottom layout_padding-top" id="aboutus">
    <div class="container info_content">
      <div>
        <div class="row">
          <div class="col-md-4">
            <h5>About Us</h5>
            <p>We are a dedicated team committed to connecting talent with opportunity. Our platform streamlines the hiring process for both companies and job seekers.</p>
          </div>
          <div class="col-md-4">
            <h5>Contact Us</h5>
            <p>Email: contact@cranehr.com</p>
            <p>Phone: +63 2 8888 7777</p>
          </div>
          <div class="col-md-4">
            <h5>Follow Us</h5>
            <div class="social-box">
                <a href="#"><img src="images/fb.png" alt="Facebook" /></a>
                <a href="#"><img src="images/twitter.png" alt="Twitter" /></a>
                <a href="#"><img src="images/linkedin.png" alt="LinkedIn" /></a>
                <a href="#"><img src="images/insta.png" alt="Instagram" /></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end info_section -->

  <!-- footer section -->
  <footer class="container-fluid footer_section">
    <p>Crane And Trucking Management System</p>
  </footer>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstraps.js"></script>
</body>
</html>
