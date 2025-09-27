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
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Poppins', 'sans-serif'] }, colors: { brand: { 500: '#d37a15', 600: '#b8650f' } } } }
        }
    </script>
    <style>
        :root { --primary-color: #d37a15; --background-light: #f8f9fa; --text-light: #f4f4f4; }
        body { background-color: var(--background-light); display: flex; font-family: "Poppins", sans-serif; }
        .sidebar { width: 260px; background-color: var(--primary-color); position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; transition: all 0.3s ease; }
        .main-content { margin-left: 260px; transition: margin-left 0.3s ease; width: calc(100% - 260px); }
        .sidebar-nav a { color: var(--text-light); background-color: transparent; }
        .sidebar-nav a:hover { background-color: rgba(0,0,0,0.2); }
        .modal-body { max-height: 70vh; overflow-y: auto; }
        .star-rating input[type="radio"] { display: none; }
        .star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
        .star-rating input[type="radio"]:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <nav class="sidebar p-5 text-white flex flex-col">
        <div class="sidebar-header flex items-center pb-5 border-b border-white/20">
            <i class='fas fa-user-shield text-3xl'></i>
            <h2 class="text-xl font-bold ml-3 whitespace-nowrap">HR Admin</h2>
        </div>
        <ul class="sidebar-nav flex-grow pt-5 space-y-2">
            <li><a href="../admin.php" class="flex items-center p-3 rounded-lg"><i class="fas fa-tachometer-alt w-6 text-center"></i><span class="ml-3">Dashboard</span></a></li>
        </ul>
        <div class="pt-5"><a href="../logout.php" class="flex items-center p-3 rounded-lg"><i class="fas fa-sign-out-alt w-6 text-center"></i><span class="ml-3">Logout</span></a></div>
    </nav>

     <div class="main-content p-6">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Performance & Appraisals</h1>
            <p class="text-gray-600">Review and rate employee performance</p>
        </header>

        <!-- Employee Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Last Rating</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($employees)): ?>
                            <tr><td colspan="4" class="text-center py-10 text-gray-500">No active employees found.</td></tr>
                        <?php else: foreach ($employees as $employee): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full object-cover" src="../<?= htmlspecialchars($employee['photo_path']) ?>" alt="">
                                        <div class="ml-4 font-medium text-gray-900"><?= htmlspecialchars($employee['name']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($employee['position']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-800">
                                    <?= $employee['last_rating'] ? str_repeat('⭐', $employee['last_rating']) : '<span class="text-gray-400">Not Rated</span>' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="openRateModal(<?= $employee['id'] ?>)" class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600">Rate Performance</button>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="rateModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="flex items-center justify-between p-5 border-b">
                    <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Rate Performance</h3>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form id="appraisalForm" class="modal-body p-6">
                    <input type="hidden" name="action" value="submit_appraisal">
                    <input type="hidden" name="employee_id" id="modalEmployeeId">
                    
                    <div class="text-center mb-6">
                        <img id="modalPhoto" class="h-24 w-24 rounded-full object-cover mx-auto mb-4 border-4 border-brand-500" src="" alt="Employee">
                        <h4 id="modalName" class="font-bold text-lg"></h4>
                        <p id="modalPosition" class="text-gray-600"></p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-center text-sm font-medium text-gray-700 mb-2">Overall Performance Rating</label>
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
                        <button type="button" id="cancelBtn" class="px-5 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600">Submit Appraisal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('rateModal');
    const form = document.getElementById('appraisalForm');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    const openModal = () => modal.classList.remove('hidden');
    const closeModal = () => modal.classList.add('hidden');

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    window.openRateModal = function(employeeId) {
        fetch(`?action=get_employee&id=${employeeId}`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                const emp = data.data;
                document.getElementById('modalEmployeeId').value = emp.id;
                document.getElementById('modalName').textContent = emp.name;
                document.getElementById('modalPosition').textContent = emp.position;
                document.getElementById('modalPhoto').src = `../${emp.photo_path}`;
                form.reset();
                openModal();
            } else {
                alert(data.message);
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

