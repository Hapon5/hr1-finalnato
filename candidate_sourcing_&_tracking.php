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
                            500: '#d37a15',
                            600: '#b8650f'
                        }
                    }
                }
            }
        }
    </script>
  </head>
<body class="bg-gray-50 font-sans">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-brand-500 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0" id="sidebar">
        <div class="flex items-center justify-between h-16 px-6 bg-brand-600">
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
                <span>Back to Dashboard</span>
            </a>
            
           
                <a href="logout.php" class="flex items-center px-4 py-3 text-white hover:bg-red-600 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Navigation -->
        <div class="bg-white shadow-sm border-b">
            <div class="flex items-center justify-between px-6 py-4">
                <button id="sidebar-toggle" class="text-gray-600 hover:text-gray-900 lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($admin_email); ?></span>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Candidate Sourcing & Tracking</h1>
                <p class="text-gray-600">Manage and track candidate applications and recruitment pipeline</p>
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
                            <option value="">All Status</option>
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

            <!-- Candidates Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
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
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-brand-500 flex items-center justify-center">
                                                        <span class="text-white font-medium">
                                                            <?php echo strtoupper(substr($candidate['full_name'], 0, 2)); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($candidate['full_name']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($candidate['email']); ?></div>
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
                                                <button onclick="viewCandidate(<?php echo $candidate['id']; ?>)" 
                                                        class="text-blue-500 hover:text-blue-600">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="editCandidate(<?php echo $candidate['id']; ?>)" 
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

    <!-- Add/Edit Candidate Modal -->
    <div id="candidateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
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

    <!-- View Candidate Modal -->
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
                    <!-- Candidate details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
        });

        sidebarClose.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });

        // Modal functionality
        const modal = document.getElementById('candidateModal');
        const viewModal = document.getElementById('viewModal');
        const addCandidateBtn = document.getElementById('addCandidateBtn');
        const closeModal = document.getElementById('closeModal');
        const closeViewModal = document.getElementById('closeViewModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const candidateForm = document.getElementById('candidateForm');

        addCandidateBtn.addEventListener('click', () => {
            document.getElementById('modalTitle').textContent = 'Add New Candidate';
            document.getElementById('formAction').value = 'add';
            document.getElementById('statusField').style.display = 'none';
            document.getElementById('candidateForm').reset();
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
        const statusFilter = document.getElementById('statusFilter');

        function performSearch() {
            const searchTerm = searchInput.value;
            const status = statusFilter.value;
            const url = new URL(window.location);
            url.searchParams.set('search', searchTerm);
            url.searchParams.set('status', status);
            window.location.href = url.toString();
        }

        searchInput.addEventListener('input', performSearch);
        statusFilter.addEventListener('change', performSearch);

        // Candidate functions
        function editCandidate(id) {
            document.getElementById('modalTitle').textContent = 'Edit Candidate';
            document.getElementById('formAction').value = 'update';
            document.getElementById('candidateId').value = id;
            document.getElementById('statusField').style.display = 'block';
            modal.classList.remove('hidden');
        }

        function viewCandidate(id) {
            // This would typically fetch candidate data via AJAX
            document.getElementById('candidateDetails').innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-700">Full Name:</label>
                            <p class="text-gray-900">John Doe</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700">Email:</label>
                            <p class="text-gray-900">john.doe@email.com</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-700">Position:</label>
                            <p class="text-gray-900">Software Engineer</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700">Experience:</label>
                            <p class="text-gray-900">5 years</p>
                        </div>
                    </div>
                </div>
            `;
            viewModal.classList.remove('hidden');
        }

        function deleteCandidate(id) {
            if (confirm('Are you sure you want to delete this candidate?')) {
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