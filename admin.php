<?php
session_start();
// require_once "Connections.php"; // This should be uncommented on your live server

// Make sure session exists
if (!isset($_SESSION['Email']) || !isset($_SESSION['Account_type'])) {
    // header("Location: login.php");
    // exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HR Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        :root {
            --primary-color: #d37a15;
            --secondary-color: #0a0a0a;
            --background-light: #f8f9fa;
            --background-card: #ffffff;
            --text-dark: #333;
            --text-light: #f4f4f4;
            --shadow-subtle: 0 4px 12px rgba(0, 0, 0, 0.08);
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: var(--background-light);
            display: flex;
            min-height: 100vh;
            color: var(--text-dark);
        }
        
        /* --- Sidebar Styles --- */
        .sidebar {
            width: 260px;
            background-color: var(--primary-color);
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 100;
        }
        .sidebar.close { width: 78px; }
        .sidebar-header {
            display: flex;
            align-items: center;
            color: var(--text-light);
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-left: 10px;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }
        .sidebar.close .sidebar-header h2 { opacity: 0; pointer-events: none; }
        .sidebar-nav { list-style: none; flex-grow: 1; padding-top: 20px; }
        .sidebar-nav li { margin-bottom: 10px; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-light);
            background-color: transparent;
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }
        .sidebar-nav a:hover { background-color: rgba(255, 255, 255, 0.2); }
        .sidebar-nav a i {
            font-size: 20px;
            margin-right: 15px;
            min-width: 20px;
            text-align: center;
        }
        .sidebar.close .sidebar-nav span { opacity: 0; pointer-events: none; }

        /* --- Main Content (INAYOS) --- */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 20px 30px;
            transition: margin-left 0.3s ease;
            width: calc(100% - 260px); /* Para sakupin ang buong screen */
        }
        .sidebar.close ~ .main-content { margin-left: 78px; width: calc(100% - 78px); }
        
        .top-navbar {
            display: flex;
            justify-content: space-between; /* Para maghiwalay ang menu at oras */
            align-items: center;
            margin-bottom: 20px;
        }
        .menu-toggle {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--secondary-color);
        }
        .datetime-display {
            font-size: 1rem;
            font-weight: 500;
            color: #555;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }

        /* --- Chart Section --- */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        .chart-container {
            background-color: var(--background-card);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            height: 380px; 
        }
        .chart-container h3 {
            text-align: center;
            margin-bottom: 15px;
            color: var(--primary-color);
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .chart-wrapper {
            position: relative;
            flex-grow: 1;
            width: 100%;
        }

        /* --- Media Queries --- */
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; width: 100%; }
            .sidebar.close ~ .main-content { margin-left: 0; }
            .dashboard-grid { grid-template-columns: 1fr; }
            .datetime-display { display: none; } /* Itago ang oras sa maliliit na screen */
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <i class='bx bxs-user-detail' style='font-size: 2rem; color: #fff;'></i>
            <h2>HR Admin</h2>
        </div>
        <ul class="sidebar-nav">
             <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="modules/job_posting.php"><i class="fas fa-bullhorn"></i><span>Job Posting</span></a></li>
            <li><a href="candidate_sourcing_&_tracking.php"><i class="fas fa-users"></i><span>Candidates</span></a></li>
            <li><a href="Interviewschedule.php"><i class="fas fa-calendar-alt"></i><span>Interviews</span></a></li>
            <li><a href="modules/performance_and_appraisals.php"><i class="fas fa-user-check"></i><span>Performance</span></a></li>
            <li><a href="modules/recognition.php"><i class="fas fa-star"></i><span>Recognition</span></a></li>
            <li><a href="modules/learning.php"><i class="fas fa-shield-alt"></i><span>Safety</span></a></li>
            <li><a href="aboutus.php"><i class="fas fa-info-circle"></i><span>About Us</span></a></li>
            <li><a href="logout.php" id="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="top-navbar">
            <i class="fa-solid fa-bars menu-toggle"></i>
            <!-- DITO ILALAGAY ANG DATE AND TIME -->
            <div id="datetime" class="datetime-display"></div>
        </div>
        <header class="dashboard-header">
            <h1>HR Dashboard</h1>
        </header>

        <div id="chart-section" class="dashboard-grid">
            <div class="chart-container">
                <h3>Total Applicants Over Time</h3>
                <div class="chart-wrapper">
                    <canvas id="totalApplicantsChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <h3>New Hires by Month</h3>
                <div class="chart-wrapper">
                    <canvas id="newHiresChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <h3>Applicant Source Breakdown</h3>
                <div class="chart-wrapper">
                    <canvas id="applicantSourceChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <h3>Employees by Department</h3>
                <div class="chart-wrapper">
                    <canvas id="hiringByDeptChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data for charts
            const newHiresData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{ 
                    label: 'New Hires', 
                    data: [5, 8, 7, 10, 6, 9], 
                    backgroundColor: '#0a0a0a', 
                    borderRadius: 6,
                    barPercentage: 0.6, // Inayos para lumaki ang bar
                    categoryPercentage: 0.7
                }]
            };
            const hiringByDeptData = {
                labels: ['IT', 'Sales', 'Marketing', 'HR', 'Finance'],
                datasets: [{ 
                    label: 'Employees', 
                    data: [35, 42, 28, 15, 20], 
                    backgroundColor: '#d37a15', 
                    borderRadius: 6,
                    barPercentage: 0.6, // Inayos para lumaki ang bar
                    categoryPercentage: 0.7
                }]
            };

            // Other chart data and options...
            const totalApplicantsData = { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], datasets: [{ label: 'Total Applicants', data: [85, 92, 110, 135, 148, 160], borderColor: '#d37a15', tension: 0.3 }]};
            const applicantSourceData = { labels: ['LinkedIn', 'Website', 'Referral', 'Job Fair', 'Other'], datasets: [{ label: 'Source', data: [45, 30, 20, 5, 10], backgroundColor: ['#d37a15', '#0a0a0a', '#b06511', '#888', '#555'] }]};
            const commonOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }};
            const pieOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }};

            // Render Charts
            new Chart(document.getElementById('totalApplicantsChart'), { type: 'line', data: totalApplicantsData, options: commonOptions });
            new Chart(document.getElementById('newHiresChart'), { type: 'bar', data: newHiresData, options: commonOptions });
            new Chart(document.getElementById('applicantSourceChart'), { type: 'pie', data: applicantSourceData, options: pieOptions });
            new Chart(document.getElementById('hiringByDeptChart'), { type: 'bar', data: hiringByDeptData, options: commonOptions });

            // --- BAGONG SCRIPT PARA SA DATE AND TIME ---
            const datetimeElement = document.getElementById('datetime');
            function updateDateTime() {
                const now = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                datetimeElement.textContent = now.toLocaleString('en-US', options);
            }
            updateDateTime(); // Tawagin agad para hindi blanko sa simula
            setInterval(updateDateTime, 1000); // I-update bawat segundo

            // Sidebar and Logout Logic
            const sidebar = document.querySelector(".sidebar");
            const menuToggle = document.querySelector(".menu-toggle");
            if (menuToggle) {
                menuToggle.addEventListener("click", () => sidebar.classList.toggle("close"));
            }
        });
    </script>
</body>
</html>

