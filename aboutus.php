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

    <div class="container">
        <div class="team">
            <?php
            // Array of team members (can be dynamic later)
            $team = [
                [
                    "name" => "Siegfried Mar Viloria",
                    "role" => "Team Leader/ Developer",
                    "bio" => "Experienced Team Leader and Full-Stack Developer with a strong background in leading cross-functional teams and delivering scalable software solutions. Skilled in Agile methodologies, code architecture, and mentoring developers to reach their full potential.",
                    "photo" => "https://scontent.fmnl4-4.fna.fbcdn.net/v/t39.30808-6/468659955_3820707058193525_3582090457786370381_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=a5f93a&_nc_ohc=VlMoEt5fFsoQ7kNvwHLuzgh&_nc_oc=Adnrfj9G2ja-13YkI96jxS7dcxBsk4TsQTfAU6rjJoZFzXX8FoivtkXUotRRPsXmqJpQyruOqae5lfsHy-9cAc35&_nc_zt=23&_nc_ht=scontent.fmnl4-4.fna&_nc_gid=eEN6M9xLigCtbKPBGyY6ag&oh=00_AfbErZR69Vna__RUJ12ch-jOqy1xfcxewClzTnO8np9d0g&oe=68DC3746",
                ],
                [
                    "name" => "John Lloyd Morales",
                    "role" => "System Analyst",
                    "bio" => "Detail-oriented System Analyst with a strong background in analyzing business requirements and translating them into effective technical solutions. Experienced in system design, process optimization, and bridging the gap between stakeholders and development teams.",
                    "photo" => "https://scontent.fmnl4-6.fna.fbcdn.net/v/t39.30808-6/492405786_29142971938680051_2088275159235501263_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=F6jq0x9YIFMQ7kNvwGdhZde&_nc_oc=AdnRze11Eu6hnl2e_-H_teC0W3qQx8t3-bw6bd-UR4OWZ-qMkPdaeSHdteixYFvG_Lm3rPGsMyUN_5i1RVvwMR93&_nc_zt=23&_nc_ht=scontent.fmnl4-6.fna&_nc_gid=s3vkciayBG_D_96no_xY7A&oh=00_AfZsRv3JOUi8Ccz65NbPbSldp7TrFY0ILxHyuSjwdBTopA&oe=68DB213A",
                ],
                [
                    "name" => "Andy Ferrer",
                    "role" => "Document Specialist",
                    "bio" => "Skilled Document Specialist with expertise in managing, formatting, and maintaining high-quality business documents across various platforms. Proficient in document control, version tracking, and ensuring compliance with organizational and industry standards.",
                    "photo" => "https://scontent.fmnl4-6.fna.fbcdn.net/v/t39.30808-6/488037395_9202214696567940_1476724353504982961_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=833d8c&_nc_ohc=v473BDMp1ZYQ7kNvwE_uoUU&_nc_oc=Adkm0LiIaoo3QNoTrcAhwNYTVXIyZj14mNXoxYg_c3cm49HTZm_Dej8WxavbILww3FsapshSMFkOe1lxV6RTJsIy&_nc_zt=23&_nc_ht=scontent.fmnl4-6.fna&_nc_gid=Q2lhX1tH6xH1HbXju3HNrg&oh=00_AfaeJ1n5ZINWZ86JueyhdeIwYFMjJ7qoj5uTs44Z5fvnDA&oe=68DB4591",
                ],
                [
                    "name" => "Andrea Ilagan",
                    "role" => "Technical Support Analyst",
                    "bio" => "I am a dedicated Technical Support Analyst with experience in diagnosing and resolving hardware, software, and network issues across various platforms. I excel at providing timely and effective support to end-users, ensuring minimal downtime and high customer satisfaction.",
                    "photo" => "https://scontent.fmnl4-7.fna.fbcdn.net/v/t39.30808-6/552178979_816211454297863_4231209233583079242_n.jpg?_nc_cat=104&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=4shnNPS-0dAQ7kNvwEQzipd&_nc_oc=AdlQii70B_ptX4u_RvzpFlYzT6BgiHxivXiCqD1XNhmr-T_7NeEJaA8DxTMV22r1vrxVWOU27NH9SQcpEoCdQ5Vq&_nc_zt=23&_nc_ht=scontent.fmnl4-7.fna&_nc_gid=_RQ-rohvFrALYspv7zCcUg&oh=00_AfbBb3Caiggi3etJdtCZ6aRx6CERJLBLCSaSUcHREDn--A&oe=68DB4F07",
                ],
                [
                    "name" => "Charlotte Achivida",
                    "role" => "Cyber Security Analyst",
                    "bio" => "I am a detail-oriented Cybersecurity Analyst with expertise in identifying vulnerabilities, monitoring threats, and implementing security measures to protect critical systems and data. I specialize in threat analysis, incident response, and compliance with security standards.",
                    "photo" => "https://scontent.fmnl4-1.fna.fbcdn.net/v/t1.15752-9/552297701_1374328944085433_1105416416599965136_n.jpg?_nc_cat=103&ccb=1-7&_nc_sid=9f807c&_nc_ohc=kOp3GXyPapEQ7kNvwHETXq7&_nc_oc=AdmeNZ6_H5XSd411wrGoJrefOPb1Jfr0q-NTZSzOFn2d47k2vqbRJc4js7vfopno7cAsB1loBAxYXD24wlARjtZP&_nc_zt=23&_nc_ht=scontent.fmnl4-1.fna&oh=03_Q7cD3QHI5KU2zXuyyCjs0NIvlKsObWwHCE70gez3EdtROr3wuQ&oe=68FDB0E3"
                ]
            ];

            // Loop through team array and display members
            foreach ($team as $member) {
                echo '<div class="member">';
                echo '<img src="' . $member["photo"] . '" alt="' . $member["name"] . '">';
                echo '<h3>' . $member["name"] . '</h3>';
                echo '<p class="role">' . $member["role"] . '</p>';
                echo '<p class="bio">' . $member["bio"] . '</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <footer>
        &copy; <?= date("Y") ?> Our Company. All rights reserved.
    </footer>

</body>
</html>
