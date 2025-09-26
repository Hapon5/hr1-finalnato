<?php
session_start();
require_once "Connections.php";

// Require admin
if (!isset($_SESSION['Email']) || $_SESSION['Account_type'] !== '1') {
    header('Location: login.php');
    exit();
}
$admin_email = $_SESSION['Email'];

// Ensure table exists
try {
    $Connections->exec(
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

// Handle actions
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'add') {
            $stmt = $Connections->prepare(
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
            $stmt = $Connections->prepare(
                'UPDATE interviews SET candidate_name=?, email=?, position=?, interviewer=?, start_time=?, end_time=?, location=?, status=?, notes=? WHERE id=?'
            );
            $stmt->execute([
                trim($_POST['candidate_name']),
                trim($_POST['email']),
                trim($_POST['position']),
                trim($_POST['interviewer']),
                $_POST['start_time'],
                $_POST['end_time'],
                trim($_POST['location']),
                $_POST['status'],
                $_POST['notes'] ?? '',
                (int)$_POST['id']
            ]);
            $message = 'Interview updated';
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $Connections->prepare('DELETE FROM interviews WHERE id=?');
            $stmt->execute([(int)$_POST['id']]);
            $message = 'Interview deleted';
        }
    } catch (Throwable $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch data
try {
    $filter = isset($_GET['status']) ? $_GET['status'] : '';
    $q = 'SELECT * FROM interviews';
    $params = [];
    if ($filter !== '') {
        $q .= ' WHERE status = ?';
        $params[] = $filter;
    }
    $q .= ' ORDER BY start_time DESC';
    $stmt = $Connections->prepare($q);
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
</head>
<body class="bg-gray-50 font-sans">
    <!-- Sidebar (simple) -->
    <div class="fixed inset-y-0 left-0 z-40 w-64 bg-brand-500 text-white hidden lg:block">
        <div class="h-16 flex items-center px-6 bg-brand-600">
            <i class="fas fa-users mr-3"></i>
            <span class="font-semibold">HR1</span>
        </div>
        <div class="p-6 space-y-2">
            <a class="flex items-center hover:text-yellow-300" href="admin.php"><i class="fas fa-arrow-left mr-2"></i>Back to Dashboard</a>
            <a class="flex items-center hover:bg-brand-600 rounded px-3 py-2" href="modules/job_posting.php"><i class="fas fa-bullhorn mr-2"></i>Job Posting</a>
            <a class="flex items-center hover:bg-brand-600 rounded px-3 py-2" href="candidate_sourcing_&_tracking.php"><i class="fas fa-users mr-2"></i>Candidates</a>
            <a class="flex items-center bg-brand-600 rounded px-3 py-2" href="Interviewschedule.php"><i class="fas fa-calendar-alt mr-2"></i>Interviews</a>
            <a class="flex items-center hover:bg-red-600 rounded px-3 py-2" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </div>
    </div>

    <div class="lg:ml-64">
        <div class="bg-white border-b">
            <div class="px-6 h-16 flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-800">Interview Scheduling</h1>
                <div class="text-gray-600">Welcome, <?php echo htmlspecialchars($admin_email); ?></div>
            </div>
        </div>

        <div class="p-6">
            <?php if ($message): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <button id="openModal" class="inline-flex items-center px-6 py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600">
                        <i class="fas fa-plus mr-2"></i> Schedule Interview
                    </button>
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-gray-600">Status</label>
                        <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                            <option value="">All</option>
                            <option value="scheduled" <?php echo (isset($_GET['status']) && $_GET['status']==='scheduled')?'selected':''; ?>>Scheduled</option>
                            <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status']==='completed')?'selected':''; ?>>Completed</option>
                            <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status']==='cancelled')?'selected':''; ?>>Cancelled</option>
                            <option value="no_show" <?php echo (isset($_GET['status']) && $_GET['status']==='no_show')?'selected':''; ?>>No-show</option>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($iv['candidate_name']); ?><div class="text-gray-500"><?php echo htmlspecialchars($iv['email']); ?></div></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($iv['position']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($iv['interviewer']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('M d, Y g:i A', strtotime($iv['start_time'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('M d, Y g:i A', strtotime($iv['end_time'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($iv['location']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php
                                            echo $iv['status']==='scheduled'?'bg-blue-100 text-blue-800':($iv['status']==='completed'?'bg-green-100 text-green-800':($iv['status']==='cancelled'?'bg-red-100 text-red-800':'bg-yellow-100 text-yellow-800'));
                                        ?>"><?php echo ucfirst(str_replace('_',' ',$iv['status'])); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button class="text-brand-500 hover:text-brand-600" onclick="openEdit(<?php echo (int)$iv['id']; ?>)"><i class="fas fa-edit"></i></button>
                                            <button class="text-red-500 hover:text-red-600" onclick="confirmDelete(<?php echo (int)$iv['id']; ?>)"><i class="fas fa-trash"></i></button>
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

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black/40 hidden z-50">
        <div class="min-h-screen w-full flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 id="modalTitle" class="text-lg font-semibold">Schedule Interview</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
                </div>
                <form method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="rowId" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Candidate Name</label>
                            <input name="candidate_name" id="candidate_name" type="text" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Email</label>
                            <input name="email" id="email" type="email" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Position</label>
                            <input name="position" id="position" type="text" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Interviewer</label>
                            <input name="interviewer" id="interviewer" type="text" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Start Time</label>
                            <input name="start_time" id="start_time" type="datetime-local" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">End Time</label>
                            <input name="end_time" id="end_time" type="datetime-local" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Location</label>
                            <input name="location" id="location" type="text" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No-show</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"><i class="fas fa-save mr-2"></i>Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId" value="">
    </form>

    <script>
        const modal = document.getElementById('modal');
        const openModalBtn = document.getElementById('openModal');
        const closeModalBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const formAction = document.getElementById('formAction');
        const rowId = document.getElementById('rowId');
        const statusFilter = document.getElementById('statusFilter');

        openModalBtn.addEventListener('click', () => { formAction.value='add'; rowId.value=''; document.getElementById('modalTitle').textContent='Schedule Interview'; modal.classList.remove('hidden'); });
        closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
        cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));

        function openEdit(id){
            formAction.value='edit';
            rowId.value = id;
            document.getElementById('modalTitle').textContent='Edit Interview';
            modal.classList.remove('hidden');
        }
        function confirmDelete(id){
            if(confirm('Delete this interview?')){
                document.getElementById('deleteId').value=id;
                document.getElementById('deleteForm').submit();
            }
        }
        statusFilter.addEventListener('change', ()=>{
            const url=new URL(window.location.href); url.searchParams.set('status', statusFilter.value); window.location.href=url.toString();
        });
    </script>
</body>
</html>