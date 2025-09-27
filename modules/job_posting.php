<?php
session_start();
include("../Connections.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['Email']) || $_SESSION['Account_type'] !== '1') {
    header("Location: ../login.php");
    exit();
}

$admin_email = $_SESSION['Email'];

// Create job_postings table if it doesn't exist
try {
    $createTable = "CREATE TABLE IF NOT EXISTS job_postings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        position VARCHAR(255) NOT NULL,
        location VARCHAR(255) NOT NULL,
        requirements TEXT,
        contact VARCHAR(255) NOT NULL,
        platform VARCHAR(255) NOT NULL,
        date_posted DATE NOT NULL,
        status ENUM('active', 'inactive', 'closed') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $Connections->exec($createTable);
} catch (PDOException $e) {
    error_log("Error creating table: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'add') {
                $stmt = $Connections->prepare("INSERT INTO job_postings (title, position, location, requirements, contact, platform, date_posted) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['position'],
                    $_POST['location'],
                    $_POST['requirements'],
                    $_POST['contact'],
                    $_POST['platform'],
                    $_POST['date_posted']
                ]);
                $message = "Job posting added successfully!";
            } elseif ($_POST['action'] === 'edit') {
                $stmt = $Connections->prepare("UPDATE job_postings SET title=?, position=?, location=?, requirements=?, contact=?, platform=?, date_posted=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['position'],
                    $_POST['location'],
                    $_POST['requirements'],
                    $_POST['contact'],
                    $_POST['platform'],
                    $_POST['date_posted'],
                    $_POST['id']
                ]);
                $message = "Job posting updated successfully!";
            } elseif ($_POST['action'] === 'delete') {
                $stmt = $Connections->prepare("DELETE FROM job_postings WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $message = "Job posting deleted successfully!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch job postings
try {
    $stmt = $Connections->prepare("SELECT * FROM job_postings ORDER BY created_at DESC");
    $stmt->execute();
    $job_postings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching job postings: " . $e->getMessage();
    $job_postings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Posting - HR1 System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        brand: {
                            500: '#d37a15',
                            600: '#b8650f'
                        }
                    }
                }
            }
        }
    </script>
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
            max-width: calc(100vw - 260px);
            overflow-x: hidden;
        }
        .sidebar.close ~ .main-content {
            margin-left: 78px;
            max-width: calc(100vw - 78px);
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
        }
    </style>
  </head>
<body class="bg-gray-50 font-sans">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class='bx bxs-user-detail' style='font-size: 2rem; color: #fff;'></i>
            <h2>HR Admin</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="../admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <i class="fa-solid fa-bars menu-toggle"></i>
        </div>
        <header class="dashboard-header">
            <h1>Job Posting Management</h1>
        </header>

        <!-- Page Content -->
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Job Posting Management</h1>
                <p class="text-gray-600">Manage and track job postings across different platforms</p>
            </div>

            <!-- Success/Error Messages -->
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

            <!-- Action Bar -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button id="addJobBtn" class="inline-flex items-center px-6 py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Job Posting
                    </button>
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search job postings..." 
                               class="w-full sm:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    </div>
                </div>
            </div>

            <!-- Job Postings Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Posted</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($job_postings)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-briefcase text-4xl mb-4 text-gray-300"></i>
                                        <p class="text-lg">No job postings found</p>
                                        <p class="text-sm">Click "Add New Job Posting" to get started</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($job_postings as $job): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($job['title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($job['position']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($job['location']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($job['platform']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('M d, Y', strtotime($job['date_posted'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php 
                                                switch($job['status']) {
                                                    case 'active': echo 'bg-green-100 text-green-800'; break;
                                                    case 'inactive': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'closed': echo 'bg-red-100 text-red-800'; break;
                                                }
                                                ?>">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="editJob(<?php echo $job['id']; ?>)" 
                                                        class="text-brand-500 hover:text-brand-600">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="viewJob(<?php echo $job['id']; ?>)" 
                                                        class="text-blue-500 hover:text-blue-600">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="deleteJob(<?php echo $job['id']; ?>)" 
                                                        class="text-red-500 hover:text-red-600">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Job Modal -->
    <div id="jobModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Job Posting</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="jobForm" method="POST" class="p-6">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="jobId" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                            <input type="text" name="title" id="jobTitle" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
      </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <input type="text" name="position" id="jobPosition" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
</div>
</div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" id="jobLocation" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                            <select name="platform" id="jobPlatform" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                                <option value="">Select Platform</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Indeed">Indeed</option>
                                <option value="Glassdoor">Glassdoor</option>
                                <option value="Company Website">Company Website</option>
                                <option value="Job Fair">Job Fair</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
      </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact</label>
                            <input type="text" name="contact" id="jobContact" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Posted</label>
                            <input type="date" name="date_posted" id="jobDate" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
      </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                        <textarea name="requirements" id="jobRequirements" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                                  placeholder="Enter job requirements and qualifications..."></textarea>
      </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Save Job Posting
                        </button>
      </div>
    </form>
            </div>
  </div>
</div>

    <!-- View Job Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Job Details</h3>
                    <button id="closeViewModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="jobDetails" class="p-6">
                    <!-- Job details will be loaded here -->
                </div>
            </div>
        </div>
</div>

    <script>
        // Sidebar and Logout Logic
        const sidebar = document.querySelector(".sidebar");
        const menuToggle = document.querySelector(".menu-toggle");
        menuToggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });

        document.getElementById("logout-link").addEventListener("click", function (e) {
    e.preventDefault();
            localStorage.clear();
            window.location.href = "../logout.php";
        });

        // Modal functionality
        const modal = document.getElementById('jobModal');
        const viewModal = document.getElementById('viewModal');
        const addJobBtn = document.getElementById('addJobBtn');
        const closeModal = document.getElementById('closeModal');
        const closeViewModal = document.getElementById('closeViewModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const jobForm = document.getElementById('jobForm');

        addJobBtn.addEventListener('click', () => {
            document.getElementById('modalTitle').textContent = 'Add New Job Posting';
            document.getElementById('formAction').value = 'add';
            document.getElementById('jobForm').reset();
            modal.classList.remove('hidden');
        });

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        closeViewModal.addEventListener('click', () => {
            viewModal.classList.add('hidden');
        });

        cancelBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
            if (e.target === viewModal) {
                viewModal.classList.add('hidden');
            }
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
    });
});

        // Job functions
        function editJob(id) {
            // This would typically fetch job data via AJAX
            // For now, we'll just show the modal
            document.getElementById('modalTitle').textContent = 'Edit Job Posting';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('jobId').value = id;
            modal.classList.remove('hidden');
        }

        function viewJob(id) {
            // This would typically fetch job data via AJAX
            // For now, we'll show a placeholder
            document.getElementById('jobDetails').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="font-medium text-gray-700">Job Title:</label>
                        <p class="text-gray-900">Software Engineer</p>
                    </div>
                    <div>
                        <label class="font-medium text-gray-700">Position:</label>
                        <p class="text-gray-900">Senior Developer</p>
                    </div>
                    <div>
                        <label class="font-medium text-gray-700">Location:</label>
                        <p class="text-gray-900">Remote</p>
                    </div>
                    <div>
                        <label class="font-medium text-gray-700">Requirements:</label>
                        <p class="text-gray-900">5+ years experience in web development...</p>
                    </div>
                </div>
            `;
            viewModal.classList.remove('hidden');
        }

        function deleteJob(id) {
            if (confirm('Are you sure you want to delete this job posting?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
</script>
  </body>
</html>