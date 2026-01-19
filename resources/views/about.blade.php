@extends('layouts.student')
@section('title', 'About Us - Digital Library')

@section('content')
<style>
    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, #4361ee, #3a0ca3);
        border-radius: 3px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 40px;
    }
    
    .timeline-date {
        position: absolute;
        left: -65px;
        top: 0;
        width: 50px;
        text-align: center;
    }
    
    .timeline-date .badge {
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border: 2px solid white;
        box-shadow: 0 3px 10px rgba(67, 97, 238, 0.3);
    }
    
    .timeline-content {
        padding-left: 30px;
        position: relative;
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-left: 4px solid #4361ee;
    }
    
    .timeline-content::before {
        content: '';
        position: absolute;
        left: -10px;
        top: 25px;
        width: 20px;
        height: 20px;
        background: white;
        border: 3px solid #4361ee;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        z-index: 2;
    }
    
    .timeline-icon {
        position: absolute;
        left: -30px;
        top: 20px;
        width: 40px;
        height: 40px;
        background: white;
        border: 2px solid #4361ee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4361ee;
        z-index: 3;
        box-shadow: 0 3px 10px rgba(67, 97, 238, 0.2);
    }
    
    .timeline-content h5 {
        color: #4361ee;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .timeline-content p {
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .timeline-content strong {
        color: #3a0ca3;
        font-weight: 600;
    }
    
    /* Team Image */
    .team-img {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border-radius: 50%;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Founder Highlight */
    .founder-avatar {
        width: 100px;
        height: 100px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #4361ee;
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
    }
    
    /* Custom card styling */
    .about-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        background: white;
    }
    
    .about-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }
    
    /* Section Title */
    .section-title {
        position: relative;
        margin-bottom: 3rem;
        font-weight: 700;
        color: #212529;
        text-align: center;
        font-size: 2.5rem;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 5px;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border-radius: 3px;
        box-shadow: 0 3px 10px rgba(67, 97, 238, 0.3);
    }
    
    /* Stats Section */
    .stats-section {
        background: #f8f9fa;
        padding: 4rem 0;
    }
    
    .stat-card {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 12px;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #4361ee;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.5rem;
    }
    
    .stat-text {
        color: #6c757d;
        font-weight: 500;
    }
    
    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
        margin: 0;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
    }
    
    .hero-title {
        font-weight: 800;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-size: 3rem;
    }
    
    .hero-lead {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-section {
            padding: 60px 0 !important;
        }
        
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-lead {
            font-size: 1rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .timeline {
            padding-left: 30px;
        }
        
        .timeline::before {
            left: 15px;
        }
        
        .timeline-date {
            position: relative;
            left: 0;
            margin-bottom: 10px;
            width: auto;
            text-align: left;
        }
        
        .timeline-content {
            padding-left: 20px;
            margin-left: 0;
        }
        
        .timeline-content::before {
            display: none;
        }
        
        .timeline-icon {
            position: relative;
            left: 0;
            top: 0;
            margin-bottom: 15px;
            width: 50px;
            height: 50px;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center hero-content">
        <h1 class="hero-title display-4 mb-4">About Our Library</h1>
        <p class="hero-lead mb-4">Providing access to knowledge since 2003</p>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card about-card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <i class="fas fa-bullseye fa-3x mb-4" style="color: #4361ee;"></i>
                        <h3 class="mb-3 fw-bold">Our Mission</h3>
                        <p class="text-muted mb-0">
                            To provide equitable access to information, resources, and services that meet the educational, 
                            informational, and recreational needs of our community. We strive to foster a love for reading, 
                            support lifelong learning, and serve as a center for community engagement.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card about-card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <i class="fas fa-eye fa-3x mb-4" style="color: #4361ee;"></i>
                        <h3 class="mb-3 fw-bold">Our Vision</h3>
                        <p class="text-muted mb-0">
                            To be the premier destination for discovery, learning, and connection in our community. 
                            We envision a future where every individual has the tools and resources they need to succeed 
                            in an ever-changing world, powered by the transformative power of knowledge.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- History Timeline -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center section-title mb-5">Our Journey</h2>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="timeline">
                    <div class="timeline-item mb-4">
                        <div class="timeline-date">
                            <span class="badge bg-primary rounded-pill">2003</span>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-icon">
                                <i class="fas fa-flag fa-lg"></i>
                            </div>
                            <h5 class="fw-bold">Library Founded</h5>
                            <p class="text-muted mb-0">Digital Library established by Founder <strong>Mr. Te Laurent</strong></p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-date">
                            <span class="badge bg-primary rounded-pill">2005</span>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-icon">
                                <i class="fas fa-stamp fa-lg"></i>
                            </div>
                            <h5 class="fw-bold">Official Approval</h5>
                            <p class="text-muted mb-0">Received official approval from <strong>Ministry of Education, Youth and Sport</strong></p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-date">
                            <span class="badge bg-primary rounded-pill">2010</span>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-icon">
                                <i class="fas fa-globe fa-lg"></i>
                            </div>
                            <h5 class="fw-bold">Digital Expansion</h5>
                            <p class="text-muted mb-0">Launched online platform reaching nationwide educational institutions</p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-date">
                            <span class="badge bg-primary rounded-pill">2016</span>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-icon">
                                <i class="fas fa-mobile-alt fa-lg"></i>
                            </div>
                            <h5 class="fw-bold">Mobile Innovation</h5>
                            <p class="text-muted mb-0">Introduced mobile app for students and educators</p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-date">
                            <span class="badge bg-primary rounded-pill">2020</span>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-icon">
                                <i class="fas fa-book-reader fa-lg"></i>
                            </div>
                            <h5 class="fw-bold">Pandemic Response</h5>
                            <p class="text-muted mb-0">Expanded digital resources to support remote learning nationwide</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <span class="badge bg-primary rounded-pill">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-icon">
                                <i class="fas fa-university fa-lg"></i>
                            </div>
                            <h5 class="fw-bold">Modern Education Hub</h5>
                            <p class="text-muted mb-0">Serving as premier digital resource center for educational excellence</p>
                        </div>
                    </div>
                </div>
                
                <!-- Founder Highlight -->
                <div class="card about-card border-0 shadow-lg mt-5">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <div class="founder-avatar mx-auto">
                                    <i class="fas fa-user-tie fa-3x text-primary"></i>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h4 class="fw-bold mb-2">Our Founder</h4>
                                <h5 class="text-primary mb-3">Mr. Te Laurent</h5>
                                <p class="text-muted mb-0">
                                    Visionary educator and technology advocate who established the Digital Library in 2003. 
                                    His commitment to making educational resources accessible to all has shaped the institution 
                                    into a leading digital learning platform recognized by the Ministry of Education, Youth and Sport.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title mb-5">Meet Our Team</h2>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="card about-card border-0 shadow text-center h-100">
                    <div class="card-body p-4">
                        <div class="team-img mb-3">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Mr A</h5>
                        <p class="text-muted small mb-2">Head Librarian</p>
                        <p class="text-muted small">15+ years experience</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card about-card border-0 shadow text-center h-100">
                    <div class="card-body p-4">
                        <div class="team-img mb-3">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Mr B</h5>
                        <p class="text-muted small mb-2">Technical Lead</p>
                        <p class="text-muted small">Digital Systems Expert</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card about-card border-0 shadow text-center h-100">
                    <div class="card-body p-4">
                        <div class="team-img mb-3">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Mr C</h5>
                        <p class="text-muted small mb-2">Catalog Manager</p>
                        <p class="text-muted small">Metadata Specialist</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card about-card border-0 shadow text-center h-100">
                    <div class="card-body p-4">
                        <div class="team-img mb-3">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Mr D</h5>
                        <p class="text-muted small mb-2">User Support</p>
                        <p class="text-muted small">Customer Service Lead</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center section-title mb-5">Contact Information</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card about-card border-0 shadow text-center h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-map-marker-alt fa-2x mb-3" style="color: #4361ee;"></i>
                        <h5 class="fw-bold mb-2">Location</h5>
                        <p class="text-muted">54 St 606<br>Phnom Penh</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card about-card border-0 shadow text-center h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-phone fa-2x mb-3" style="color: #4361ee;"></i>
                        <h5 class="fw-bold mb-2">Phone</h5>
                        <p class="text-muted">098 765 432<br>Mon-Fri: 9am-6pm</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card about-card border-0 shadow text-center h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-envelope fa-2x mb-3" style="color: #4361ee;"></i>
                        <h5 class="fw-bold mb-2">Email</h5>
                        <p class="text-muted">info@digitallibrary.edu<br>support@digitallibrary.edu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation to stat cards and about cards when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });
        
        document.querySelectorAll('.stat-card, .about-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
        
        // Animate timeline items on scroll
        const timelineItems = document.querySelectorAll('.timeline-item');
        const timelineObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateX(0)';
                    }, index * 200);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -50px 0px'
        });
        
        timelineItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'all 0.6s ease';
            timelineObserver.observe(item);
        });
    });
</script>
@endsection