<?php
session_start();

// Include database connection
$pathsToTry = [
    __DIR__ . '/../Connections.php',
    __DIR__ . '/Connections.php'
];

$connectionsIncluded = false;
foreach ($pathsToTry as $path) {
    if (file_exists($path)) {
        require_once $path;
        $connectionsIncluded = true;
        break;
    }
}

if (!$connectionsIncluded || !isset($Connections)) {
    die("Critical Error: Unable to load database connection.");
}

// Check if user is logged in
if (!isset($_SESSION['Email'])) {
    header('Location: ../login.php');
    exit;
}

$success_message = '';
$error_message = '';

// Handle appraisal submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_appraisal'])) {
    $employee_id = $_POST['employee_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $rater_id = $_SESSION['LoginID'] ?? 1; // Assuming admin is rater, fallback to 1

    // Validate inputs
    if (empty($employee_id) || empty($rating) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        $error_message = "Please provide a valid rating (1-5) and select an employee.";
    } else {
        try {
            $stmt = $Connections->prepare("INSERT INTO appraisals (employee_id, rater_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$employee_id, $rater_id, $rating, $comment]);
            $success_message = "Appraisal submitted successfully!";
        } catch (Exception $e) {
            $error_message = "Failed to submit appraisal: " . $e->getMessage();
        }
    }
}

// Fetch employees
try {
    $stmt = $Connections->query("SELECT * FROM employees WHERE status = 'active' ORDER BY name");
    $employees = $stmt->fetchAll();
} catch (Exception $e) {
    $employees = [];
    $error_message = "Failed to load employees: " . $e->getMessage();
}

// Fetch recent appraisals for display
try {
    $stmt = $Connections->query("
        SELECT a.*, e.name as employee_name, e.position, e.photo_path 
        FROM appraisals a 
        JOIN employees e ON a.employee_id = e.id 
        ORDER BY a.appraisal_date DESC 
        LIMIT 10
    ");
    $recent_appraisals = $stmt->fetchAll();
} catch (Exception $e) {
    $recent_appraisals = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance & Appraisals - HR Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");
        
        :root {
            --primary-color: #d37a15;
            --secondary-color: #0a0a0a;
            --background-light: #f8f9fa;
            --background-card: #ffffff;
            --text-dark: #333;
            --text-light: #f4f4f4;
            --shadow-subtle: 0 4px 12px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
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
        
        /* Sidebar Styles */
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
            color: var(--text-light);
            transition: background-color 0.3s ease;
        }
        
        .sidebar-nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
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

        /* Main Content */
        .main-content {
            margin-left: 260px;
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

        /* Header */
        .dashboard-header {
            background: var(--background-card);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            color: var(--text-dark);
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            color: #666;
            font-size: 1.1rem;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Search Bar */
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto 30px;
        }

        .search-wrapper input {
            width: 100%;
            padding: 15px 50px 15px 50px;
            border-radius: 25px;
            border: 2px solid #e9ecef;
            outline: none;
            font-size: 16px;
            box-shadow: var(--shadow-subtle);
            transition: border-color 0.3s ease;
        }

        .search-wrapper input:focus {
            border-color: var(--primary-color);
        }

        .search-wrapper .search-icon {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            color: #888;
            font-size: 18px;
        }

        /* Employee Grid */
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .employee-card {
            background: var(--background-card);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .employee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .employee-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid var(--primary-color);
        }

        .employee-card h3 {
            color: var(--text-dark);
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .employee-card .position {
            color: #666;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .employee-card .employee-id {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .employee-card .rating-preview {
            display: flex;
            justify-content: center;
            gap: 3px;
            margin-bottom: 15px;
        }

        .employee-card .rating-preview i {
            color: #ddd;
            font-size: 16px;
        }

        .employee-card .rating-preview i.filled {
            color: #ffc107;
        }

        .rate-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .rate-button:hover {
            background: #b8650f;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: var(--background-card);
            padding: 40px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-content .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
        }

        .modal-content .close:hover {
            color: #333;
        }

        .modal-content img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 20px;
            border: 4px solid var(--primary-color);
        }

        .modal-content h2 {
            text-align: center;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .modal-content .employee-info {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
        }

        .rating-section {
            margin-bottom: 25px;
        }

        .rating-section label {
            display: block;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .star-rating {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 32px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
            margin: 0;
        }

        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover {
            color: #ffc107;
        }

        .comment-section {
            margin-bottom: 25px;
        }

        .comment-section label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .comment-section textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        .comment-section textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .submit-button {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background: #b8650f;
        }

        .submit-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Recent Appraisals */
        .recent-appraisals {
            background: var(--background-card);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
        }

        .recent-appraisals h3 {
            color: var(--text-dark);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .appraisal-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        .appraisal-item:hover {
            background-color: #f8f9fa;
        }

        .appraisal-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }

        .appraisal-item .info {
            flex-grow: 1;
        }

        .appraisal-item .name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .appraisal-item .position {
            color: #666;
            font-size: 0.9rem;
        }

        .appraisal-item .rating {
            display: flex;
            gap: 2px;
        }

        .appraisal-item .rating i {
            color: #ffc107;
            font-size: 14px;
        }

        .appraisal-item .date {
            color: #888;
            font-size: 0.8rem;
        }

        /* Responsive Design */
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
            
            .employee-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class='bx bxs-user-detail' style='font-size: 2rem; color: #fff;'></i>
            <h2>HR Admin</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="../admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="../logout.php" id="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="top-navbar">
            <i class="fa-solid fa-bars menu-toggle"></i>
        </div>

        <header class="dashboard-header">
            <h1><i class="fas fa-chart-line"></i> Performance & Appraisals</h1>
            <p>Manage employee performance reviews and appraisals</p>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="search-wrapper">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="searchBar" placeholder="Search employees by name or position...">
        </div>

        <div class="employee-grid">
            <?php foreach ($employees as $employee): ?>
                <div class="employee-card" data-name="<?php echo htmlspecialchars(strtolower($employee['name'] . ' ' . $employee['position'])); ?>" 
                     onclick="openModal(<?php echo $employee['id']; ?>, '<?php echo htmlspecialchars($employee['name']); ?>', '<?php echo htmlspecialchars($employee['position']); ?>', '<?php echo htmlspecialchars($employee['photo_path']); ?>')">
                    <img src="<?php echo htmlspecialchars($employee['photo_path']); ?>" alt="Employee Photo" onerror="this.src='https://via.placeholder.com/100x100?text=No+Photo'">
                    <h3><?php echo htmlspecialchars($employee['name']); ?></h3>
                    <p class="position"><?php echo htmlspecialchars($employee['position']); ?></p>
                    <p class="employee-id">ID: <?php echo str_pad($employee['id'], 3, '0', STR_PAD_LEFT); ?></p>
                    <div class="rating-preview">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                    </div>
                    <button class="rate-button">Rate Performance</button>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($recent_appraisals)): ?>
        <div class="recent-appraisals">
            <h3><i class="fas fa-history"></i> Recent Appraisals</h3>
            <?php foreach ($recent_appraisals as $appraisal): ?>
                <div class="appraisal-item">
                    <img src="<?php echo htmlspecialchars($appraisal['photo_path']); ?>" alt="Employee Photo" onerror="this.src='https://via.placeholder.com/50x50?text=No+Photo'">
                    <div class="info">
                        <div class="name"><?php echo htmlspecialchars($appraisal['employee_name']); ?></div>
                        <div class="position"><?php echo htmlspecialchars($appraisal['position']); ?></div>
                    </div>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa fa-star<?php echo $i <= $appraisal['rating'] ? '' : '-o'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <div class="date"><?php echo date('M j, Y', strtotime($appraisal['appraisal_date'])); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Modal -->
        <div id="employeeModal" class="modal">
            <form method="post" action="">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <img id="modalPhoto" src="" alt="Employee Photo" onerror="this.src='https://via.placeholder.com/120x120?text=No+Photo'">
                    <h2 id="modalName"></h2>
                    <div class="employee-info">
                        <p><strong>ID:</strong> <span id="modalID"></span></p>
                        <p><strong>Position:</strong> <span id="modalPosition"></span></p>
                        <p><strong>Status:</strong> Active</p>
                    </div>

                    <input type="hidden" name="employee_id" id="modalEmployeeId">

                    <div class="rating-section">
                        <label>Rate Employee Performance:</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" value="5" id="star5">
                            <label for="star5">★</label>
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4">★</label>
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3">★</label>
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2">★</label>
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1">★</label>
                        </div>
                    </div>

                    <div class="comment-section">
                        <label for="comment">Add Comment:</label>
                        <textarea name="comment" id="comment" rows="4" placeholder="Write your performance review comment here..."></textarea>
                    </div>

                    <button type="submit" name="submit_appraisal" class="submit-button">
                        <i class="fas fa-paper-plane"></i> Submit Appraisal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        const sidebar = document.querySelector(".sidebar");
        const menuToggle = document.querySelector(".menu-toggle");
        
        if (menuToggle) {
            menuToggle.addEventListener("click", () => {
                sidebar.classList.toggle("close");
            });
        }

        // Logout functionality
        document.getElementById("logout-link").addEventListener("click", function (e) {
            e.preventDefault();
            localStorage.clear();
            window.location.href = "../logout.php";
        });

        // Modal functionality
        function openModal(employeeId, name, position, photoPath) {
            document.getElementById("modalEmployeeId").value = employeeId;
            document.getElementById("modalName").textContent = name;
            document.getElementById("modalID").textContent = String(employeeId).padStart(3, '0');
            document.getElementById("modalPosition").textContent = position;
            document.getElementById("modalPhoto").src = photoPath;
            
            // Reset form
            document.querySelector('form').reset();
            
            document.getElementById("employeeModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("employeeModal").style.display = "none";
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById("employeeModal");
            if (event.target === modal) {
                closeModal();
            }
        };

        // Search functionality
        const searchBar = document.getElementById("searchBar");
        searchBar.addEventListener("input", function () {
            const query = searchBar.value.toLowerCase();
            const cards = document.querySelectorAll(".employee-card");

            cards.forEach(card => {
                const searchData = card.getAttribute("data-name");
                if (searchData.includes(query)) || query === "") {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        });

        // Star rating interaction
        document.querySelectorAll('.star-rating input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const rating = this.value;
                const labels = document.querySelectorAll('.star-rating label');
                
                labels.forEach((label, index) => {
                    if (index < rating) {
                        label.style.color = '#ffc107';
                    } else {
                        label.style.color = '#ddd';
                    }
                });
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const rating = document.querySelector('input[name="rating"]:checked');
            if (!rating) {
                e.preventDefault();
                alert('Please select a rating before submitting.');
                return false;
            }
        });
    </script>
</body>
</html>