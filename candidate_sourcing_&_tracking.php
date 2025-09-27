<?php
session_start();
include("Connections.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['Email']) || $_SESSION['Account_type'] !== '1') {
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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'add') {
                // Handle file upload
                $resume_path = null;
                if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../uploads/resumes/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
                    $resume_filename = uniqid() . '_' . time() . '.' . $file_extension;
                    $resume_path = $upload_dir . $resume_filename;
                    move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
                }

                $stmt = $conn->prepare("INSERT INTO candidates (full_name, job_title, position, experience_years, age, contact_number, email, address, resume_path, source, skills, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['full_name'],
                    $_POST['job_title'],
                    $_POST['position'],
                    $_POST['experience_years'],
                    $_POST['age'],
                    $_POST['contact_number'],
                    $_POST['email'],
                    $_POST['address'],
                    $resume_path,
                    $_POST['source'] ?? 'Direct Application',
                    $_POST['skills'] ?? '',
                    $_POST['notes'] ?? ''
                ]);
                $message = "Candidate added successfully!";
            } elseif ($_POST['action'] === 'update') {
                $stmt = $conn->prepare("UPDATE candidates SET full_name=?, job_title=?, position=?, experience_years=?, age=?, contact_number=?, email=?, address=?, source=?, skills=?, notes=?, status=? WHERE id=?");
                $stmt->execute([
                    $_POST['full_name'],
                    $_POST['job_title'],
                    $_POST['position'],
                    $_POST['experience_years'],
                    $_POST['age'],
                    $_POST['contact_number'],
                    $_POST['email'],
                    $_POST['address'],
                    $_POST['source'],
                    $_POST['skills'],
                    $_POST['notes'],
                    $_POST['status'],
                    $_POST['id']
                ]);
                $message = "Candidate updated successfully!";
            } elseif ($_POST['action'] === 'delete') {
                $stmt = $conn->prepare("DELETE FROM candidates WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $message = "Candidate deleted successfully!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch candidates
try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    
    $sql = "SELECT * FROM candidates WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (full_name LIKE ? OR email LIKE ? OR job_title LIKE ? OR position LIKE ?)";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    }
    
    if (!empty($status_filter)) {
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
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        brand: {
                            500: '#0000',
                            600: '#0000'
                        }
                    }
                }
            }
        }
    </script>
  </head>
<body class="bg-gray-50 font-sans">
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-black transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0" id="sidebar">
        <div class="flex items-center justify-between h-16 px-6 bg-gray-900">
            <div class="flex items-center">
                <i class="fas fa-users text-white text-2xl mr-3"></i>
                <h1 class="text-white text-xl font-bold">HR1</h1>
            </div>
            <button id="sidebar-close" class="text-white lg:hidden">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <a href="./admin.php" class="flex items-center text-white hover:text-yellow-300 transition-colors mb-6">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="logout.php" class="flex items-center px-4 py-3 text-white hover:bg-red-600 rounded-lg transition-colors">
                <i class="fas fa-sign-out-alt mr-3"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="lg:ml-64">
        <div class="shadow-sm border-b" style="background-color: #323A3C;">
            <div class="flex items-center justify-between px-6 py-3">
                <button id="sidebar-toggle" class="text-white hover:text-gray-300 lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div id="live-datetime" class="text-right text-white hidden sm:block">
                    </div>
                <div class="flex items-center space-x-4">
                    <span class="text-white">Welcome, <?php echo htmlspecialchars($admin_email); ?></span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Candidate Sourcing & Tracking</h1>
                <p class="text-gray-600">Manage and track candidate applications and recruitment pipeline</p>
            </div>

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

            <div class="rounded-lg shadow-sm p-6 mb-6" style="background-color: #0000;">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <button id="addCandidateBtn" 
                        class="inline-flex items-center px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors w-auto">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Candidate
                    </button>
                </div>


                    <!-- Container -->
<div class="flex justify-end gap-4">
    
    <!-- Search -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
        <input type="text" id="searchInput" placeholder="Search candidates..." 
            class="w-full sm:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>

    <!-- Dropdown -->
    <select id="statusFilter" 
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <option value="">All Status</option>
        <option value="new">New</option>
        <option value="reviewed">Reviewed</option>
        <option value="shortlisted">Shortlisted</option>
        <option value="interviewed">Interviewed</option>
        <option value="rejected">Rejected</option>
        <option value="hired">Hired</option>
    </select>
</div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" style="margin-top: 20px;">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Applied</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($candidates)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-user-plus text-4xl mb-4 text-gray-300"></i>
                                        <p class="text-lg">No candidates found</p>
                                        <p class="text-sm">Click "Add New Candidate" to get started</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($candidates as $candidate): ?>
                                   <td class="px-6 py-4 whitespace-nowrap text-white">
    <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
            <!-- Circle -->
            <div class="h-10 w-10 rounded-full flex items-center justify-center" 
                 style="background-color: black; color: white;">
                <span class="text-white font-medium">
                    <?php echo strtoupper(substr($candidate['full_name'], 0, 2)); ?>
                </span>
            </div>
        </div>
    </div>
