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

// Handle course enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_course'])) {
    $employee_id = $_SESSION['LoginID'] ?? 1;
    $course_id = $_POST['course_id'];

    try {
        $stmt = $Connections->prepare("INSERT INTO course_enrollments (employee_id, course_id, enrollment_date) VALUES (?, ?, NOW())");
        $stmt->execute([$employee_id, $course_id]);
        $success_message = "Successfully enrolled in course!";
    } catch (Exception $e) {
        $error_message = "Failed to enroll in course: " . $e->getMessage();
    }
}

// Handle course completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_course'])) {
    $enrollment_id = $_POST['enrollment_id'];
    
    try {
        $stmt = $Connections->prepare("UPDATE course_enrollments SET completion_date = NOW(), status = 'completed' WHERE id = ?");
        $stmt->execute([$enrollment_id]);
        $success_message = "Course completed successfully!";
    } catch (Exception $e) {
        $error_message = "Failed to mark course as complete: " . $e->getMessage();
    }
}

// Fetch available courses
try {
    $stmt = $Connections->prepare("SELECT * FROM courses WHERE is_active = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $courses = $stmt->fetchAll();
} catch (Exception $e) {
    $courses = [];
    $error_message = "Failed to load courses: " . $e->getMessage();
}

// Fetch employee enrollments
try {
    $employee_id = $_SESSION['LoginID'] ?? 1;
    $stmt = $Connections->prepare("
        SELECT ce.*, c.title, c.description, c.duration, c.category, c.instructor
        FROM course_enrollments ce
        JOIN courses c ON ce.course_id = c.id
        WHERE ce.employee_id = ?
        ORDER BY ce.enrollment_date DESC
    ");
    $stmt->execute([$employee_id]);
    $enrollments = $stmt->fetchAll();
} catch (Exception $e) {
    $enrollments = [];
    $error_message = "Failed to load enrollments: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Management - HR Admin</title>
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

        /* Course Grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .course-card {
            background: var(--background-card);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #ff6b35);
        }

        .course-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .course-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), #ff6b35);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .course-icon i {
            color: white;
            font-size: 20px;
        }

        .course-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .course-category {
            color: #666;
            font-size: 0.9rem;
        }

        .course-description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px 0;
            border-top: 1px solid #e9ecef;
        }

        .course-duration {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 0.9rem;
        }

        .course-duration i {
            margin-right: 5px;
        }

        .course-instructor {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 0.9rem;
        }

        .course-instructor i {
            margin-right: 5px;
        }

        .enroll-button {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .enroll-button:hover {
            background: #b8650f;
        }

        .enroll-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* My Courses Section */
        .my-courses {
            background: var(--background-card);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
        }

        .my-courses h3 {
            color: var(--text-dark);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .enrollment-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }

        .enrollment-item:hover {
            background-color: #f8f9fa;
        }

        .enrollment-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .enrollment-status.in-progress {
            background-color: #ffc107;
        }

        .enrollment-status.completed {
            background-color: #28a745;
        }

        .enrollment-info {
            flex-grow: 1;
        }

        .enrollment-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .enrollment-date {
            color: #666;
            font-size: 0.9rem;
        }

        .enrollment-actions {
            display: flex;
            gap: 10px;
        }

        .complete-button {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .complete-button:hover {
            background: #218838;
        }

        .complete-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #999;
        }

        /* Progress Bar */
        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), #ff6b35);
            transition: width 0.3s ease;
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
            
            .dashboard-header h1 {
                font-size: 2rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
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
            <li><a href="performance_and_appraisals.php"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
            <li><a href="recognition.php"><i class="fas fa-trophy"></i><span>Recognition</span></a></li>
            <li><a href="../logout.php" id="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="top-navbar">
            <i class="fa-solid fa-bars menu-toggle"></i>
        </div>

        <header class="dashboard-header">
            <h1><i class="fas fa-graduation-cap"></i> Learning Management</h1>
            <p>Enhance your skills and knowledge through our comprehensive learning platform</p>
            
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

        <!-- Available Courses -->
        <div class="courses-grid">
            <?php if (empty($courses)): ?>
                <div class="empty-state">
                    <i class="fas fa-book"></i>
                    <h3>No Courses Available</h3>
                    <p>Check back later for new learning opportunities!</p>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <div class="course-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div>
                                <div class="course-title"><?php echo htmlspecialchars($course['title']); ?></div>
                                <div class="course-category"><?php echo htmlspecialchars($course['category']); ?></div>
                            </div>
                        </div>
                        
                        <div class="course-description">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </div>
                        
                        <div class="course-meta">
                            <div class="course-duration">
                                <i class="fas fa-clock"></i>
                                <?php echo htmlspecialchars($course['duration']); ?> hours
                            </div>
                            <div class="course-instructor">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($course['instructor']); ?>
                            </div>
                        </div>
                        
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <button type="submit" name="enroll_course" class="enroll-button">
                                <i class="fas fa-plus"></i> Enroll Now
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- My Courses -->
        <div class="my-courses">
            <h3><i class="fas fa-user-graduate"></i> My Learning Progress</h3>
            
            <?php if (empty($enrollments)): ?>
                <div class="empty-state">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>No Enrollments Yet</h3>
                    <p>Enroll in courses above to start your learning journey!</p>
                </div>
            <?php else: ?>
                <?php foreach ($enrollments as $enrollment): ?>
                    <div class="enrollment-item">
                        <div class="enrollment-status <?php echo $enrollment['status']; ?>"></div>
                        <div class="enrollment-info">
                            <div class="enrollment-title"><?php echo htmlspecialchars($enrollment['title']); ?></div>
                            <div class="enrollment-date">
                                Enrolled: <?php echo date('M j, Y', strtotime($enrollment['enrollment_date'])); ?>
                                <?php if ($enrollment['completion_date']): ?>
                                    | Completed: <?php echo date('M j, Y', strtotime($enrollment['completion_date'])); ?>
                                <?php endif; ?>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $enrollment['status'] === 'completed' ? '100' : '50'; ?>%"></div>
                            </div>
                        </div>
                        <div class="enrollment-actions">
                            <?php if ($enrollment['status'] !== 'completed'): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['id']; ?>">
                                    <button type="submit" name="complete_course" class="complete-button">
                                        <i class="fas fa-check"></i> Mark Complete
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #28a745; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
    </script>
</body>
</html>