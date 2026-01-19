<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Digital Library')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
    :root {
        --primary: #4361ee;
        --primary-dark: #3a0ca3;
        --secondary: #7209b7;
        --accent: #4cc9f0;
        --light: #f8f9fa;
        --dark: #212529;
        --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        --gradient-light: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
    }
    
    /* Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: #ffffff;
        color: var(--dark);
        line-height: 1.6;
    }
    
    main {
        flex: 1;
        position: relative;
    }
    
    /* Enhanced Navigation */
    .navbar {
        background: var(--gradient) !important;
        box-shadow: 0 4px 20px rgba(67, 97, 238, 0.15);
        padding: 0.8rem 0;
        transition: all 0.3s ease;
    }
    
    .navbar.scrolled {
        padding: 0.5rem 0;
        box-shadow: 0 4px 20px rgba(67, 97, 238, 0.2);
    }
    
    .navbar-brand {
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: -0.5px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .navbar-brand:hover {
        transform: translateY(-2px);
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .nav-link {
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        margin: 0 0.2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
        color: rgba(255, 255, 255, 0.9) !important;
    }
    
    .nav-link:hover, .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        color: white !important;
    }
    
    .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: white;
        transition: width 0.3s ease;
    }
    
    .nav-link:hover::after, .nav-link.active::after {
        width: 70%;
    }
    
    /* User Info in Dropdown */
    .user-info {
        display: flex;
        flex-direction: column;
    }
    
    .user-info .fw-bold {
        font-size: 0.95rem;
    }
    
    .user-info small {
        font-size: 0.8rem;
        opacity: 0.8;
    }
    
    /* Enhanced Dropdown */
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-radius: 12px;
        overflow: hidden;
        margin-top: 10px;
        animation: fadeIn 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        min-width: 220px;
    }
    
    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(-10px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    .dropdown-item {
        padding: 0.8rem 1.2rem;
        transition: all 0.2s ease;
        font-weight: 500;
        border-left: 3px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding-left: 1.5rem;
        border-left: 3px solid var(--primary);
    }
    
    .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, #fee 0%, #ffe6e6 100%);
    }
    
    .dropdown-divider {
        margin: 0.5rem 0;
        opacity: 0.2;
    }
    
    /* Enhanced Buttons */
    .btn-primary {
        background: var(--gradient);
        border: none;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        background: linear-gradient(135deg, #3a0ca3 0%, #4361ee 100%);
    }
    
    .btn-primary:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }
    
    .btn-primary::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.4s, height 0.4s;
    }
    
    .btn-primary:hover::after {
        width: 300px;
        height: 300px;
    }
    
    /* Enhanced Input */
    .form-control {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 0.75rem 1rem;
        transition: all 0.3s;
        font-size: 0.95rem;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        transform: translateY(-2px);
    }
    
    /* Enhanced Footer */
    footer {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        position: relative;
        overflow: hidden;
        color: white;
    }
    
    footer * {
        color: white !important;
    }
    
    footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient);
    }
    
    footer h5 {
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        display: inline-block;
        font-size: 1.1rem;
    }
    
    footer h5::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--accent);
        transition: width 0.3s ease;
    }
    
    footer h5:hover::after {
        width: 60px;
    }
    
    footer .list-unstyled li {
        margin-bottom: 0.8rem;
    }
    
    footer a {
        transition: all 0.3s;
        opacity: 0.9;
        text-decoration: none !important;
    }
    
    footer a:hover {
        color: var(--accent) !important;
        opacity: 1;
        padding-left: 5px;
        text-decoration: none !important;
    }
    
    /* Social Icons */
    .social-icons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .social-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transition: all 0.3s;
        text-decoration: none;
        font-size: 1rem;
    }
    
    .social-icons a:hover {
        background: var(--primary);
        transform: translateY(-3px) rotate(5deg);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    /* Newsletter Form */
    .newsletter-form .input-group {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .newsletter-form .input-group:focus-within {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .newsletter-form .form-control {
        border-radius: 12px 0 0 12px;
        border: none;
        padding-left: 20px;
        background: rgba(255, 255, 255, 0.95);
        color: var(--dark) !important;
    }
    
    .newsletter-form .form-control::placeholder {
        color: #666;
        opacity: 0.8;
    }
    
    .newsletter-form .btn {
        border-radius: 0 12px 12px 0;
        min-width: 45px;
    }
    
    /* Opening Hours */
    .opening-hours {
        background: rgba(255, 255, 255, 0.05);
        padding: 20px;
        border-radius: 12px;
        margin-top: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .opening-hours p {
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .opening-hours i {
        width: 20px;
        text-align: center;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar-brand {
            font-size: 1.3rem;
        }
        
        .nav-link {
            padding: 0.8rem 1rem !important;
            margin: 0.2rem 0;
            text-align: center;
        }
        
        .dropdown-menu {
            border-radius: 8px;
            margin-top: 5px;
            width: 95%;
            left: 50% !important;
            transform: translateX(-50%) !important;
        }
        
        footer {
            text-align: center;
            padding: 3rem 0;
        }
        
        footer h5::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .social-icons {
            justify-content: center;
        }
        
        .opening-hours {
            text-align: left;
        }
    }
    
    /* Scroll Animation */
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 10px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: var(--gradient);
        border-radius: 5px;
        border: 2px solid #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: var(--gradient-light);
    }
    
    /* Loading Animation */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(67, 97, 238, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary);
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Utility Classes */
    .text-gradient {
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .shadow-soft {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .shadow-medium {
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .hover-lift {
        transition: transform 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
    }
</style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="fas fa-book-open me-2"></i>
                <span>Digital Library</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Side Navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="fas fa-home me-2"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">
                            <i class="fas fa-info-circle me-2"></i>About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-search me-2"></i>Browse
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#featured-books">
                            <i class="fas fa-star me-2"></i>Featured
                        </a>
                    </li>
                </ul>
                
                <!-- Right Side Navigation -->
                <ul class="navbar-nav ms-auto">
                    @auth
                        <!-- User Dropdown Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                </div>
                                <div class="user-info">
                                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                                    <small class="text-light opacity-75">{{ ucfirst(auth()->user()->role) }}</small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li class="px-3 py-2">
                                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user me-2"></i>My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('my.checkouts') }}">
                                        <i class="fas fa-book me-2"></i>My Checkouts
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('reservations.index') }}">
                                        <i class="fas fa-calendar-check me-2"></i>My Reservations
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('history') }}">
                                        <i class="fas fa-history me-2"></i>Borrowing History
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cog me-2"></i>Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-2"></i> Get Started
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
<footer class="py-5 mt-5" style="background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4 fade-in">
                <h5 class="text-white"><i class="fas fa-book-open me-2"></i>Digital Library</h5>
                <p class="text-white mt-3">
                    Your premier destination for knowledge and discovery. 
                    Access thousands of digital resources, books, and research materials.
                </p>
                <div class="social-icons mt-4">
                    <a href="#" class="text-white" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4 fade-in" style="animation-delay: 0.1s;">
                <h5 class="text-white">Quick Links</h5>
                <ul class="list-unstyled mt-3">
                    <li><a href="{{ url('/') }}" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="{{ route('about') }}" class="text-white text-decoration-none">About Us</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Browse Collection</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Categories</a></li>
                    <li><a href="#" class="text-white text-decoration-none">New Arrivals</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Best Sellers</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.2s;">
                <h5 class="text-white">Support</h5>
                <ul class="list-unstyled mt-3">
                    <li><a href="#" class="text-white text-decoration-none">Help Center</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Contact Support</a></li>
                    <li><a href="#" class="text-white text-decoration-none">FAQ</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Documentation</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Privacy Policy</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Terms of Service</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.3s;">
                <h5 class="text-white">Stay Updated</h5>
                <p class="text-white mt-3">
                    Subscribe to our newsletter for the latest book releases, library events, and special offers.
                </p>
                <div class="newsletter-form mt-3">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email" aria-label="Email">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <div class="opening-hours text-white mt-4">
                    <p class="mb-1"><i class="fas fa-clock me-2"></i><strong>Opening Hours</strong></p>
                    <p class="mb-0">Mon-Fri: 8:00 AM - 10:00 PM</p>
                    <p class="mb-0">Sat-Sun: 9:00 AM - 8:00 PM</p>
                    <p class="mt-2 mb-0"><i class="fas fa-phone me-2"></i>(555) 123-4567</p>
                    <p class="mb-0"><i class="fas fa-envelope me-2"></i>support@library.edu</p>
                </div>
            </div>
        </div>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.25);">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-white">
                    &copy; {{ date('Y') }} Digital Library Management System. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-center justify-content-md-end align-items-center">
                    <img src="https://img.icons8.com/color/30/ffffff/visa.png" class="me-2" alt="Visa">
                    <img src="https://img.icons8.com/color/30/ffffff/mastercard.png" class="me-2" alt="Mastercard">
                    <img src="https://img.icons8.com/color/30/ffffff/paypal.png" class="me-2" alt="PayPal">
                    <img src="https://img.icons8.com/color/30/ffffff/amex.png" alt="Amex">
                </div>
            </div>
        </div>
    </div>
</footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sticky navbar on scroll
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Active link detection
        const currentUrl = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const linkUrl = link.getAttribute('href');
            if (currentUrl === linkUrl || 
                (linkUrl !== '/' && currentUrl.startsWith(linkUrl))) {
                link.classList.add('active');
            }
        });
        
        // Intersection Observer for fade-in animations
        const fadeElements = document.querySelectorAll('.fade-in');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        fadeElements.forEach(el => observer.observe(el));
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
        
        // Newsletter form submission
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            const submitBtn = newsletterForm.querySelector('.btn');
            const emailInput = newsletterForm.querySelector('input[type="email"]');
            
            submitBtn.addEventListener('click', function() {
                const email = emailInput.value.trim();
                if (email && validateEmail(email)) {
                    // Show loading
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<span class="loading"></span>';
                    this.disabled = true;
                    
                    // Simulate API call
                    setTimeout(() => {
                        alert('Thank you for subscribing! You\'ll receive updates shortly.');
                        emailInput.value = '';
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                    }, 1500);
                } else {
                    alert('Please enter a valid email address.');
                    emailInput.focus();
                }
            });
        }
        
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
</body>
</html>