</td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($candidate['job_title']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($candidate['position']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $candidate['experience_years']; ?> years
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($candidate['contact_number']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($candidate['source']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php 
                                                switch($candidate['status']) {
                                                    case 'new': echo 'bg-gray-100 text-gray-800'; break;
                                                    case 'reviewed': echo 'bg-blue-100 text-blue-800'; break;
                                                    case 'shortlisted': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'interviewed': echo 'bg-purple-100 text-purple-800'; break;
                                                    case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                                    case 'hired': echo 'bg-green-100 text-green-800'; break;
                                                }
                                                ?>">
                                                <?php echo ucfirst($candidate['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('M d, Y', strtotime($candidate['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="viewCandidate(<?php echo htmlspecialchars(json_encode($candidate)); ?>)" 
                                                        class="text-blue-500 hover:text-blue-600">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="editCandidate(<?php echo htmlspecialchars(json_encode($candidate)); ?>)" 
                                                        class="text-brand-500 hover:text-brand-600">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($candidate['resume_path']): ?>
                                                    <a href="<?php echo htmlspecialchars($candidate['resume_path']); ?>" 
                                                       target="_blank" class="text-green-500 hover:text-green-600">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button onclick="deleteCandidate(<?php echo $candidate['id']; ?>)" 
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

    <div id="candidateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[95vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b sticky top-0 bg-white">
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Candidate</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="candidateForm" method="POST" enctype="multipart/form-data" class="p-6">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="candidateId" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="full_name" id="fullName" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                            <input type="text" name="job_title" id="jobTitle" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Position *</label>
                            <input type="text" name="position" id="position" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Experience (Years) *</label>
                            <input type="number" name="experience_years" id="experienceYears" required min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                            <input type="number" name="age" id="age" required min="18" max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number *</label>
                            <input type="tel" name="contact_number" id="contactNumber" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" id="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                            <select name="source" id="source"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                                <option value="Direct Application">Direct Application</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Indeed">Indeed</option>
                                <option value="Glassdoor">Glassdoor</option>
                                <option value="Referral">Referral</option>
                                <option value="Job Fair">Job Fair</option>
                                <option value="Recruitment Agency">Recruitment Agency</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                        <textarea name="address" id="address" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resume (PDF/DOC)</label>
                            <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div id="statusField" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                                <option value="new">New</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="shortlisted">Shortlisted</option>
                                <option value="interviewed">Interviewed</option>
                                <option value="rejected">Rejected</option>
                                <option value="hired">Hired</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                            <textarea name="skills" id="skills" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                                      placeholder="Enter candidate skills..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                                      placeholder="Additional notes..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Save Candidate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Candidate Details</h3>
                    <button id="closeViewModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="candidateDetails" class="p-6">
                    </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Live Date and Time ---
        const dateTimeElement = document.getElementById('live-datetime');
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            
            const formattedDate = now.toLocaleDateString('en-US', dateOptions);
            const formattedTime = now.toLocaleTimeString('en-US', timeOptions);
            
            dateTimeElement.innerHTML = `
                <p class="text-sm font-semibold text-gray-200">${formattedDate}</p>
                <p class="text-lg font-bold text-white">${formattedTime}</p>
            `;
        }
        // Update the time immediately and then every second
        if(dateTimeElement) {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        }

        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
            });
        }
        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
            });
        }

        // Modal functionality
        const modal = document.getElementById('candidateModal');
        const viewModal = document.getElementById('viewModal');
        const addCandidateBtn = document.getElementById('addCandidateBtn');
        const closeModalBtn = document.getElementById('closeModal');
        const closeViewModalBtn = document.getElementById('closeViewModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const candidateForm = document.getElementById('candidateForm');

        if (addCandidateBtn) {
            addCandidateBtn.addEventListener('click', () => {
                document.getElementById('modalTitle').textContent = 'Add New Candidate';
                document.getElementById('formAction').value = 'add';
                document.getElementById('statusField').style.display = 'none';
                candidateForm.reset();
                document.getElementById('candidateId').value = '';
                modal.classList.remove('hidden');
            });
        }

        function hideModals() {
            modal.classList.add('hidden');
            viewModal.classList.add('hidden');
        }

        if(closeModalBtn) closeModalBtn.addEventListener('click', hideModals);
        if(closeViewModalBtn) closeViewModalBtn.addEventListener('click', hideModals);
        if(cancelBtn) cancelBtn.addEventListener('click', hideModals);

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal || e.target === viewModal) {
                hideModals();
            }
        });
        
        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');

        function performSearchAndFilter() {
            const searchTerm = searchInput.value;
            const status = statusFilter.value;
            const url = new URL(window.location);
            url.searchParams.set('search', searchTerm);
            url.searchParams.set('status', status);
            window.location.href = url.toString();
        }

        if (searchInput) searchInput.addEventListener('input', performSearchAndFilter);
        if (statusFilter) statusFilter.addEventListener('change', performSearchAndFilter);

        // Pre-fill search and filter from URL
        const urlParams = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = urlParams.get('search') || '';
        if (statusFilter) statusFilter.value = urlParams.get('status') || '';
    });

    // Candidate functions (now global)
    function editCandidate(candidate) {
        document.getElementById('modalTitle').textContent = 'Edit Candidate';
        document.getElementById('formAction').value = 'update';
        document.getElementById('statusField').style.display = 'block';

        // Populate form
        document.getElementById('candidateId').value = candidate.id;
        document.getElementById('fullName').value = candidate.full_name;
        document.getElementById('jobTitle').value = candidate.job_title;
        document.getElementById('position').value = candidate.position;
        document.getElementById('experienceYears').value = candidate.experience_years;
        document.getElementById('age').value = candidate.age;
        document.getElementById('contactNumber').value = candidate.contact_number;
        document.getElementById('email').value = candidate.email;
        document.getElementById('address').value = candidate.address;
        document.getElementById('source').value = candidate.source;
        document.getElementById('status').value = candidate.status;
        document.getElementById('skills').value = candidate.skills;
        document.getElementById('notes').value = candidate.notes;
        
        document.getElementById('candidateModal').classList.remove('hidden');
    }

    function viewCandidate(candidate) {
        const detailsContainer = document.getElementById('candidateDetails');
        const statusColors = {
            new: 'bg-gray-100 text-gray-800', reviewed: 'bg-blue-100 text-blue-800',
            shortlisted: 'bg-yellow-100 text-yellow-800', interviewed: 'bg-purple-100 text-purple-800',
            rejected: 'bg-red-100 text-red-800', hired: 'bg-green-100 text-green-800'
        };
        const statusClass = statusColors[candidate.status] || 'bg-gray-100 text-gray-800';

        let resumeLink = candidate.resume_path 
            ? `<a href="${candidate.resume_path}" target="_blank" class="text-blue-500 hover:underline">Download Resume <i class="fas fa-download ml-1"></i></a>`
            : '<span class="text-gray-500">No resume uploaded</span>';

        detailsContainer.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-gray-600">Full Name:</label>
                        <p class="text-gray-900 text-lg">${candidate.full_name}</p>
                    </div>
                    <div>
                        <label class="font-semibold text-gray-600">Job Title:</label>
                        <p class="text-gray-900">${candidate.job_title}</p>
                    </div>
                     <div>
                        <label class="font-semibold text-gray-600">Position:</label>
                        <p class="text-gray-900">${candidate.position}</p>
                    </div>
                </div>
                <hr>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div><label class="font-semibold text-gray-600">Email:</label><p>${candidate.email}</p></div>
                    <div><label class="font-semibold text-gray-600">Contact:</label><p>${candidate.contact_number}</p></div>
                    <div><label class="font-semibold text-gray-600">Experience:</label><p>${candidate.experience_years} years</p></div>
                    <div><label class="font-semibold text-gray-600">Age:</label><p>${candidate.age}</p></div>
                </div>
                 <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div><label class="font-semibold text-gray-600">Source:</label><p>${candidate.source}</p></div>
                    <div><label class="font-semibold text-gray-600">Status:</label><p><span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">${candidate.status.charAt(0).toUpperCase() + candidate.status.slice(1)}</span></p></div>
                    <div><label class="font-semibold text-gray-600">Date Applied:</label><p>${new Date(candidate.created_at).toLocaleDateString()}</p></div>
                     <div><label class="font-semibold text-gray-600">Resume:</label><p>${resumeLink}</p></div>
                </div>
                <hr>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div>
                        <label class="font-semibold text-gray-600">Address:</label>
                        <p class="text-gray-700 whitespace-pre-wrap">${candidate.address || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="font-semibold text-gray-600">Skills:</label>
                        <p class="text-gray-700 whitespace-pre-wrap">${candidate.skills || 'N/A'}</p>
                    </div>
                 </div>
                 <div>
                    <label class="font-semibold text-gray-600">Notes:</label>
                    <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded-md">${candidate.notes || 'N/A'}</p>
                </div>
            </div>`;
        document.getElementById('viewModal').classList.remove('hidden');
    }

    function deleteCandidate(id) {
        if (confirm('Are you sure you want to delete this candidate?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
  </body>
</html>