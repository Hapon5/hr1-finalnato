<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR1 - Human Resources Management System</title>
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
                    },
                    backgroundImage: {
                        'hero-gradient': 'linear-gradient(135deg, #d37a15 0%, #b8650f 100%)',
                        'stats-gradient': 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)'
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans text-gray-800 antialiased">
    <!-- Header -->
    <header class="fixed inset-x-0 top-0 z-50 bg-hero-gradient shadow-lg">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="#" class="text-white text-2xl font-bold flex items-center">
                <i class="fas fa-users mr-2"></i>
                HR1
            </a>
            <button id="mobileToggle" class="text-white text-2xl md:hidden" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <ul id="navLinks" class="hidden gap-8 md:flex">
                <li><a href="#features" class="text-white/90 hover:text-white transition duration-300">Features</a></li>
                <li><a href="#modules" class="text-white/90 hover:text-white transition duration-300">Modules</a></li>
                <li><a href="aboutus.php" class="text-white/90 hover:text-white transition duration-300">About Us</a></li>
                <li><a href="#contact" class="text-white/90 hover:text-white transition duration-300">Contact</a></li>
            </ul>
            <a href="hr1/login.php" class="ml-6 hidden rounded-full bg-white px-6 py-2 font-semibold text-brand-500 shadow hover:bg-gray-100 transition duration-300 md:inline-block">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
            </a>
        </nav>
        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-hero-gradient border-t border-white/20">
            <div class="mx-auto max-w-7xl px-6 py-4">
                <ul class="space-y-3">
                    <li><a href="#features" class="block rounded-lg px-4 py-2 text-white hover:bg-white/10 transition duration-300">Features</a></li>
                    <li><a href="#modules" class="block rounded-lg px-4 py-2 text-white hover:bg-white/10 transition duration-300">Modules</a></li>
                    <li><a href="aboutus.php" class="block rounded-lg px-4 py-2 text-white hover:bg-white/10 transition duration-300">About Us</a></li>
                    <li><a href="#contact" class="block rounded-lg px-4 py-2 text-white hover:bg-white/10 transition duration-300">Contact</a></li>
                    <li class="pt-2">
                        <a href="hr1/login.php" class="inline-flex items-center rounded-full bg-white px-6 py-2 font-semibold text-brand-500 shadow hover:bg-gray-100 transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-hero-gradient pt-24 pb-20 text-white">
        <div class="absolute inset-0 opacity-10">
            <svg viewBox="0 0 1000 100" class="h-full w-full">
                <polygon points="0,0 1000,0 1000,100 0,80" fill="white"/>
            </svg>
        </div>
        <div class="relative z-10 mx-auto max-w-6xl px-6 text-center">
            <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-6">
                Streamline Your <span class="text-yellow-300">HR Operations</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-white/90 max-w-4xl mx-auto leading-relaxed">
                Comprehensive Human Resources Management System designed to simplify recruitment, 
                performance management, and the entire employee lifecycle.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="hr1/login.php" class="inline-flex items-center gap-3 rounded-full bg-white px-8 py-4 text-lg font-semibold text-brand-500 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <i class="fas fa-rocket"></i>
                    Get Started Now
                </a>
                <a href="#features" class="inline-flex items-center gap-3 rounded-full border-2 border-white px-8 py-4 text-lg font-semibold text-white transition-all duration-300 hover:bg-white hover:text-brand-500 hover:-translate-y-1">
                    <i class="fas fa-play"></i>
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">Why Choose HR1?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Our comprehensive HR management system provides all the tools you need to manage your workforce effectively and efficiently.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Candidate Management</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Streamline your recruitment process with advanced candidate sourcing, tracking, and management tools that help you find the best talent.
                    </p>
                </div>
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Interview Scheduling</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Efficiently manage interview schedules with automated booking, reminders, and coordination tools for seamless recruitment workflow.
                    </p>
                </div>
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Performance Tracking</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Monitor and evaluate employee performance with comprehensive appraisal systems and real-time analytics for better decision making.
                    </p>
                </div>
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Document Management</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Centralized document storage and management system ensuring all HR documents are organized, secure, and easily accessible.
                    </p>
                </div>
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-briefcase text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Job Posting</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Create and manage job postings across multiple platforms with automated distribution and application tracking capabilities.
                    </p>
                </div>
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Learning & Development</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Comprehensive learning management system for employee training, skill development, and career growth initiatives.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="py-20 bg-white">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">HR Management Modules</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Access all your HR tools from one integrated platform designed for modern workforce management.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="Applicant_Tracking.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Applicant Tracking</h4>
                    <p class="text-sm text-gray-600">Manage applications and pipeline</p>
                </a>
                <a href="candidate_sourcing_&_tracking.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-search text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Candidate Sourcing</h4>
                    <p class="text-sm text-gray-600">Find and track candidates</p>
                </a>
                <a href="Interviewschedule.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Interview Schedule</h4>
                    <p class="text-sm text-gray-600">Schedule and manage interviews</p>
                </a>
                <a href="performance_and_appraisals.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Performance & Appraisals</h4>
                    <p class="text-sm text-gray-600">Evaluate performance</p>
                </a>
                <a href="Documentfiles.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-folder text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Document Files</h4>
                    <p class="text-sm text-gray-600">Manage documents</p>
                </a>
                <a href="modules/job_posting.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-bullhorn text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Job Posting</h4>
                    <p class="text-sm text-gray-600">Create and manage jobs</p>
                </a>
                <a href="modules/learning.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Learning Management</h4>
                    <p class="text-sm text-gray-600">Training and development</p>
                </a>
                <a href="modules/recognition.php" class="group bg-gray-50 rounded-xl p-6 text-center hover:bg-white hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-2 border-transparent hover:border-brand-500">
                    <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Recognition</h4>
                    <p class="text-sm text-gray-600">Rewards and recognition</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-stats-gradient py-20 text-white">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Trusted by Companies Worldwide</h2>
                <p class="text-xl text-white/90 max-w-3xl mx-auto">
                    Join thousands of organizations that rely on HR1 for their human resources management needs.
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="group">
                    <div class="text-5xl md:text-6xl font-extrabold text-brand-500 mb-2 group-hover:scale-110 transition-transform duration-300">500+</div>
                    <p class="text-lg text-white/90">Companies Trust HR1</p>
                </div>
                <div class="group">
                    <div class="text-5xl md:text-6xl font-extrabold text-brand-500 mb-2 group-hover:scale-110 transition-transform duration-300">50K+</div>
                    <p class="text-lg text-white/90">Employees Managed</p>
                </div>
                <div class="group">
                    <div class="text-5xl md:text-6xl font-extrabold text-brand-500 mb-2 group-hover:scale-110 transition-transform duration-300">99.9%</div>
                    <p class="text-lg text-white/90">Uptime Guarantee</p>
                </div>
                <div class="group">
                    <div class="text-5xl md:text-6xl font-extrabold text-brand-500 mb-2 group-hover:scale-110 transition-transform duration-300">24/7</div>
                    <p class="text-lg text-white/90">Support Available</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 py-16 text-gray-300">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">HR1 System</h3>
                    </div>
                    <p class="text-gray-400 leading-relaxed mb-6 max-w-md">
                        Comprehensive Human Resources Management System designed to streamline HR operations and improve workforce management efficiency.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500 transition-colors duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500 transition-colors duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500 transition-colors duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="hr1/login.php" class="text-gray-400 hover:text-brand-500 transition-colors duration-300">Login</a></li>
                        <li><a href="aboutus.php" class="text-gray-400 hover:text-brand-500 transition-colors duration-300">About Us</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-brand-500 transition-colors duration-300">Features</a></li>
                        <li><a href="#modules" class="text-gray-400 hover:text-brand-500 transition-colors duration-300">Modules</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-6">Contact Info</h4>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-brand-500 mr-3"></i>
                            <span class="text-gray-400">support@hr1system.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-brand-500 mr-3"></i>
                            <span class="text-gray-400">+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt text-brand-500 mr-3"></i>
                            <span class="text-gray-400">123 Business St, City, State</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 HR1 System. All rights reserved. | Designed with ❤️ for better HR management</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const toggle = document.getElementById('mobileToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const navLinks = document.getElementById('navLinks');
        
        if (toggle && mobileMenu) {
            toggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'start' 
                        });
                    }
                    // Close mobile menu on navigation
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.classList.add('backdrop-blur-md', 'bg-brand-500/95');
            } else {
                header.classList.remove('backdrop-blur-md', 'bg-brand-500/95');
            }
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all feature cards and module cards
        document.querySelectorAll('.group').forEach(el => {
            observer.observe(el);
        });
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 0.8s ease-out;
        }
    </style>
</body>
</html>
