<?php
session_start();
include("Connections.php"); // brings in $Connections (PDO object)

$Email = $Password = "";
$EmailErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Email"])) {
        $EmailErr = "Email is required";
    } else {
        $Email = trim($_POST["Email"]);
    }

    if (empty($_POST["Password"])) {
        $passwordErr = "Password is required";
    } else {
        $Password = trim($_POST["Password"]);
    }

    if (empty($EmailErr) && empty($passwordErr)) {
        $stmt = $Connections->prepare("SELECT * FROM logintbl WHERE Email = :email LIMIT 1");
        $stmt->execute(['email' => $Email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $db_Password = $user["Password"];
            $db_Account_type = $user["Account_type"];

            // NOTE: If you store hashed passwords, use password_verify()
            if ($db_Password === $Password) {
                $_SESSION['Email'] = $Email;
                $_SESSION['Account_type'] = $db_Account_type;

                if ($db_Account_type == "1") {
                    header("Location: admin.php");
                    exit();
                } else {
                    header("Location: user.php");
                    exit();
                }
            } else {
                $passwordErr = "Incorrect Password";
            }
        } else {
            $EmailErr = "Email is not registered";
        }
    }
}
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Login</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            height: 100vh; display: flex; justify-content: center; align-items: center;
            background-image: url('crane.jpg'); background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            display: flex; width: 750px; background: #ffffff; border-radius: 14px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3); overflow: hidden;
            animation: fadeSlide 0.7s ease-out;
        }
        @keyframes fadeSlide {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .form-box {
            flex: 1; padding: 40px; display: flex; flex-direction: column;
            justify-content: center; background: #f0f4f8;
        }
        .form-box h2 {
            margin-bottom: 25px; text-align: center; font-size: 28px; color: #9c6410ff;
        }
        .input-box { position: relative; margin-bottom: 20px; }
        .input-box input {
            width: 100%; padding: 10px 10px 10px 35px; font-size: 16px;
            border: 1px solid #ccc; border-radius: 8px; background-color: #fff;
            transition: all 0.3s ease;
        }
        .input-box input:focus {
            border-color: #d37a15ff; box-shadow: 0 0 5px rgba(236,164,56,0.5);
        }
        .input-box i {
            position: absolute; top: 50%; left: 10px; transform: translateY(-50%);
            color: #888;
        }
        .error-msg { color: red; font-size: 14px; margin-top: -10px; margin-bottom: 10px; }
        .btn {
            padding: 12px; width: 100%; background: #d37a15ff; color: #fff;
            border: none; border-radius: 8px; font-size: 16px; cursor: pointer;
            transition: background 0.3s ease; font-weight: bold;
        }
        .btn:hover { background: #b36510; }
        .info-section {
            flex: 1; background: linear-gradient(to bottom right, #d37a15ff, #d37a15ff);
            color: white; padding: 40px 30px; display: flex; flex-direction: column;
            justify-content: center; align-items: center;
        }
        .info-section h2 { font-size: 28px; margin-bottom: 10px; }
        .info-section p { text-align: center; font-size: 15px; line-height: 1.6; }
        @media (max-width: 768px) {
            .login-container { flex-direction: column; width: 90%; }
            .info-section { display: none; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="form-box">
            <h2>Human Resources 1</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="input-box">
                    <i class='bx bxs-user'></i>
                    <input type="email" name="Email" placeholder="Email" required>
                </div>
                <?php if (!empty($EmailErr)) echo "<div class='error-msg'>$EmailErr</div>"; ?>

                <div class="input-box">
                    <i class='bx bxs-lock-alt'></i>
                    <input type="password" name="Password" placeholder="Password" required>
                </div>
                <?php if (!empty($passwordErr)) echo "<div class='error-msg'>$passwordErr</div>"; ?>

                <button type="submit" class="btn">Login</button>
            </form>
        </div>
        <div class="info-section">
            <h2>Human Resources</h2>
            <p>"People-First Strategies for Tomorrow's Workforce," "Turning Human Potential into HR Reality," or "Building a Stronger, Smarter, Happier Workplace"</p>
        </div>
    </div>
</body>
</html>