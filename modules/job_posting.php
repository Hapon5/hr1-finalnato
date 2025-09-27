<?php
session_start();
include("../Connections.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['Email']) || (isset($_SESSION['Account_type']) && $_SESSION['Account_type'] !== '1')) {
    header("Location: ../login.php");
    exit();
}

// --- AJAX HANDLER ---
// Handle specific AJAX requests for fetching job data
if (isset($_GET['action']) && $_GET['action'] == 'get_job' && isset($_GET['id'])) {
    try {
        $stmt = $Connections->prepare("SELECT * FROM job_postings WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        if ($job) {
            // Format date for HTML date input
            $job['date_posted_formatted'] = date('Y-m-d', strtotime($job['date_posted']));
            echo json_encode(['status' => 'success', 'data' => $job]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Job not found.']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit(); // IMPORTANT: Stop script execution after AJAX response
}


// Handle form submissions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [];
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $stmt = $Connections->prepare("INSERT INTO job_postings (title, position, location, requirements, contact, platform, date_posted) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'], $_POST['position'], $_POST['location'],
                    $_POST['requirements'], $_POST['contact'], $_POST['platform'], $_POST['date_posted']
                ]);
                $response = ['status' => 'success', 'message' => 'Job posting added successfully!'];
            } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
                $stmt = $Connections->prepare("UPDATE job_postings SET title=?, position=?, location=?, requirements=?, contact=?, platform=?, date_posted=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'], $_POST['position'], $_POST['location'],
                    $_POST['requirements'], $_POST['contact'], $_POST['platform'], $_POST['date_posted'],
                    $_POST['id']
                ]);
                $response = ['status' => 'success', 'message' => 'Job posting updated successfully!'];
            } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
                $stmt = $Connections->prepare("DELETE FROM job_postings WHERE id=?");
                $stmt->execute([$_POST['id']]);
                // For delete, we'll just redirect back
                $_SESSION['message'] = "Job posting deleted successfully!";
                header("Location: job_posting.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    }

    // For AJAX add/edit requests, return JSON
    if (!empty($response)) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

// Fetch all job postings for initial page load
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
    <title>Job Posting - HR Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { /* Tailwind config... */ }
    </script>
    <style>
      /* Your existing sidebar and main content CSS... */
      .sidebar { width: 260px; /* ... */ }
      .main-content { margin-left: 260px; /* ... */ }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    
    <nav class="sidebar">
        </nav>

    <div class="main-content p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Job Posting Management</h1>

        </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
            modal.classList.remove('hidden');
        });

        closeModalBtns.forEach(btn => btn.addEventListener('click', () => modal.classList.add('hidden')));
        closeViewModal.addEventListener('click', () => viewModal.classList.add('hidden'));

        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
            if (e.target === viewModal) viewModal.classList.add('hidden');
        });
        
        // --- AJAX Form Submission (FIXED) ---
        jobForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(jobForm);

            fetch('job_posting.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload(); // Reload to see changes
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                alert('An unexpected error occurred.');
            });
        });

        // Search functionality remains the same...
    });

    // --- Global Functions for Edit, View, Delete (FIXED) ---

    // EDIT JOB: Fetch data and populate modal
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
                document.getElementById('jobRequirements').value = job.requirements;
                
                document.getElementById('jobModal').classList.remove('hidden');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Fetch error:', error));
    }

    // VIEW JOB: Fetch data and populate view modal
    function viewJob(id) {
        fetch(`job_posting.php?action=get_job&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const job = data.data;
                const detailsHtml = `
                    <div class="space-y-4 text-sm">
                        <div class="grid grid-cols-3 gap-2">
                            <strong class="col-span-1 text-gray-600">Job Title:</strong>
                            <p class="col-span-2 text-gray-800">${job.title}</p>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <strong class="col-span-1 text-gray-600">Position:</strong>
                            <p class="col-span-2 text-gray-800">${job.position}</p>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <strong class="col-span-1 text-gray-600">Location:</strong>
                            <p class="col-span-2 text-gray-800">${job.location}</p>
                        </div>
                         <div class="grid grid-cols-3 gap-2">
                            <strong class="col-span-1 text-gray-600">Platform:</strong>
                            <p class="col-span-2 text-gray-800">${job.platform}</p>
                        </div>
                        <hr>
                        <div>
                            <strong class="text-gray-600">Requirements:</strong>
                            <p class="mt-1 text-gray-800 whitespace-pre-wrap">${job.requirements || 'N/A'}</p>
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

    // DELETE JOB: Submits a hidden form
    function deleteJob(id) {
        if (confirm('Are you sure you want to delete this job posting?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'job_posting.php';
            form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
</body>
</html>