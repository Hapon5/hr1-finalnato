<?php
session_start();
include("../Connections.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['Email']) || (isset($_SESSION['Account_type']) && $_SESSION['Account_type'] !== '1')) {
    header("Location: ../login.php");
    exit();
}

// --- AJAX HANDLER: Get Appraisal Details for Editing ---
if (isset($_GET['action']) && $_GET['action'] == 'get_appraisal' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    try {
        $stmt = $conn->prepare("SELECT a.id, a.employee_id, a.rating, a.comment, e.name as employee_name FROM appraisals a JOIN employees e ON a.employee_id = e.id WHERE a.id = ?");
        $stmt->execute([$_GET['id']]);
        $appraisal = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($appraisal ? ['status' => 'success', 'data' => $appraisal] : ['status' => 'error', 'message' => 'Appraisal not found']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// --- AJAX HANDLER: Add / Update / Delete ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    
    try {
        if ($action === 'add_or_edit') {
            $employee_id = $_POST['employee_id'];
            $rating = $_POST['rating'];
            $comment = trim($_POST['comment']);
            $rater_email = $_SESSION['Email'];
            $appraisal_id = $_POST['appraisal_id'] ?? null;

            if (empty($employee_id) || empty($rating)) {
                echo json_encode(['status' => 'error', 'message' => 'Employee and rating are required.']);
                exit();
            }

            if ($appraisal_id) { // This is an UPDATE
                $stmt = $conn->prepare("UPDATE appraisals SET rating = ?, comment = ? WHERE id = ?");
                $stmt->execute([$rating, $comment, $appraisal_id]);
                $message = 'Appraisal updated successfully!';
            } else { // This is an INSERT
                $stmt = $conn->prepare("INSERT INTO appraisals (employee_id, rater_email, rating, comment, appraisal_date) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$employee_id, $rater_email, $rating, $comment]);
                $message = 'Appraisal added successfully!';
            }
            echo json_encode(['status' => 'success', 'message' => $message]);

        } elseif ($action === 'delete') {
            $appraisal_id = $_POST['appraisal_id'];
            if(empty($appraisal_id)) {
                 echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
                 exit();
            }
            $stmt = $conn->prepare("DELETE FROM appraisals WHERE id = ?");
            $stmt->execute([$appraisal_id]);
            echo json_encode(['status' => 'success', 'message' => 'Appraisal deleted successfully!']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database operation failed: ' . $e->getMessage()]);
    }
    exit();
}

// --- INITIAL PAGE LOAD ---
// Fetch appraisals and employees
try {
    // Fetch all active employees for the dropdown in the modal
    $employeeStmt = $conn->query("SELECT id, name FROM employees WHERE status = 'active' ORDER BY name");
    $employees = $employeeStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all appraisals to display in the table
    $appraisalStmt = $conn->query(
        "SELECT a.id, e.name as employee_name, e.position, a.rater_email, a.rating, a.comment, a.appraisal_date 
         FROM appraisals a 
         JOIN employees e ON a.employee_id = e.id 
         ORDER BY a.appraisal_date DESC"
    );
    $appraisals = $appraisalStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $employees = [];
    $appraisals = [];
    $error_message = "Failed to load data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance & Appraisals - HR Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --primary-color: #d37a15; --background-light: #f8f9fa; --text-light: #f4f4f4; }
        body { background-color: var(--background-light); display: flex; font-family: "Poppins", sans-serif; }
        .sidebar { width: 260px; background-color: var(--primary-color); position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); }
        .sidebar-nav a { color: var(--text-light); }
        .sidebar-nav a:hover { background-color: rgba(0,0,0,0.2); }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; }
        .star-rating input[type="radio"] { display: none; }
        .star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
        .star-rating input[type="radio"]:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <nav class="sidebar p-5 text-white flex flex-col">
        <div class="sidebar-header flex items-center pb-5 border-b border-white/20">
            <i class='fas fa-user-shield text-3xl'></i>
            <h2 class="text-xl font-bold ml-3">HR Admin</h2>
        </div>
        <ul class="sidebar-nav flex-grow pt-5 space-y-2">
            <li><a href="../admin.php" class="flex items-center p-3 rounded-lg"><i class="fas fa-tachometer-alt w-6 text-center"></i><span class="ml-3">Dashboard</span></a></li>
        </ul>
        <div class="pt-5"><a href="../logout.php" class="flex items-center p-3 rounded-lg"><i class="fas fa-sign-out-alt w-6 text-center"></i><span class="ml-3">Logout</span></a></div>
    </nav>

    <!-- Main Content -->
    <div class="main-content p-6">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Performance Management</h1>
            <p class="text-gray-600">Track and manage employee performance appraisals.</p>
        </header>

        <!-- Action Bar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <button id="addAppraisalBtn" class="px-6 py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600">
                <i class="fas fa-plus mr-2"></i> Add New Appraisal
            </button>
        </div>

        <!-- Appraisals Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rater</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($appraisals)): ?>
                        <tr><td colspan="5" class="text-center py-10 text-gray-500">No appraisals found.</td></tr>
                    <?php else: foreach ($appraisals as $appraisal): ?>
                        <tr id="appraisal-row-<?= $appraisal['id'] ?>">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($appraisal['employee_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($appraisal['position']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($appraisal['rater_email']) ?></td>
                            <td class="px-6 py-4 text-center text-yellow-500"><?= str_repeat('★', $appraisal['rating']) . str_repeat('☆', 5 - $appraisal['rating']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-800"><?= date('M d, Y', strtotime($appraisal['appraisal_date'])) ?></td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <button onclick="openEditModal(<?= $appraisal['id'] ?>)" class="text-blue-500 hover:text-blue-700">Edit</button>
                                <button onclick="deleteAppraisal(<?= $appraisal['id'] ?>)" class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="appraisalModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-5 border-b flex justify-between items-center">
                    <h3 id="modalTitle" class="text-xl font-semibold">Add New Appraisal</h3>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form id="appraisalForm" class="p-6">
                    <input type="hidden" name="action" id="formAction" value="add_or_edit">
                    <input type="hidden" name="appraisal_id" id="appraisalId">
                    
                    <div class="mb-4">
                        <label for="employeeSelect" class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                        <select id="employeeSelect" name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select an employee...</option>
                            <?php foreach($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="employeeNameDisplay" class="hidden mt-2 p-2 bg-gray-100 rounded-lg"></div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-center text-sm font-medium text-gray-700 mb-2">Performance Rating</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                            <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                            <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                            <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                            <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                        <textarea name="comment" id="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Provide feedback..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-5 border-t">
                        <button type="button" id="cancelBtn" class="px-5 py-2 bg-gray-200 rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-brand-500 text-white rounded-lg">Save Appraisal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('appraisalModal');
    const form = document.getElementById('appraisalForm');
    const addBtn = document.getElementById('addAppraisalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const employeeSelect = document.getElementById('employeeSelect');
    const employeeNameDisplay = document.getElementById('employeeNameDisplay');

    const openModal = () => modal.classList.remove('hidden');
    const closeModal = () => modal.classList.add('hidden');

    addBtn.addEventListener('click', () => {
        form.reset();
        document.getElementById('modalTitle').textContent = 'Add New Appraisal';
        document.getElementById('appraisalId').value = '';
        employeeSelect.style.display = 'block';
        employeeNameDisplay.style.display = 'none';
        openModal();
    });

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    window.openEditModal = function(id) {
        fetch(`?action=get_appraisal&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                const appraisal = data.data;
                form.reset();
                document.getElementById('modalTitle').textContent = 'Edit Appraisal';
                document.getElementById('appraisalId').value = appraisal.id;
                document.getElementById('employeeSelect').value = appraisal.employee_id;
                document.getElementById('comment').value = appraisal.comment;
                
                // Set stars
                const starInput = form.querySelector(`input[name="rating"][value="${appraisal.rating}"]`);
                if(starInput) starInput.checked = true;

                // Show employee name instead of dropdown for editing
                employeeSelect.style.display = 'none';
                employeeNameDisplay.textContent = appraisal.employee_name;
                employeeNameDisplay.style.display = 'block';

                openModal();
            } else {
                alert(data.message);
            }
        });
    }

    window.deleteAppraisal = function(id) {
        if (!confirm('Are you sure you want to delete this appraisal?')) return;
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('appraisal_id', id);

        fetch('', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') {
                document.getElementById(`appraisal-row-${id}`).remove();
            }
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!form.rating.value) {
            alert('Please select a star rating.');
            return;
        }
        const formData = new FormData(this);
        fetch('', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                window.location.reload();
            }
        });
    });
});
</script>
</body>
</html>

