<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - HR Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");

        :root {
            --primary-color: #000;
            --background-light: #f8f9fa;
            --background-card: #ffffff;
            --text-dark: #333;
            --text-light: #f4f4f4;
            --text-muted: #6c757d;
            --shadow-subtle: 0 4px 12px rgba(0, 0, 0, 0.08);
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
            white-space: nowrap;
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
        }
        
        .sidebar-nav a span {
             white-space: nowrap;
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
        }

        .sidebar.close ~ .main-content {
            margin-left: 78px;
        }

        .page-header {
            background: var(--background-card);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }
        
        .menu-toggle {
            display: none; /* Hide by default, show on smaller screens if needed */
            font-size: 24px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        /* Team Section UI */
        .team-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .member-card {
            background-color: var(--background-card);
            border-radius: var(--border-radius);
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow-subtle);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .member-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .member-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 4px solid var(--primary-color);
        }

        .member-card h3 {
            margin: 10px 0 5px;
            color: var(--text-dark);
            font-size: 1.25rem;
        }

        .member-card .role {
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .member-card .bio {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        @media screen and (max-width: 992px) {
            .sidebar {
                left: -260px;
            }
            .sidebar.close {
                left: 0;
                width: 260px;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.close ~ .main-content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>

    <nav class="sidebar" style="border-right: 1px solid #ccc;">
        <div class="sidebar-header">
            <i class='bx bxs-user-detail' style='font-size: 2rem; color: #fff;'></i>
            <h2>HR Admin</h2>
        </div>
        <ul class="sidebar-nav" ">
            <li><a href="./admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="./logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <div class="main-content" style="background-color: #000; ">
        <i class="fa-solid fa-bars menu-toggle"></i>
        
        <header class="page-header">
            <div id="liveDateTime" class="text-lg font-medium text-gray-200" style="display:flex; float: right;"></div>
            <h1>About Us</h1>
            <p>Meet the passionate team behind our success</p>
        </header>

        <?php
        // Define the team array
        $team = [
            [
                "name" => "Siegfried Mar Viloria",
                "role" => "Team Leader / Developer",
                "bio" => "Experienced Team Leader and Full-Stack Developer with a strong background in leading cross-functional teams and delivering scalable software solutions.",
                "photo" => "profile/Viloria.jpeg",
            ],
            [
                "name" => "John Lloyd Morales",
                "role" => "System Analyst",
                "bio" => "Detail-oriented System Analyst with a strong background in analyzing business requirements and translating them into effective technical solutions.",
                "photo" =>"profile/morales.jpeg",
            ],
            [
                "name" => "Andy Ferrer",
                "role" => "Document Specialist",
                "bio" => "Skilled Document Specialist with expertise in managing, formatting, and maintaining high-quality business documents across various platforms.",
                "photo" => "profile/ferrer.jpeg",
            ],
            [
                "name" => "Andrea Ilagan",
                "role" => "Technical Support Analyst",
                "bio" => "A dedicated Technical Support Analyst with experience in diagnosing and resolving hardware, software, and network issues across various platforms.",
                "photo" => "profile/ilagan.jpeg",
            ],
            [
                "name" => "Charlotte Achivida",
                "role" => "Cyber Security Analyst",
                "bio" => "A detail-oriented Cybersecurity Analyst with expertise in identifying vulnerabilities, monitoring threats, and implementing security measures.",
                "photo" => "profile/achivida.jpeg",
            ]
        ];
        ?>

        <div class="team-container">
            <?php foreach ($team as $member): ?>
                <div class="member-card">
                    <img src="<?= htmlspecialchars($member["photo"]) ?>" alt="Photo of <?= htmlspecialchars($member["name"]) ?>">
                    <h3><?= htmlspecialchars($member["name"]) ?></h3>
                    <p class="role"><?= htmlspecialchars($member["role"]) ?></p>
                    <p class="bio"><?= htmlspecialchars($member["bio"]) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const sidebar = document.querySelector(".sidebar");
        const menuToggle = document.querySelector(".menu-toggle");

        // Use 'close' class to represent the open state for mobile to match behavior
        if (menuToggle) {
            menuToggle.addEventListener("click", () => {
                sidebar.classList.toggle("close");
            });
        }

         function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        };
        document.getElementById("liveDateTime").textContent =
            now.toLocaleDateString("en-US", options);
    }

    // run immediately + update every second
    updateDateTime();
    setInterval(updateDateTime, 1000);
    </script>

</body>
</html>