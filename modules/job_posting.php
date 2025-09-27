<?php
session_start();
// Use a relative path to go up one directory from 'modules' to the root 'hr1' folder
// Make sure this path is correct for your file structure.
include("../Connections.php"); 

// Check if user is logged in and is admin
if (!isset($_SESSION['Email']) || (isset($_SESSION['Account_type']) && $_SESSION['Account_type'] !== '1')) {
    header("Location: ../login.php");
    exit();
}

// --- AJAX HANDLER ---
if (isset($_GET['action']) && $_GET['action'] == 'get_job' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    try {
        $stmt = $conn->prepare("SELECT *, DATE_FORMAT(date_posted, '%Y-%m-%d') as date_posted_formatted FROM job_postings WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($job) {
            echo json_encode(['status' => 'success', 'data' => $job]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Job not found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// --- FORM SUBMISSION HANDLER (ADD/EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = [];
    try {
        // Sanitize inputs
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);
        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
        $requirements = filter_input(INPUT_POST, 'requirements', FILTER_SANITIZE_STRING);
        $contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);
        $platform = filter_input(INPUT_POST, 'platform', FILTER_SANITIZE_STRING);
        $date_posted = $_POST['date_posted']; // Assuming valid date format from input type="date"
        $status = $_POST['status'];
        
        if ($_POST['action'] === 'add') {
            $stmt = $conn->prepare("INSERT INTO job_postings (title, position, location, requirements, contact, platform, date_posted, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $position, $location, $requirements, $contact, $platform, $date_posted, $status]);
            $response = ['status' => 'success', 'message' => 'Job posting added successfully!'];
        } elseif ($_POST['action'] === 'edit' && !empty($_POST['id'])) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $stmt = $conn->prepare("UPDATE job_postings SET title=?, position=?, location=?, requirements=?, contact=?, platform=?, date_posted=?, status=? WHERE id=?");
            $stmt->execute([$title, $position, $location, $requirements, $contact, $platform, $date_posted, $status, $id]);
            $response = ['status' => 'success', 'message' => 'Job posting updated successfully!'];
        }
    } catch (PDOException $e) {
        $response = ['status' => 'error', 'message' => 'Database operation failed: ' . $e->getMessage()];
    }
    echo json_encode($response);
    exit();
}

// --- DELETE HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_delete'])) {
    if ($_POST['action_delete'] === 'delete' && !empty($_POST['id'])) {
        try {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $stmt = $conn->prepare("DELETE FROM job_postings WHERE id=?");
            $stmt->execute([$id]);
            $_SESSION['message'] = "Job posting deleted successfully!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting job posting.";
        }
        header("Location: job_posting.php");
        exit();
    }
}


