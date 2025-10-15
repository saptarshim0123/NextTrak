<?php include 'includes/header.php';?>

<body>
    <!-- <style>
        :root {
            /* Primary */
            --bs-primary: #4F46E5;
            --bs-primary-rgb: 79, 70, 229;

            /* Success / Progress */
            --bs-success: #10B981;
            --bs-success-rgb: 16, 185, 129;

            /* Neutral Backgrounds */
            --bs-light: #F9FAFB;
            --bs-body-bg: #FFFFFF;
            --bs-body-color: #111827;

            /* Accent */
            --bs-accent: #6366F1;

            /* Borders */
            --bs-border-color: #E5E7EB;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--bs-body-color);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            border-radius: 50rem;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #4338CA;
            border-color: #4338CA;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-outline-primary {
            color: var(--bs-primary);
            border-color: var(--bs-primary);
            border-radius: 50rem;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            transform: translateY(-1px);
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--bs-primary) !important;
        }

        .nav-link {
            position: relative;
            color: var(--bs-body-color) !important;
            font-weight: 500;
            transition: color 0.3s ease;
            /* Only animate color */
        }

        .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 100%;
            height: 2px;
            background: var(--bs-accent);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--bs-primary) !important;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            transform: scaleX(1);
        }


        .hero-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.05) 0%, transparent 70%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .feature-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid var(--bs-border-color);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-accent));
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .stats-section {
            background: var(--bs-primary);
            color: white;
        }

        .stat-number {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
        }

        .cta-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
        }

        .testimonial-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--bs-border-color);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-applied {
            background: var(--bs-primary);
            color: white;
        }

        .status-interview {
            background: #F59E0B;
            color: white;
        }

        .status-offer {
            background: var(--bs-success);
            color: white;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 4rem 0;
            }

            .fs-1 {
                font-size: 2.5rem !important;
            }

            .navbutton {
                display: none;
            }
        }

        .company-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style> -->
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-lg">
            <a class="navbar-brand" href="#">
                <i data-lucide="target" class="me-2"></i>NextTrak
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-3" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a href="auth/login.php" class="navbutton btn btn-outline-primary me-2">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="auth/register.php" class="navbutton btn btn-primary">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container-lg">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="fs-1 fw-bold mb-4">
                        Track Your Job Hunt<br>
                        <span class="text-primary">Like a Pro</span>
                    </h1>
                    <p class="fs-4 text-muted mb-5 lh-base">
                        Stay organized, never miss a follow-up, and land your dream job faster with NextTrak's
                        intelligent job application tracking system.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 mb-5">
                        <a href="auth/register.php" class="btn btn-primary shadow-sm">
                            <i data-lucide="rocket" class="me-2" style="width: 20px; height: 20px;"></i>
                            Start Tracking Free
                        </a>
                        <a href="#features" class="btn btn-outline-primary">
                            <i data-lucide="play-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                            See How It Works
                        </a>
                    </div>

                    <div class="d-flex align-items-center text-muted">
                        <span class="fs-7">🎯 No credit card required • 💼 Free forever • ⚡ Setup in 2 minutes</span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-lg border-0 p-3">
                        <div class="card-header bg-light border-0 rounded-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">My Applications</h6>
                                <span class="badge bg-primary">12 Active</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="border-0 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="company-icon bg-primary rounded p-2 me-3">
                                                        <i data-lucide="building"
                                                            style="width: 16px; height: 16px; color: white;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Google</div>
                                                        <small class="text-muted">Frontend Developer</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 py-3 text-end">
                                                <span class="status-badge status-interview">Interview</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="company-icon bg-success rounded p-2 me-3">
                                                        <i data-lucide="building"
                                                            style="width: 16px; height: 16px; color: white;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Microsoft</div>
                                                        <small class="text-muted">Senior Developer</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 py-3 text-end">
                                                <span class="status-badge status-offer">Offer</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-0 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="company-icon bg-info rounded p-2 me-3">
                                                        <i data-lucide="building"
                                                            style="width: 16px; height: 16px; color: white;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Amazon</div>
                                                        <small class="text-muted">Full Stack Engineer</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 py-3 text-end">
                                                <span class="status-badge status-applied">Applied</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5">
        <div class="container-lg">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <div class="stat-number">500+</div>
                    <div class="fs-6 opacity-75">Job Seekers</div>
                </div>
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <div class="stat-number">2.5K+</div>
                    <div class="fs-6 opacity-75">Applications Tracked</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-number">78%</div>
                    <div class="fs-6 opacity-75">Success Rate</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-number">45%</div>
                    <div class="fs-6 opacity-75">Faster Job Hunt</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container-lg">
            <div class="text-center mb-5">
                <h2 class="fs-3 fw-semibold mb-3">Everything You Need to Land Your Dream Job</h2>
                <p class="fs-6 text-muted">Powerful features designed to streamline your job search and keep you
                    organized</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i data-lucide="search" style="width: 24px; height: 24px; color: white;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Smart Company Search</h5>
                        <p class="text-muted">Auto-complete company selection with instant search. Add custom companies
                            that aren't listed yet.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i data-lucide="calendar-check" style="width: 24px; height: 24px; color: white;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Follow-up Reminders</h5>
                        <p class="text-muted">Never miss important follow-ups. Set dates and get notified when it's time
                            to reach out.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i data-lucide="palette" style="width: 24px; height: 24px; color: white;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Color-Coded Status</h5>
                        <p class="text-muted">Visual status tracking with color codes. Instantly see which applications
                            need attention.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i data-lucide="filter" style="width: 24px; height: 24px; color: white;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Advanced Filtering</h5>
                        <p class="text-muted">Filter by status, company, date range, or salary. Find exactly what you're
                            looking for quickly.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i data-lucide="bar-chart-3" style="width: 24px; height: 24px; color: white;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Analytics Dashboard</h5>
                        <p class="text-muted">Track your progress with detailed analytics. See your success rate and
                            identify areas for improvement.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i data-lucide="shield-check" style="width: 24px; height: 24px; color: white;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Secure & Private</h5>
                        <p class="text-muted">Your data is encrypted and secure. Only you can access your job
                            application information.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-5 bg-light">
        <div class="container-lg">
            <div class="text-center mb-5">
                <h2 class="fs-3 fw-semibold mb-3">How NextTrak Works</h2>
                <p class="fs-6 text-muted">Get started in just 3 simple steps</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                        style="width: 80px; height: 80px;">
                        <span class="text-white fw-bold fs-4">1</span>
                    </div>
                    <h5 class="fw-semibold mb-3">Create Your Account</h5>
                    <p class="text-muted">Sign up for free in under 2 minutes. No credit card required.</p>
                </div>

                <div class="col-md-4 text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                        style="width: 80px; height: 80px;">
                        <span class="text-white fw-bold fs-4">2</span>
                    </div>
                    <h5 class="fw-semibold mb-3">Add Applications</h5>
                    <p class="text-muted">Input your job applications with company details, status, and follow-up dates.
                    </p>
                </div>

                <div class="col-md-4 text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                        style="width: 80px; height: 80px;">
                        <span class="text-white fw-bold fs-4">3</span>
                    </div>
                    <h5 class="fw-semibold mb-3">Track & Succeed</h5>
                    <p class="text-muted">Stay organized, get reminders, and land your dream job faster.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
        <div class="container-lg">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="fs-3 fw-semibold mb-4">Ready to Accelerate Your Job Search?</h2>
                    <p class="fs-6 text-muted mb-5">Join hundreds of job seekers who have organized their hunt and
                        landed better opportunities faster.</p>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="auth/register.php" class="btn btn-primary px-5">
                            <i data-lucide="user-plus" class="me-2" style="width: 20px; height: 20px;"></i>
                            Create Free Account
                        </a>
                        <a href="auth/login.php" class="btn btn-outline-primary px-5">
                            <i data-lucide="log-in" class="me-2" style="width: 20px; height: 20px;"></i>
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container-lg">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h5 class="fw-bold mb-3">
                        <i data-lucide="target" class="me-2"></i>NextTrak
                    </h5>
                    <p class="text-muted">
                        The smart way to track job applications and accelerate your career growth.
                    </p>
                </div>

                <div class="col-lg-6 text-lg-end">
                    <div class="mb-3">
                        <a href="#" class="text-white text-decoration-none me-4">Privacy Policy</a>
                        <a href="#" class="text-white text-decoration-none me-4">Terms of Service</a>
                        <a href="#contact" class="text-white text-decoration-none">Contact</a>
                    </div>
                    <p class="text-muted mb-0">© 2024 NextTrak. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();

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

        // Navbar scroll effect
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>
</body>

</html>