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
    <header class="fixed inset-x-0 top-0 z-50 bg-hero-gradient shadow">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3">
            <a href="#" class="text-white text-2xl font-bold">HR1</a>
            <button id="mobileToggle" class="text-white text-2xl md:hidden" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <ul id="navLinks" class="hidden gap-8 md:flex">
                <li><a href="#features" class="text-white/90 hover:text-white transition">Features</a></li>
                <li><a href="#modules" class="text-white/90 hover:text-white transition">Modules</a></li>
                <li><a href="aboutus.php" class="text-white/90 hover:text-white transition">About Us</a></li>
                <li><a href="#contact" class="text-white/90 hover:text-white transition">Contact</a></li>
            </ul>
            <a href="/HR1/login.php" class="ml-6 hidden rounded-full bg-white px-5 py-2 font-semibold text-brand-500 shadow hover:bg-gray-100 md:inline-block">Login</a>
        </nav>
        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden">
            <div class="mx-auto max-w-7xl px-6 pb-4">
                <ul class="space-y-2">
                    <li><a href="#features" class="block rounded px-3 py-2 text-white hover:bg-white/10">Features</a></li>
                    <li><a href="#modules" class="block rounded px-3 py-2 text-white hover:bg-white/10">Modules</a></li>
                    <li><a href="aboutus.php" class="block rounded px-3 py-2 text-white hover:bg-white/10">About Us</a></li>
                    <li><a href="#contact" class="block rounded px-3 py-2 text-white hover:bg-white/10">Contact</a></li>
                    <li><a href="/HR1/login.php" class="mt-2 inline-block rounded-full bg-white px-5 py-2 font-semibold text-brand-500 shadow hover:bg-gray-100">Login</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden bg-hero-gradient pt-28 pb-16 text-white">
        <div class="absolute inset-0 opacity-10">
            <svg viewBox="0 0 1000 100" class="h-full w-full"><polygon points="0,0 1000,0 1000,100 0,80" fill="white"/></svg>
        </div>
        <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
            <h1 class="text-4xl font-bold leading-tight md:text-5xl">Streamline Your HR Operations</h1>
            <p class="mt-4 text-lg/relaxed text-white/90">Comprehensive Human Resources Management System designed to simplify recruitment, performance management, and the entire employee lifecycle.</p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="/HR1/login.php" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-lg font-semibold text-brand-500 shadow transition hover:-translate-y-0.5 hover:bg-gray-100">
                    <i class="fas fa-sign-in-alt"></i>
                    Get Started
                </a>
                <a href="#features" class="inline-flex items-center gap-2 rounded-full border-2 border-white px-6 py-3 text-lg font-semibold text-white transition hover:-translate-y-0.5 hover:bg-white hover:text-brand-500">
                    <i class="fas fa-play"></i>
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="bg-gray-50 py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">Why Choose HR1?</h2>
                <p class="mt-3 text-gray-600">All the tools you need to manage your workforce effectively and efficiently.</p>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl bg-white p-8 shadow transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-3xl text-white"><i class="fas fa-users"></i></div>
                    <h3 class="text-xl font-semibold text-gray-800">Candidate Management</h3>
                    <p class="mt-2 text-gray-600">Streamline recruitment with sourcing, tracking, and management tools to find the best talent.</p>
                </div>
                <div class="rounded-2xl bg-white p-8 shadow transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-3xl text-white"><i class="fas fa-calendar-check"></i></div>
                    <h3 class="text-xl font-semibold text-gray-800">Interview Scheduling</h3>
                    <p class="mt-2 text-gray-600">Automated booking, reminders, and coordination for a seamless recruitment workflow.</p>
                </div>
                <div class="rounded-2xl bg-white p-8 shadow transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-3xl text-white"><i class="fas fa-chart-line"></i></div>
                    <h3 class="text-xl font-semibold text-gray-800">Performance Tracking</h3>
                    <p class="mt-2 text-gray-600">Evaluate employee performance with appraisals and real-time analytics.</p>
                </div>
                <div class="rounded-2xl bg-white p-8 shadow transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-3xl text-white"><i class="fas fa-file-alt"></i></div>
                    <h3 class="text-xl font-semibold text-gray-800">Document Management</h3>
                    <p class="mt-2 text-gray-600">Centralized, secure storage ensuring HR documents are organized and accessible.</p>
                </div>
                <div class="rounded-2xl bg-white p-8 shadow transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-3xl text-white"><i class="fas fa-briefcase"></i></div>
                    <h3 class="text-xl font-semibold text-gray-800">Job Posting</h3>
                    <p class="mt-2 text-gray-600">Create and distribute postings across platforms with application tracking.</p>
                </div>
                <div class="rounded-2xl bg-white p-8 shadow transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-3xl text-white"><i class="fas fa-graduation-cap"></i></div>
                    <h3 class="text-xl font-semibold text-gray-800">Learning & Development</h3>
                    <p class="mt-2 text-gray-600">Train and upskill your workforce with integrated learning tools.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules -->
    <section id="modules" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">HR Management Modules</h2>
                <p class="mt-3 text-gray-600">Access all your HR tools from one integrated platform.</p>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="Applicant_Tracking.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-user-plus text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Applicant Tracking</h4>
                    <p class="mt-1 text-sm text-gray-600">Manage applications and pipeline</p>
                </a>
                <a href="candidate_sourcing_&_tracking.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-search text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Candidate Sourcing</h4>
                    <p class="mt-1 text-sm text-gray-600">Find and track candidates</p>
                </a>
                <a href="Interviewschedule.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-calendar-alt text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Interview Schedule</h4>
                    <p class="mt-1 text-sm text-gray-600">Schedule and manage interviews</p>
                </a>
                <a href="performance_and_appraisals.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-star text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Performance & Appraisals</h4>
                    <p class="mt-1 text-sm text-gray-600">Evaluate performance</p>
                </a>
                <a href="Documentfiles.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-folder text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Document Files</h4>
                    <p class="mt-1 text-sm text-gray-600">Manage documents</p>
                </a>
                <a href="modules/job_posting.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-bullhorn text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Job Posting</h4>
                    <p class="mt-1 text-sm text-gray-600">Create and manage jobs</p>
                </a>
                <a href="modules/learning.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-book text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Learning Management</h4>
                    <p class="mt-1 text-sm text-gray-600">Training and development</p>
                </a>
                <a href="modules/recognition.php" class="group rounded-xl border-2 border-transparent bg-gray-50 p-6 text-center transition hover:-translate-y-1 hover:border-brand-500 hover:bg-white hover:shadow">
                    <i class="fas fa-trophy text-3xl text-brand-500"></i>
                    <h4 class="mt-3 font-semibold text-gray-800">Recognition</h4>
                    <p class="mt-1 text-sm text-gray-600">Rewards and recognition</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-stats-gradient py-16 text-white">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-2 gap-6 text-center md:grid-cols-4">
                <div>
                    <h3 class="text-4xl font-extrabold text-brand-500">500+</h3>
                    <p class="mt-1 text-white/90">Companies Trust HR1</p>
                </div>
                <div>
                    <h3 class="text-4xl font-extrabold text-brand-500">50K+</h3>
                    <p class="mt-1 text-white/90">Employees Managed</p>
                </div>
                <div>
                    <h3 class="text-4xl font-extrabold text-brand-500">99.9%</h3>
                    <p class="mt-1 text-white/90">Uptime Guarantee</p>
                </div>
                <div>
                    <h3 class="text-4xl font-extrabold text-brand-500">24/7</h3>
                    <p class="mt-1 text-white/90">Support Available</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 pt-14 text-gray-300">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 gap-8 pb-8 md:grid-cols-3">
                <div>
                    <h3 class="text-brand-500 text-xl font-semibold">HR1 System</h3>
                    <p class="mt-3 text-gray-400">Comprehensive HRMS designed to streamline HR operations and improve workforce management efficiency.</p>
                </div>
                <div>
                    <h3 class="text-brand-500 text-xl font-semibold">Quick Links</h3>
                    <ul class="mt-3 space-y-2">
                        <li><a class="hover:text-brand-500 transition" href="/HR1/login.php">Login</a></li>
                        <li><a class="hover:text-brand-500 transition" href="aboutus.php">About Us</a></li>
                        <li><a class="hover:text-brand-500 transition" href="#features">Features</a></li>
                        <li><a class="hover:text-brand-500 transition" href="#modules">Modules</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-brand-500 text-xl font-semibold">Contact Info</h3>
                    <ul class="mt-3 space-y-2">
                        <li><i class="fas fa-envelope mr-2"></i> support@hr1system.com</li>
                        <li><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> 123 Business St, City, State</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 py-4 text-center text-sm text-gray-400">
                <p>&copy; <?= date("Y") ?> HR1 System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const toggle = document.getElementById('mobileToggle');
        const mobileMenu = document.getElementById('mobileMenu');
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
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    // close mobile menu on navigation
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
    </script>
</body>
</html>

