<?php
session_start();

// Robust include path for Connections.php across environments
$connectionsPath = __DIR__ . DIRECTORY_SEPARATOR . 'Connections.php';
if (!file_exists($connectionsPath)) {
    $connectionsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Connections.php';
}
require_once $connectionsPath; // provides $Connections (PDO)

// PHP 7 compatibility for str_starts_with
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, 0, strlen($needle)) === $needle;
    }
}

$Email = $Password = "";
$EmailErr = $passwordErr = "";
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Email"])) {
        $EmailErr = "Email is required";
    } else {
        $Email = strtolower(trim($_POST["Email"]));
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $EmailErr = "Please enter a valid email address";
        }
    }

    if (empty($_POST["Password"])) {
        $passwordErr = "Password is required";
    } else {
        $Password = trim($_POST["Password"]);
    }

    if (empty($EmailErr) && empty($passwordErr)) {
        try {
            $stmt = $Connections->prepare("SELECT Email, Password, Account_type FROM logintbl WHERE Email = :email LIMIT 1");
            $stmt->execute(['email' => $Email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $dbPassword = (string)$user['Password'];
            $dbAccountType = (string)$user['Account_type'];

            $passwordMatches = false;
            if (!empty($dbPassword)) {
                // Support hashed passwords if used; fallback to plain match for current data
                if (strlen($dbPassword) > 20 && str_starts_with($dbPassword, '$')) {
                    $passwordMatches = password_verify($Password, $dbPassword);
                } else {
                    $passwordMatches = hash_equals($dbPassword, $Password);
                }
            }

            if ($passwordMatches) {
                session_regenerate_id(true);
                $_SESSION['Email'] = $user['Email'];
                $_SESSION['Account_type'] = $dbAccountType;

                if ($dbAccountType === '1') {
                    header('Location: admin.php');
                    exit();
                }
                header('Location: landing.php');
                exit();
                } else {
                    $passwordErr = "Incorrect password";
                }
            } else {
                $EmailErr = "Email is not registered";
            }
        } catch (Exception $e) {
            $loginError = "Login failed. Please try again.";
        }
    }
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR1 - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        brand: {
                            500: '#d37a15',
                            600: '#b8650f'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 0.6s ease-out;
        }
        
        input:focus {
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center p-4 font-sans">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"25\" cy=\"25\" r=\"1\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"75\" cy=\"75\" r=\"1\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"50\" cy=\"10\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"10\" cy=\"60\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"90\" cy=\"40\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');"></div>
    </div>

    <div class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
        <div class="flex flex-col lg:flex-row min-h-[600px]">
            <!-- Login Form -->
            <div class="flex-1 p-8 lg:p-12 flex flex-col justify-center">
                <div class="max-w-md mx-auto w-full">
                    <!-- Logo/Header -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-500 rounded-full mb-4">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">HR1 System</h1>
                        <p class="text-gray-600">Sign in to your account</p>
                    </div>

                    <!-- Error Message -->
                    <?php if (!empty($loginError)): ?>
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                <span class="text-red-700"><?php echo htmlspecialchars($loginError); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
                        <!-- Email Field -->
                        <div>
                            <label for="Email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    id="Email"
                                    name="Email" 
                                    value="<?php echo htmlspecialchars($Email); ?>"
                                    placeholder="Enter your email" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors <?php echo !empty($EmailErr) ? 'border-red-500' : ''; ?>"
                                    required
                                >
                            </div>
                            <?php if (!empty($EmailErr)): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    <?php echo htmlspecialchars($EmailErr); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="Password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="Password"
                                    name="Password" 
                                    placeholder="Enter your password" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors <?php echo !empty($passwordErr) ? 'border-red-500' : ''; ?>"
                                    required
                                >
                            </div>
                            <?php if (!empty($passwordErr)): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    <?php echo htmlspecialchars($passwordErr); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-brand-500 focus:ring-brand-500 border-gray-300 rounded">
                                <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                            </div>
                            <div class="text-sm">
                                <a href="#" class="font-medium text-brand-500 hover:text-brand-600 transition-colors">Forgot password?</a>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-brand-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-brand-600 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>
                    </form>

                    <!-- Back to Landing -->
                    <div class="mt-6 text-center">
                        <a href="landing.php" class="text-sm text-gray-600 hover:text-brand-500 transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="lg:w-1/2 bg-gradient-to-br from-brand-500 to-brand-600 p-8 lg:p-12 flex flex-col justify-center text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"1\" fill=\"white\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>');"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="text-center lg:text-left">
                        <h2 class="text-4xl font-bold mb-6">Welcome to HR1</h2>
                        <p class="text-xl mb-8 text-white/90 leading-relaxed">
                            Streamline your human resources operations with our comprehensive management system.
                        </p>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-users text-sm"></i>
                                </div>
                                <span>Manage employees and candidates</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-chart-line text-sm"></i>
                                </div>
                                <span>Track performance and analytics</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-calendar-check text-sm"></i>
                                </div>
                                <span>Schedule interviews and meetings</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-file-alt text-sm"></i>
                                </div>
                                <span>Organize documents and files</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus email field
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('Email');
            if (emailField && !emailField.value) {
                emailField.focus();
            }
        });

        // Clear errors on input
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const errorMsg = this.parentElement.parentElement.querySelector('.text-red-600');
                if (errorMsg) {
                    errorMsg.style.display = 'none';
                }
                this.classList.remove('border-red-500');
            });
        });
    </script>
</body>
</html>