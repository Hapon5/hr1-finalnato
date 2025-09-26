<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR1 - Human Resources Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #d37a15 0%, #b8650f 100%);
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            text-decoration: none;
            color: white;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #f0f0f0;
        }

        .cta-button {
            background: white;
            color: #d37a15;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #d37a15 0%, #b8650f 100%);
            color: white;
            padding: 8rem 0 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,0 1000,100 0,80"/></svg>');
            background-size: cover;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: white;
            color: #d37a15;
            padding: 1rem 2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
            padding: 1rem 2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary:hover {
            background: white;
            color: #d37a15;
            transform: translateY(-3px);
        }

        /* Features Section */
        .features {
            padding: 5rem 0;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #d37a15, #b8650f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Modules Section */
        .modules {
            padding: 5rem 0;
            background: white;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .module-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .module-card:hover {
            background: white;
            border-color: #d37a15;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .module-card i {
            font-size: 2.5rem;
            color: #d37a15;
            margin-bottom: 1rem;
        }

        .module-card h4 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .module-card p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Stats Section */
        .stats {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 4rem 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 700;
            color: #d37a15;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background: #2c3e50;
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: #d37a15;
            margin-bottom: 1rem;
        }

        .footer-section p, .footer-section a {
            color: #bdc3c7;
            text-decoration: none;
            line-height: 1.8;
        }

        .footer-section a:hover {
            color: #d37a15;
        }

        .footer-bottom {
            border-top: 1px solid #34495e;
            padding-top: 1rem;
            text-align: center;
            color: #bdc3c7;
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .modules-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <a href="#" class="logo">HR1</a>
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#modules">Modules</a></li>
                <li><a href="aboutus.php">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <a href="login.php" class="cta-button">Login</a>
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content fade-in-up">
            <h1>Streamline Your HR Operations</h1>
            <p>Comprehensive Human Resources Management System designed to simplify recruitment, performance management, and employee lifecycle processes.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Get Started
                </a>
                <a href="#features" class="btn-secondary">
                    <i class="fas fa-play"></i>
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose HR1?</h2>
                <p>Our comprehensive HR management system provides all the tools you need to manage your workforce effectively and efficiently.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Candidate Management</h3>
                    <p>Streamline your recruitment process with advanced candidate sourcing, tracking, and management tools that help you find the best talent.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Interview Scheduling</h3>
                    <p>Efficiently manage interview schedules with automated booking, reminders, and coordination tools for seamless recruitment workflow.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Performance Tracking</h3>
                    <p>Monitor and evaluate employee performance with comprehensive appraisal systems and real-time analytics for better decision making.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Document Management</h3>
                    <p>Centralized document storage and management system ensuring all HR documents are organized, secure, and easily accessible.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Job Posting</h3>
                    <p>Create and manage job postings across multiple platforms with automated distribution and application tracking capabilities.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Learning & Development</h3>
                    <p>Comprehensive learning management system for employee training, skill development, and career growth initiatives.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="modules" id="modules">
        <div class="container">
            <div class="section-title">
                <h2>HR Management Modules</h2>
                <p>Access all your HR tools from one integrated platform</p>
            </div>
            <div class="modules-grid">
                <a href="Applicant_Tracking.php" class="module-card">
                    <i class="fas fa-user-plus"></i>
                    <h4>Applicant Tracking</h4>
                    <p>Manage job applications and candidate pipeline</p>
                </a>
                <a href="candidate_sourcing_&_tracking.php" class="module-card">
                    <i class="fas fa-search"></i>
                    <h4>Candidate Sourcing</h4>
                    <p>Find and track potential candidates</p>
                </a>
                <a href="Interviewschedule.php" class="module-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h4>Interview Schedule</h4>
                    <p>Schedule and manage interviews</p>
                </a>
                <a href="performance_and_appraisals.php" class="module-card">
                    <i class="fas fa-star"></i>
                    <h4>Performance & Appraisals</h4>
                    <p>Employee performance evaluation</p>
                </a>
                <a href="Documentfiles.php" class="module-card">
                    <i class="fas fa-folder"></i>
                    <h4>Document Files</h4>
                    <p>Manage HR documents and files</p>
                </a>
                <a href="modules/job_posting.php" class="module-card">
                    <i class="fas fa-bullhorn"></i>
                    <h4>Job Posting</h4>
                    <p>Create and manage job postings</p>
                </a>
                <a href="modules/learning.php" class="module-card">
                    <i class="fas fa-book"></i>
                    <h4>Learning Management</h4>
                    <p>Employee training and development</p>
                </a>
                <a href="modules/recognition.php" class="module-card">
                    <i class="fas fa-trophy"></i>
                    <h4>Recognition</h4>
                    <p>Employee recognition and rewards</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Companies Trust HR1</p>
                </div>
                <div class="stat-item">
                    <h3>50K+</h3>
                    <p>Employees Managed</p>
                </div>
                <div class="stat-item">
                    <h3>99.9%</h3>
                    <p>Uptime Guarantee</p>
                </div>
                <div class="stat-item">
                    <h3>24/7</h3>
                    <p>Support Available</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>HR1 System</h3>
                    <p>Comprehensive Human Resources Management System designed to streamline your HR operations and improve workforce management efficiency.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="login.php">Login</a></p>
                    <p><a href="aboutus.php">About Us</a></p>
                    <p><a href="#features">Features</a></p>
                    <p><a href="#modules">Modules</a></p>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p><i class="fas fa-envelope"></i> support@hr1system.com</p>
                    <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Business St, City, State</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date("Y") ?> HR1 System. All rights reserved. | Designed with ❤️ for better HR management</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all feature cards and module cards
        document.querySelectorAll('.feature-card, .module-card, .stat-item').forEach(el => {
            observer.observe(el);
        });

        // Mobile menu toggle (basic implementation)
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const navLinks = document.querySelector('.nav-links');
        
        if (mobileToggle && navLinks) {
            mobileToggle.addEventListener('click', () => {
                navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            });
        }
    </script>
</body>
</html>
