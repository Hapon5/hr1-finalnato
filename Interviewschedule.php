<?php
session_start();
if (!isset($_SESSION['Email']) || $_SESSION['Account_type'] !== '1') {
    header("Location: login.php");
    exit();
}
$admin_email = $_SESSION['Email'];
echo " ";

echo ' ';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HR1</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
     <link rel="stylesheet" href="../css/interviewschedule.css">


  </head>
  <link rel="stylesheet" href="Dashboard.css" />
  <body>
    <nav class="sidebar">
            <h2 id="h2">HR1</h2>
      <div class="menu-content">
        <ul class="menu-items">
          <div class="menu-title">
                <i class="fa-solid fa-chevron-left"></i>
                Back
              </div>
            
            <ul class="menu-items submenu">
              <div class="menu-title">
              <i class='bx bx-arrow-back'></i>Back</div>
              <li class="item1">
                <a>EMPLOYEE MANAGEMENT</a>
                <div class="dropdown-content">
                  <a href="employee_database.php"><small>EMPLOYEE DATABASE</small></a>
                  <a href="performance_and_appraisals.php"><small>PERFORMANCE & APPRAISALS</small></a>
                </div>
              </li>
              <li class="item1">
                <a>RECRUITMENT</a>
                <div class="dropdown-content">
                <a href="job_posting.php"><small>JOB POSTING</small></a>
                <a href="candidate_sourcing_&_tracking.php"><small>CANDIDATE SOURCING & TRACKING</small></a>
                <a href="interview_scheduling.php"><small>INTERVIEW SCHEDULING</small></a>
                <a href="assessment_&_screening.php"><small>ASSESSMENT & SCREENING</small></a>
                </div>
                </li>
              <li class="item1">
                <a>APPLICANT MANAGEMENT</a>
                <div class="dropdown-content">
                  <a href="#"><small>RESUME PARSING & STORAGE</small></a>
                  <a href="#"><small>COMMUNICATION & NOTIFICATIONS</small></a>
                  <a href="#"><small>DOCUMENT MANAGEMENT</small></a>
                </div>
              </li>
              <li class="item1">
                <a>NEW HIRED ONBOARDING SYSTEM</a>
                <div class="dropdown-content">
                  <a href="#"><small>DIGITAL ONBOARDING PROCESS</small></a>
                  <a href="#"><small>WELCOME KIT & ORIENTATION</small></a>
                  <a href="#"><small>USER ACCOUNT & SETUP</small></a>
                </div>
              </li>
              <li class="item1">
                <a>RECRUITING ANALYTIC & REPORTING</a>
                <div class="dropdown-content">
                  <a href="#"><small>HIRING METRICS DASHBOARD</small></a>
                  <a href="#"><small>RECRUITMENT FUNNEL & ANALYSIS</small></a>
                  <a href="#"><small>RECRUITER PERFORMANCE TRACKING</small></a>
                  <a href="#"><small>DIVERSITY & COMPLIANCE REPORT</small></a>
                  <a href="#"><small>COST & BUDGET ANALYSIS</small></a>
                </div>
              </li>
            </ul>
          </li>
            <li class="item">
              <span class="icon"><i class="fa-solid fa-gear"></i></span>
                <a href="aboutus.html" class="nav_link">About Us</a>
            </li>
            <li class="item" id="logout-link">
              <span class="icon"><i class='bx bx-log-out'></i></span>
              <a href="#">Logout</a>
            </li>
        </ul>
      </div>
    </nav>
    <nav class="navbar">
      <i class="fa-solid fa-bars" id="sidebar-close"></i>
    </nav>
    <main class="main">
    <form action="submit_applicant.php" method="POST" enctype="multipart/form-data" class="applicant-form">
   <h2 style="text-align: center;">Interview Scheduling</h2>
  <div class="form-row">
    <div class="form-group">
      <label for="name">Full Name:</label>
      <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
      <label for="job">Start Time:</label>
      <input type="text" id="job" name="job" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="position">End Time:</label>
      <input type="text" id="position" name="position" required>
    </div>

    <div class="form-group">
      <label for="experience">Applicant Number:</label>
      <input type="number" id="experience" name="experience" required min="0">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="age">Position:</label>
      <input type="number" id="age" name="age" required min="18">
    </div>

    <div class="form-group">
      <label for="contact">Interviewer:</label>
      <input type="tel" id="contact" name="contact" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
      <label for="address">Address:</label>
      <input type="address" id="address" name="address" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group" style="width: 100%;">
      <label for="resume">Upload Police/NBI Clearance:</label>
      <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
    </div>
  </div>

  <button type="submit">Submit</button>
</form>

</main>
        <script src="Dashboard.js"></script>
  </body>
</html>