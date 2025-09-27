<?php
session_start();
require_once "Connections.php";

// Require admin
if (!isset($_SESSION['Email']) || (isset($_SESSION['Account_type']) && $_SESSION['Account_type'] !== '1')) {
    header('Location: login.php');
    exit();
}
$admin_email = $_SESSION['Email'];

// Ensure table exists
try {
    $conn->exec(
        "CREATE TABLE IF NOT EXISTS interviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            candidate_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            position VARCHAR(255) NOT NULL,
            interviewer VARCHAR(255) NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME NOT NULL,
            location VARCHAR(255) NOT NULL,
            status ENUM('scheduled','completed','cancelled','no_show') DEFAULT 'scheduled',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    );
} catch (Throwable $e) {
    error_log('Create interviews table failed: ' . $e->getMessage());
}

// Handle actions and fetch data
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'add') {
            $stmt = $conn->prepare(
                'INSERT INTO interviews (candidate_name, email, position, interviewer, start_time, end_time, location, status, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                trim($_POST['candidate_name']),
                trim($_POST['email']),
                trim($_POST['position']),
                trim($_POST['interviewer']),
                $_POST['start_time'],
                $_POST['end_time'],
                trim($_POST['location']),
                $_POST['status'] ?? 'scheduled',
                $_POST['notes'] ?? ''
            ]);
            $message = 'Interview scheduled successfully';
        } elseif ($_POST['action'] === 'edit') {
            // Edit logic can be added here
        } elseif ($_POST['action'] === 'delete') {
            // Delete logic can be added here
        }
    } catch (Throwable $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

try {
    $filter = isset($_GET['status']) ? $_GET['status'] : '';
    $q = 'SELECT * FROM interviews';
    $params = [];
    if ($filter !== '' && $filter !== 'all') {
        $q .= ' WHERE status = ?';
        $params[] = $filter;
    }
    $q .= ' ORDER BY start_time DESC';
    $stmt = $conn->prepare($q);
    $stmt->execute($params);
    $interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $interviews = [];
    $error = 'Failed to load interviews: ' . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Interview Schedule - HR1</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Poppins','ui-sans-serif','system-ui'] }, colors: { brand: {500:'#d37a15',600:'#b8650f'} } } }
        }
    </script>
    <style>
        :root {
            --primary-color: #d37a15;
            --background-light: #f8f9fa;
            --background-card: #ffffff;
            --text-dark: #333;
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
            flex-grow: 1; /* Para mapuno ang space */
            padding-top: 20px;
            display: flex; /* Gagamit ng flexbox */
            flex-direction: column; /* Para vertical ang ayos */
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
            margin-top: auto; /* Itulak ang item na ito sa pinakababa */
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
            <i class='bx bxs-user-shield' style='font-size: 2rem; color: #fff;'></i>
            <h2>HR Admin</h2>
        </div>
        <!-- INAYOS NA NAVIGATION LINKS -->
        <ul class="sidebar-nav">
            <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            
            <!-- Logout button sa baba -->
            <li class="logout-item"><a href="logout.php" id="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="flex justify-between items-center mb-6">
            <i class="fas fa-bars menu-toggle"></i>
        </div>
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Interview Scheduling</h1>
        </header>

        <!-- Page content remains the same -->
        <div class="p-0">
            <?php if ($message): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg"><?= htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <button id="openModal" class="inline-flex items-center px-6 py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600">
                        <i class="fas fa-plus mr-2"></i> Schedule Interview
                    </button>
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-gray-600">Status</label>
                        <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                            <option value="all">All</option>
                            <option value="scheduled" <?= (isset($_GET['status']) && $_GET['status']==='scheduled')?'selected':''; ?>>Scheduled</option>
                            <option value="completed" <?= (isset($_GET['status']) && $_GET['status']==='completed')?'selected':''; ?>>Completed</option>
                            <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status']==='cancelled')?'selected':''; ?>>Cancelled</option>
                            <option value="no_show" <?= (isset($_GET['status']) && $_GET['status']==='no_show')?'selected':''; ?>>No-show</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Candidate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interviewer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($interviews)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">No interviews scheduled.</td>
                                </tr>
                            <?php else: foreach ($interviews as $iv): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($iv['candidate_name']); ?><div class="text-gray-500"><?= htmlspecialchars($iv['email']); ?></div></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($iv['position']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($iv['interviewer']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('M d, Y g:i A', strtotime($iv['start_time'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('M d, Y g:i A', strtotime($iv['end_time'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($iv['location']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php
                                            echo $iv['status']==='scheduled'?'bg-blue-100 text-blue-800':($iv['status']==='completed'?'bg-green-100 text-green-800':($iv['status']==='cancelled'?'bg-red-100 text-red-800':'bg-yellow-100 text-yellow-800'));
                                        ?>"><?= ucfirst(str_replace('_',' ',$iv['status'])); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button class="text-brand-500 hover:text-brand-600" onclick="openEdit(<?= (int)$iv['id']; ?>)"><i class="fas fa-edit"></i></button>
                                            <button class="text-red-500 hover:text-red-600" onclick="confirmDelete(<?= (int)$iv['id']; ?>)"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal and other scripts remain the same -->
    <div id="modal" class="fixed inset-0 bg-black/40 hidden z-50">
        <!-- Modal content... -->
    </div>
    <form id="deleteForm" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId" value="">
    </form>
    <script>
        // All JavaScript remains the same
        const sidebar = document.querySelector(".sidebar");
        const menuToggle = document.querySelector(".menu-toggle");
        if(menuToggle) {
            menuToggle.addEventListener("click", () => sidebar.classList.toggle("close"));
        }
        // ... rest of the script
    </script>
  </body>
</html>

