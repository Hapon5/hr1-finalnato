<?php
session_start();
require_once "Connections.php"; // gives you $Connections (PDO object)

// Make sure session exists
if (!isset($_SESSION['Email']) || !isset($_SESSION['Account_type'])) {
    header("Location: login.php");
    exit();
}

$admin_email = $_SESSION['Email'];
$account_type = $_SESSION['Account_type'];

// Optional: re-check in DB
$stmt = $Connections->prepare("SELECT Account_type FROM logintbl WHERE Email = :email LIMIT 1");
$stmt->execute(['email' => $admin_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['Account_type'] !== '1') {
    // Not admin → redirect
    header("Location: login.php");
    exit();
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
            --background-light: #e7f2fd;
            --background-card: #ffffff;
            --text-dark: #333;
            --text-light: #f4f4f4;
            --shadow-subtle: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
        }
        .sidebar.close {
            width: 78px;
        }
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
        }
        .sidebar.close .sidebar-header h2 {
            opacity: 0;
            pointer-events: none;
        }
        .sidebar-nav {
            list-style: none;
            flex-grow: 1;
            padding-top: 20px;
        }
        .sidebar-nav li {
            margin-bottom: 10px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-dark);
            background-color: var(--background-card);
            transition: background-color 0.3s ease;
        }
        .sidebar-nav a:hover {
            background-color: rgba(255, 255, 255, 0.8);
        }
        .sidebar-nav a i {
            font-size: 20px;
            margin-right: 15px;
            min-width: 20px;
            text-align: center;
            transition: margin 0.3s ease;
        }
        .sidebar.close .sidebar-nav a i {
            margin-right: 0;
        }
        .sidebar-nav a span {
            transition: opacity 0.3s ease;
        }
        .sidebar.close .sidebar-nav a span {
            opacity: 0;
            pointer-events: none;
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: 260px; /* Offset to clear the fixed sidebar */
            flex-grow: 1;
            padding: 20px 30px;
            transition: margin-left 0.3s ease;
        }
        .sidebar.close ~ .main-content {
            margin-left: 78px;
        }
        .top-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            margin-bottom: 20px;
        }
        .menu-toggle {
            font-size: 2rem;
            cursor: pointer;
            color: var(--secondary-color);
            transition: transform 0.3s ease;
        }
        .menu-toggle:hover {
            transform: scale(1.1);
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
            color: var(--secondary-color);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        .chart-container {
            background-color: var(--background-card);
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow-subtle);
        }
        .chart-container h3 {
            text-align: center;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        /* --- Loading Spinner --- */
        .loading-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: var(--text-dark);
            z-index: 10;
        }
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* --- Media Queries for Responsiveness --- */
        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 15px;
            }
            .sidebar-nav {
                display: none;
            }
            .sidebar-header {
                border-bottom: none;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
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
            <li><a href="./modules/job_posting.php"><i class="fas fa-bullhorn"></i><span>Job Posting</span></a></li>
            <li><a href="./modules/candidate_sourcing_&_tracking.php"><i class="fas fa-users"></i><span>Candidates</span></a></li>
            <li><a href="./modules/Interviewschedule.php"><i class="fas fa-calendar-alt"></i><span>Interviews</span></a></li>
                   <li><a href="./modules/performance_and_appraisals.php"><i class="fas fa-user"></i><span>Performance Management</span></a></li>
                          <li><a href="./modules/recognition.php"><i class="fas fa-star"></i><span>Social Recognition</span></a></li>
                   <li><a href="./modules/learning.php"><i class="fas fa-envelope"></i><span>Compliance and Safety</span></a></li>
                                      <li><a href="aboutus.php"><i class="fas fa-search"></i><span>About Us</span></a></li>



            <li><a href="#" id="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="top-navbar">
            <i class="fa-solid fa-bars menu-toggle"></i>
        </div>
        <header class="dashboard-header">
            <h1>HR Dashboard</h1>
        </header>

        <div id="loading-spinner" class="loading-container">
            <div class="spinner"></div>
            <p>Loading charts...</p>
        </div>

        <div id="chart-section" class="dashboard-grid" style="display: none;">
            <div class="chart-container">
                <h3>Total Applicants Over Time</h3>
                <canvas id="totalApplicantsChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h3>New Hires by Month</h3>
                <canvas id="newHiresChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h3>Applicant Source Breakdown</h3>
                <canvas id="applicantSourceChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h3>Employees by Department</h3>
                <canvas id="hiringByDeptChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data for charts
            const totalApplicantsData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Total Applicants',
                    data: [85, 92, 110, 135, 148, 160],
                    fill: false,
                    borderColor: '#d37a15',
                    tension: 0.3,
                }]
            };

            const newHiresData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Hires',
                    data: [5, 8, 7, 10, 6, 9],
                    backgroundColor: '#0a0a0a',
                }]
            };
            
            const applicantSourceData = {
                labels: ['LinkedIn', 'Website', 'Referral', 'Job Fair', 'Other'],
                datasets: [{
                    label: 'Applicant Source',
                    data: [45, 30, 20, 5, 10],
                    backgroundColor: ['#d37a15', '#0a0a0a', '#b06511', '#e7f2fd', '#777'],
                    hoverOffset: 4
                }]
            };

            const hiringByDeptData = {
                labels: ['IT', 'Sales', 'Marketing', 'HR', 'Finance'],
                datasets: [{
                    label: 'Employees',
                    data: [35, 42, 28, 15, 20],
                    backgroundColor: '#d37a15',
                }]
            };

            // Chart Configurations
            const chartConfigs = [{
                elementId: 'totalApplicantsChart',
                type: 'line',
                data: totalApplicantsData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            }, {
                elementId: 'newHiresChart',
                type: 'bar',
                data: newHiresData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            }, {
                elementId: 'applicantSourceChart',
                type: 'pie',
                data: applicantSourceData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } }
                }
            }, {
                elementId: 'hiringByDeptChart',
                type: 'bar',
                data: hiringByDeptData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            }];
            
            // Loop through configurations and create charts
            function renderCharts() {
                chartConfigs.forEach(config => {
                    const ctx = document.getElementById(config.elementId);
                    if (ctx) {
                        new Chart(ctx, {
                            type: config.type,
                            data: config.data,
                            options: config.options
                        });
                    }
                });
            }

            // Hide spinner and show charts after a short delay
            const spinner = document.getElementById('loading-spinner');
            const chartSection = document.getElementById('chart-section');

            setTimeout(() => {
                spinner.style.display = 'none';
                chartSection.style.display = 'grid';
                renderCharts();
            }, 500); // Small delay to simulate loading
        });

        // Sidebar and Logout Logic
        const sidebar = document.querySelector(".sidebar");
        const menuToggle = document.querySelector(".menu-toggle");
        menuToggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });

        document.getElementById("logout-link").addEventListener("click", function (e) {
            e.preventDefault();
            localStorage.clear();
            window.location.href = "login.php";
        });
    </script>
</body>
</html>