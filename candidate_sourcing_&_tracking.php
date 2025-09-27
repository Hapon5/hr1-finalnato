<?php
session_start();
include("Connections.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['Email']) || (isset($_SESSION['Account_type']) && $_SESSION['Account_type'] !== '1')) {
    header("Location: login.php");
    exit();
}

$admin_email = $_SESSION['Email'];

// Create candidates table if it doesn't exist
try {
    $createTable = "CREATE TABLE IF NOT EXISTS candidates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        job_title VARCHAR(255) NOT NULL,
        position VARCHAR(255) NOT NULL,
        experience_years INT NOT NULL,
        age INT NOT NULL,
        contact_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL,
        address TEXT NOT NULL,
        resume_path VARCHAR(500),
        status ENUM('new', 'reviewed', 'shortlisted', 'interviewed', 'rejected', 'hired') DEFAULT 'new',
        source VARCHAR(100) DEFAULT 'Direct Application',
        skills TEXT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->exec($createTable);
} catch (PDOException $e) {
    error_log("Error creating table: " . $e->getMessage());
}

// Handle form submissions and fetch candidates logic (remains the same)
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logic for adding, updating, deleting candidates...
}

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    
    $sql = "SELECT * FROM candidates WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param]);
    }
    
    if (!empty($status_filter) && $status_filter !== 'all') {
        $sql .= " AND status = ?";
        $params[] = $status_filter;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching candidates: " . $e->getMessage();
    $candidates = [];
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Sourcing & Tracking - HR1 System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'ui-sans-serif', 'system-ui'] },
                    colors: { brand: { 500: '#d37a15', 600: '#b8650f' } }
                }
            }
        }
    </script>
    <style>
        :root {
            --primary-color: #d37a15;
            --background-light: #f8f9fa;
            --text-light: #f4f4f4;
        }
        body {
            background-color: var(--background-light);
            display: flex;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
        }
        
        /* --- INAYOS NA SIDEBAR STYLES --- */
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
            white-space: nowrap;
        }
        .sidebar.close .sidebar-header h2 { opacity: 0; pointer-events: none; }
        
        .sidebar-nav {
            list-style: none;
            flex-grow: 1;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar-nav li { margin-bottom: 10px; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-light); /* Puti na text */
            background-color: transparent; /* Walang background */
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }
        .sidebar-nav a:hover {
            background-color: rgba(0, 0, 0, 0.2); /* Itim na hover */
        }
        .sidebar-nav a.active {
             background-color: rgba(0, 0, 0, 0.15);
             font-weight: 500;
        }
        .sidebar-nav a i {
            font-size: 20px;
            margin-right: 15px;
            min-width: 20px;
            text-align: center;
        }
        .sidebar.close .sidebar-nav span { opacity: 0; pointer-events: none; }
        .logout-item {
            margin-top: auto; /* Itulak sa baba */
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 20px 30px;
            transition: margin-left 0.3s ease;
        }
        .sidebar.close ~ .main-content { margin-left: 78px; }
        .menu-toggle { font-size: 1.5rem; cursor: pointer; color: #333; }
    </style>
  </head>
<body class="bg-gray-50 font-sans">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class='fas fa-user-shield' style='font-size: 2rem; color: #fff;'></i>
            <h2>HR Admin</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            
            <!-- Logout button sa baba -->
            <li class="logout-item"><a href="logout.php" id="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="flex justify-between items-center mb-6">
            <i class="fas fa-bars menu-toggle"></i>
        </div>

        <!-- Page Content -->
        <div class="p-0">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Candidate Sourcing & Tracking</h1>
                <p class="text-gray-600">Manage and track candidate applications</p>
            </div>

            <!-- Messages, Action Bar, and Table remain the same -->
             <?php if (isset($message)): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-700"><?php echo htmlspecialchars($message); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="text-red-700"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <button id="addCandidateBtn" class="inline-flex items-center px-6 py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Candidate
                    </button>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" placeholder="Search candidates..." 
                                   class="w-full sm:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                            <option value="all">All Status</option>
                            <option value="new">New</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="shortlisted">Shortlisted</option>
                            <option value="interviewed">Interviewed</option>
                            <option value="rejected">Rejected</option>
                            <option value="hired">Hired</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <!-- Table content... -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (Add/Edit and View) -->
    <!-- ... -->

    <script>
        // Sidebar toggle
        const sidebar = document.querySelector(".sidebar");
        const menuToggle = document.querySelector(".menu-toggle");
        if(menuToggle) {
            menuToggle.addEventListener("click", () => sidebar.classList.toggle("close"));
        }

        // Other JavaScript for modals, search, etc. remains the same
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            const url = new URL(window.location);
            url.searchParams.set('status', status);
            window.location.href = url.toString();
        });
    </script>
  </body>
</html>

