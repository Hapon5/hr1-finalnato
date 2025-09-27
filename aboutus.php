<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - Team Page</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: rgba(255, 255, 255, 0.25);
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #d37a15;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2.5rem;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .team {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .member {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .member img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .member h3 {
            margin: 10px 0 5px;
            color: #2c3e50;
        }

        .member p.role {
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 10px;
        }

        .member p.bio {
            font-size: 0.95rem;
            color: #555;
        }

        footer {
            background-color: #d37a15;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
        }

        @media screen and (max-width: 768px) {
            .team {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

    <header>
        <h1>About Us</h1>
        <p>Meet the passionate team behind our success</p>
    </header>

    <?php
    // Define the team array
    $team = [
        [
            "name" => "Siegfried Mar Viloria",
            "role" => "Team Leader/ Developer",
            "bio" => "Experienced Team Leader and Full-Stack Developer with a strong background in leading cross-functional teams and delivering scalable software solutions. Skilled in Agile methodologies, code architecture, and mentoring developers to reach their full potential.",
            "photo" => "modules/profile/viloria.jpeg",
        ],
        [
            "name" => "John Lloyd Morales",
            "role" => "System Analyst",
            "bio" => "Detail-oriented System Analyst with a strong background in analyzing business requirements and translating them into effective technical solutions. Experienced in system design, process optimization, and bridging the gap between stakeholders and development teams.",
            "photo" => "modules/profile/morales.jpeg",
        ],
        [
            "name" => "Andy Ferrer",
            "role" => "Document Specialist",
            "bio" => "Skilled Document Specialist with expertise in managing, formatting, and maintaining high-quality business documents across various platforms. Proficient in document control, version tracking, and ensuring compliance with organizational and industry standards.",
            "photo" => "modules/profile/ferrer.jpeg",
        ],
        [
            "name" => "Andrea Ilagan",
            "role" => "Technical Support Analyst",
            "bio" => "I am a dedicated Technical Support Analyst with experience in diagnosing and resolving hardware, software, and network issues across various platforms. I excel at providing timely and effective support to end-users, ensuring minimal downtime and high customer satisfaction.",
            "photo" => "modules/profile/ilagan.jpeg",
        ],
        [
            "name" => "Charlotte Achivida",
            "role" => "Cyber Security Analyst",
            "bio" => "I am a detail-oriented Cybersecurity Analyst with expertise in identifying vulnerabilities, monitoring threats, and implementing security measures to protect critical systems and data. I specialize in threat analysis, incident response, and compliance with security standards.",
            "photo" => "modules/profile/achivida.jpeg",
        ]
    ];
    ?>

    <div class="container">
        <div class="team">
            <?php foreach ($team as $member): ?>
                <div class="member">
                    <img src="<?= htmlspecialchars($member["photo"]) ?>" alt="<?= htmlspecialchars($member["name"]) ?>">
                    <h3><?= htmlspecialchars($member["name"]) ?></h3>
                    <p class="role"><?= htmlspecialchars($member["role"]) ?></p>
                    <p class="bio"><?= htmlspecialchars($member["bio"]) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        &copy; <?= date("Y") ?> Our Company. All rights reserved.
    </footer>

</body>
</html>
