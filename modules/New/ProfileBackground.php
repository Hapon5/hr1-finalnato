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
<style>
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}
.sidebar {
    position: fixed;
    height: 100%;
    width: 260px;
    background: #d37a15ff;
    padding: 15px;
    z-index: 99;
}
#h2 {
    color: white;
    margin-top: 20px;
    margin-right: 5px;
    margin-left: 10px;
    display: flex;
    align-items: center;
}
.sidebar a {
    color: #fff;
    text-decoration: none;
}
.menu-content {
    position: relative;
    height: 80%;
    width: 100%;
    margin-top: 40px;
    overflow-y: scroll;
}
.menu-content::-webkit-scrollbar {
    display: none;
}
.menu-items {
    height: 100%;
    width: 100%;
    list-style: none;
    transition: all 0.4s ease;
}
.submenu-active .menu-items {
    transform: translateX(-56%);
}
.menu-title {
    color: whitesmoke;
    font-size: 18px;
    padding: 15px 20px;
}
.item a,
.submenu-item {
    padding: 20px;
    display: inline-block;
    width: 100%;
    border-radius: 12px;
}
.item i {
    font-size: 12px;
}
.item {
    display: flex;
    align-items: center;
}
.icon {
    color: white;
}
.icon i {
    font-size: 24px;
    margin-right: 10px;
}
.item a:hover,
.submenu-item:hover,
.submenu .menu-title:hover {
    background: rgba(255, 255, 255, 0.1);
}
.submenu-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
    cursor: pointer;
}
.submenu {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: calc(-100% - 26px);
    height: calc(100% + 100vh);
    background: #080808ff;
    display: none;
}
.show-submenu ~ .submenu {
    display: block;
}
.submenu .menu-title {
    border-radius: 12px;
    cursor: pointer;
}
.submenu .menu-title i {
    margin-right: 10px;
}
.navbar,
.main {
    left: 260px;
    width: calc(100% - 260px);
    transition: all 0.5s ease;
    z-index: 1000;
}
.sidebar.close ~ .navbar,
.sidebar.close ~ .main {
    left: 0;
    width: 100%;
}
.navbar {
    position: fixed;
    color: #fff;
    padding: 15px 20px;
    font-size: 25px;
    background: #080808ff;
    cursor: pointer;
}
.navbar #sidebar-close {
    cursor: pointer;
}
.main {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    z-index: 100;
    background: #e7f2fd;
}
.main h1 {
    color: #11101d;
    font-size: 40px;
    text-align: center;
    margin-top: 20px;
}
.dropdown-content {
    background-color: #d37a15ff;
}
.main {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    display: grid;
    display: flex;
    justify-content: space-between;
    padding-top: 50px;
}

.applicant-form label {
  font-weight: 500;
  margin-bottom: 5px;
}

.applicant-form input[type="text"],
.applicant-form input[type="email"],
.applicant-form input[type="number"],
.applicant-form input[type="tel"],
.applicant-form input[type="file"],
.applicant-form textarea {
  padding: 10px;
  width: 100%;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.applicant-form textarea {
  resize: vertical;
  height: 80px;
}

.applicant-form button {
  padding: 12px;
  background-color: #d37a15ff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
}

.applicant-form button:hover {
  background-color: #d37a15ff;
}
.applicant-form {
  background: #fff;
  padding: 30px;
  border-radius: 8px;
  width: 80%;
  max-width: 800px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin: 20px auto;
  margin-top: 30px;
}

.form-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 500;
  margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 100%;
}

.form-group textarea {
  resize: vertical;
  height: 80px;
}

button[type="submit"] {
  padding: 9px;
  background-color: #d37a15ff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  width: fit-content;
  align-self: flex-end;
}

button[type="submit"]:hover {
  background-color: #d37a15ff;
}


</style>

  </head>
  <link rel="stylesheet" href="Dashboard.css" />
  <body>
    <nav class="sidebar">
            <h2 id="h2">HR1</h2>
      <div class="menu-content">
        <ul class="menu-items">
            <div class="menu-title"><small>ADMIN DASHBOARD</small></div>
              <li class="item">
              <span class="icon"><i class='bx bxs-dashboard'></i></span>
              <a class="nav_link">Dashboard</a>
            </li>
          <li class="item">
          <span class="icon"><i class='bx bx-group'></i></span>
            <div class="submenu-item">
              <span>HR 1</span>
              <i class="fa-solid fa-chevron-right"></i>
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
   <h2 style="text-align: center;">Profile Background</h2>
  <div class="form-row">
    <div class="form-group">
      <label for="name">Full Name:</label>
      <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
      <label for="department">Department:</label>
      <input type="text" id="department" name="department" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="position">Position:</label>
      <input type="text" id="position" name="position" required>
    </div>

    <div class="form-group">
      <label for="experience">Job Experience:</label>
      <input type="number" id="experience" name="experience" required min="0">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="age">Age:</label>
      <input type="number" id="age" name="age" required min="18">
    </div>

    <div class="form-group">
      <label for="contact">Contact Number:</label>
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
      <label for="resume">Upload Picture:</label>
      <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
    </div>
  </div>

  <button type="submit">Submit</button>
</form>

</main>
        <script src="Dashboard.js"></script>
  </body>
</html>