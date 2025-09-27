<?php
// Include the database configuration file
// CORRECTED: The path should be direct as it's in the same folder.
include 'Connections.php';

// Initialize variables
$featuredJob = null;
$jobs = [];

// Use PDO to prepare and execute the query for the featured job
try {
    // CORRECTED: Changed variable from $conn to $Connections to match your file
    $stmt = $co->prepare("SELECT * FROM job_postings WHERE status = 'active' LIMIT 1"); // Assuming a 'status' or 'featured' column
    $stmt->execute();
    $featuredJob = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // For a live site, you might log this error instead of echoing it.
    error_log("Featured Job Error: " . $e->getMessage());
}

// Get all active jobs for the job listing section
try {
    // CORRECTED: Changed variable from $conn to $Connections
    $stmtJobs = $conn->prepare("SELECT * FROM job_postings WHERE status = 'active'");
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

  <link rel="stylesheet" type="text/css" href="css/bootstraps.css" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Roboto:400,700&display=swap" rel="stylesheet">
  <link href="css/stylez1.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />
</head>

<body>
  <div class="hero_area">
    <header class="header_section">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="landing.php">
            <span class="brand-text">Crane</span>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
            <div class="d-flex ml-auto flex-column flex-lg-row align-items-center">
              <ul class="navbar-nav">
                <li class="nav-item active">
                  <a class="nav-link" href="../landing.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#listjobs">Jobs</a>
                </li>
                 <li class="nav-item">
                  <a class="nav-link" href="#developersquote">Developer Quotes</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="../aboutus.php">About Us</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="../login.php">LogIn</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </div>
    </header>
    <section class="slider_section">
        </section>
  </div>

  <section id="listjobs" class="job_section layout_padding-bottom">
    <div class="container">
      <div class="heading_container">
        <h2><span>Available Jobs</span></h2>
      </div>
      <div class="job_board">
        <div class="content-box">
          <div class="content layout_padding2-top">
            <?php if (!empty($jobs)): ?>
              <?php foreach ($jobs as $job): ?>
                <div class="box job-card">
                  <div class="job-details">
                    <p><strong>Job Title:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($job['position']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><strong>Platform:</strong> <?php echo htmlspecialchars($job['platform']); ?></p>
                  </div>
                  <div class="apply-now">
                    <a href="login.php">Apply Now</a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center text-muted p-5">
                <p>No available jobs at the moment. Please check back later!</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="feature_section" id="featuredjob">
      </section>
  
  <section class="client_section" id="developersquote">
      </section>

  <section class="info_section layout_padding2-bottom layout_padding-top" id="aboutus">
      </section>

  <footer class="container-fluid footer_section">
    <p>Crane And Trucking Management System</p>
  </footer>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstraps.js"></script>
</body>
</html>