// --- INITIAL PAGE LOAD: Fetch all job postings ---
try {
    $stmt = $conn->prepare("SELECT * FROM job_postings ORDER BY created_at DESC");
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
    <title>Job Posting Management - HR Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Poppins', 'sans-serif'] }, 
                    colors: { 
                        brand: { 500: '#1f2937', 600: '#374151' } 
                    } 
                } 
            }
        }
    </script>
    <style>
        .sidebar { width: 260px; background-color: #0000; position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; transition: all 0.3s ease; }
        .main-content { margin-left: 260px; transition: margin-left 0.3s ease; }
        .sidebar.close { width: 78px; }
        .sidebar.close ~ .main-content { margin-left: 78px; }
        .modal-body { max-height: 70vh; overflow-y: auto; }
        .sidebar.close .sidebar-header h2, .sidebar.close .sidebar-nav span { display: none; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    
    <nav class="sidebar p-5 text-white flex flex-col" style="background-color: black;">
        <div class="sidebar-header flex items-center pb-5 border-b border-white/20">
            <i class='fas fa-user-shield text-3xl'></i>
            <h2 class="text-xl font-bold ml-3 whitespace-nowrap">HR Admin</h2>
        </div>
        <ul class="sidebar-nav flex-grow pt-5 space-y-2">
            <li><a href="../admin.php" class="flex items-center p-3 rounded-lg hover:bg-white/20 transition-colors"><i class="fas fa-tachometer-alt w-6 text-center"></i><span class="ml-3 whitespace-nowrap">Dashboard</span></a></li>
        </ul>
        <div>
           <a href="../logout.php" class="flex items-center p-3 rounded-lg hover:bg-white/20 transition-colors"><i class="fas fa-sign-out-alt w-6 text-center"></i><span class="ml-3 whitespace-nowrap">Logout</span></a>
        </div>
    </nav>

    <div class="main-content p-6">
        <i class="fas fa-bars text-2xl cursor-pointer mb-6 text-gray-700" id="menu-toggle"></i>
        
        <div class="flex justify-between items-start mb-8 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Job Posting Management</h1>
                <p class="text-gray-600">Manage and track job postings across different platforms</p>
            </div>
            <div id="live-datetime" class="text-right">
                </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="addJobBtn" class="inline-flex items-center px-6 py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors shadow hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Job Posting
                </button>
                <div class="relative">
                    <i class="fas fa-search text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="searchInput" placeholder="Search jobs..." class="w-full sm:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                </div>
            </div>
        </div>

        <div class="bg-dark rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="jobsTable">
                    <thead style="background-color: #323A3C;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($job_postings)): ?>
                            <tr id="no-jobs-row">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-briefcase text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg">No job postings found</p>
                                    <p class="text-sm">Click "Add New Job Posting" to get started</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($job_postings as $job): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($job['title']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($job['position']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($job['location']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= date('M d, Y g:i A', strtotime($job['created_at'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2.5 py-0.5 text-xs font-medium rounded-full <?= $job['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>"><?= ucfirst($job['status']) ?></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <button onclick="viewJob(<?= $job['id'] ?>)" class="text-blue-500 hover:text-blue-700" title="View"><i class="fas fa-eye"></i></button>
                                            <button onclick="editJob(<?= $job['id'] ?>)" class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button onclick="deleteJob(<?= $job['id'] ?>)" class="text-red-500 hover:text-red-700" title="Delete"><i class="fas fa-trash"></i></button>
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


   <div id="jobModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full" style="background-color: #000;" >
                <div class="flex items-center justify-between p-5 border-b">
                    <h3 id="modalTitle" class="text-xl text-white font-semibold text-gray-800">Add New Job Posting</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <form id="jobForm" class="modal-body p-6">
                    <input type="hidden" id="formAction" name="action" value="add">
                    <input type="hidden" id="jobId" name="id" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="jobTitle" class="block text-white text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="jobTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500" required>
                        </div>
                        <div>
                            <label for="jobPosition" class="block text-white text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input type="text" id="jobPosition" name="position" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500" required>
                        </div>
                        <div>
                            <label for="jobLocation" class="block text-sm text-white  font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" id="jobLocation" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500" required>
                        </div>
                        <div>
                            <label for="jobPlatform" class="block text-white text-sm font-medium text-gray-700 mb-1">Platform</label>
                            <input type="text" id="jobPlatform" name="platform" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                        </div>
                         <div>
                            <label for="jobContact" class="block text-white text-sm font-medium text-gray-700 mb-1">Contact</label>
                            <input type="text" id="jobContact" name="contact" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div>
                            <label for="jobDate" class="block text-white text-sm font-medium text-gray-700 mb-1">Date Posted</label>
                            <input type="date" id="jobDate" name="date_posted" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500" required>
                        </div>
                         <div class="md:col-span-2">
                            <label for="jobStatus" class="block text-white text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="jobStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="jobRequirements" class="block text-white text-sm font-medium text-gray-700 mb-1">Requirements</label>
                            <textarea id="jobRequirements" name="requirements" rows="4" class="w-full px-3 py-2 border border-ray-300 rounded-lg focus:ring-2 focus:ring-gray-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end pt-5 border-t mt-5">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 mr-2">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600">Save Job Posting</button>
                    </div>
                </form>
            </div>
        </div>
</div>


    <div id="viewModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="flex items-center justify-between p-5 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">Job Details</h3>
                    <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <div id="jobDetails" class="p-6 modal-body"></div>
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
            <p class="text-lg font-semibold text-gray-700">${formattedDate}</p>
            <p class="text-3xl font-bold text-gray-900">${formattedTime}</p>
        `;
    }
    // Update the time immediately and then every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // --- Modal elements ---
    const modal = document.getElementById('jobModal');
    const viewModal = document.getElementById('viewModal');
    const addJobBtn = document.getElementById('addJobBtn');
    const closeModalBtns = document.querySelectorAll('#closeModal, #cancelBtn');
    const closeViewModal = document.getElementById('closeViewModal');
    const jobForm = document.getElementById('jobForm');
    
    // --- Modal Controls ---
    addJobBtn.addEventListener('click', () => {
        jobForm.reset();
        document.getElementById('modalTitle').textContent = 'Add New Job Posting';
        document.getElementById('formAction').value = 'add';
        document.getElementById('jobId').value = '';
        document.getElementById('jobDate').valueAsDate = new Date(); // Set default date to today
        modal.classList.remove('hidden');
    });

    closeModalBtns.forEach(btn => btn.addEventListener('click', () => modal.classList.add('hidden')));
    closeViewModal.addEventListener('click', () => viewModal.classList.add('hidden'));

    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
        if (e.target === viewModal) viewModal.classList.add('hidden');
    });
    
    // --- AJAX Form Submission ---
    jobForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(jobForm);

        fetch('job_posting.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload(); // Reload the page to see changes
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Submission error:', error);
            alert('An unexpected error occurred. Check the console for details.');
        });
    });

    // --- Sidebar Toggle ---
    document.getElementById('menu-toggle').addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('close');
    });

    // --- Search Functionality ---
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#jobsTable tbody tr');
        let hasVisibleRows = false;

        tableRows.forEach(row => {
            if (row.id === 'no-jobs-row') return;
            const rowText = row.textContent.toLowerCase();
            if (rowText.includes(searchTerm)) {
                row.style.display = '';
                hasVisibleRows = true;
            } else {
                row.style.display = 'none';
            }
        });

        const noJobsRow = document.getElementById('no-jobs-row');
        const allJobsRows = document.querySelectorAll('#jobsTable tbody tr:not(#no-jobs-row)');
        
        if(noJobsRow) {
            if (allJobsRows.length > 0) {
                 noJobsRow.style.display = hasVisibleRows ? 'none' : 'table-row';
            }
        }
    });
});

// --- Global Functions for Buttons ---
function editJob(id) {
    fetch(`job_posting.php?action=get_job&id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const job = data.data;
            document.getElementById('modalTitle').textContent = 'Edit Job Posting';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('jobId').value = job.id;
            document.getElementById('jobTitle').value = job.title;
            document.getElementById('jobPosition').value = job.position;
            document.getElementById('jobLocation').value = job.location;
            document.getElementById('jobPlatform').value = job.platform;
            document.getElementById('jobContact').value = job.contact;
            document.getElementById('jobDate').value = job.date_posted_formatted;
            document.getElementById('jobStatus').value = job.status;
            document.getElementById('jobRequirements').value = job.requirements;
            
            document.getElementById('jobModal').classList.remove('hidden');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Fetch error for edit:', error));
}

function viewJob(id) {
    fetch(`job_posting.php?action=get_job&id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const job = data.data;
            // Create a date object in UTC to avoid timezone issues with date-only strings
            const date = new Date(job.date_posted_formatted + 'T00:00:00Z');
            const formattedDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'UTC' });

            const detailsHtml = `
                <div class="space-y-4 text-gray-800">
                    <div><strong class="font-semibold text-gray-600 block">Title:</strong> ${job.title || 'N/A'}</div>
                    <div><strong class="font-semibold text-gray-600 block">Position:</strong> ${job.position || 'N/A'}</div>
                    <div><strong class="font-semibold text-gray-600 block">Location:</strong> ${job.location || 'N/A'}</div>
                    <div><strong class="font-semibold text-gray-600 block">Platform:</strong> ${job.platform || 'N/A'}</div>
                    <div><strong class="font-semibold text-gray-600 block">Contact:</strong> ${job.contact || 'N/A'}</div>
                    <div><strong class="font-semibold text-gray-600 block">Date Posted:</strong> ${formattedDate}</div>
                    <hr class="my-4">
                    <div>
                        <strong class="font-semibold text-gray-600 block mb-2">Requirements:</strong>
                        <div class="prose prose-sm max-w-none whitespace-pre-wrap text-gray-700 p-3 bg-gray-50 rounded-md">${job.requirements || 'N/A'}</div>
                    </div>
                </div>
            `;
            document.getElementById('jobDetails').innerHTML = detailsHtml;
            document.getElementById('viewModal').classList.remove('hidden');
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function deleteJob(id) {
    if (confirm('Are you sure you want to delete this job posting?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'job_posting.php';
        form.style.display = 'hidden';
        form.innerHTML = `<input type="hidden" name="action_delete" value="delete"><input type="hidden" name="id" value="${id}">`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

</body>
</html>