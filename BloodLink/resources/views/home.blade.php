<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodLink - Save Lives</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>

@include('partials.navbar')

<!-- HERO -->
<section class="hero-section">
    <div class="hero-blood-drop"><i class="fas fa-droplet"></i></div>
    <div class="hero-blood-drop"><i class="fas fa-droplet"></i></div>
    <div class="hero-blood-drop"><i class="fas fa-droplet"></i></div>
    <div class="hero-blood-drop"><i class="fas fa-droplet"></i></div>
    <div class="hero-blood-drop"><i class="fas fa-droplet"></i></div>

    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-content">
                    <span class="d-inline-block bg-white bg-opacity-10 text-white px-3 py-1 rounded-pill mb-3" style="font-size: 0.85rem; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-heartbeat text-danger me-1"></i> Emergency Blood Network
                    </span>
                    <h1>Save Lives with <span class="highlight">BloodLink</span></h1>
                    <p class="hero-subtitle">Connecting blood donors with hospitals and patients in real time. Every drop counts, every second matters.</p>
                    <div class="hero-btns mt-4">
                        <a href="{{ route('register') }}?role=donor" class="btn-hero-primary">
                            <i class="fas fa-heart me-2"></i> Become a Donor
                        </a>
                        <a href="{{ route('register') }}?role=hospital" class="btn-hero-secondary">
                            <i class="fas fa-hospital me-2"></i> Register Hospital
                        </a>
                    </div>
                    <div class="d-flex gap-4 mt-4 pt-2">
                        <div>
                            <div class="text-white fw-bold" style="font-size: 1.5rem;">500+</div>
                            <div class="text-white-50" style="font-size: 0.85rem;">Donors</div>
                        </div>
                        <div>
                            <div class="text-white fw-bold" style="font-size: 1.5rem;">50+</div>
                            <div class="text-white-50" style="font-size: 0.85rem;">Hospitals</div>
                        </div>
                        <div>
                            <div class="text-white fw-bold" style="font-size: 1.5rem;">1000+</div>
                            <div class="text-white-50" style="font-size: 0.85rem;">Lives Saved</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-illustration">
                    <div class="hero-card fade-in-up stagger-2">
                        <div class="big-icon"><i class="fas fa-droplet"></i></div>
                        <h3>BloodLink</h3>
                        <p>Your blood type is needed. Join the network and save lives today.</p>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <span class="badge bg-danger">A+</span>
                            <span class="badge bg-danger">B+</span>
                            <span class="badge bg-danger">O+</span>
                            <span class="badge bg-danger">AB+</span>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <span class="badge bg-danger" style="opacity:0.8;">A-</span>
                            <span class="badge bg-danger" style="opacity:0.8;">B-</span>
                            <span class="badge bg-danger" style="opacity:0.8;">O-</span>
                            <span class="badge bg-danger" style="opacity:0.8;">AB-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features" class="features-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Features</span>
            <h2>Why Choose BloodLink?</h2>
            <p>We make blood donation simple, fast, and reliable for everyone involved.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 fade-in-up stagger-1">
                <div class="feature-card-modern">
                    <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                    <h5>Real-Time Matching</h5>
                    <p>Instant notifications when your blood type is needed. Respond to urgent requests in seconds.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up stagger-2">
                <div class="feature-card-modern">
                    <div class="feature-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <h5>Location Based</h5>
                    <p>Find nearby donation opportunities and hospitals. Save time when every minute counts.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up stagger-3">
                <div class="feature-card-modern">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h5>Secure & Private</h5>
                    <p>Your medical data is encrypted and protected. We prioritize your privacy and security.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up stagger-4">
                <div class="feature-card-modern">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h5>Track Donations</h5>
                    <p>Monitor your donation history, impact stats, and eligibility schedule all in one place.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up stagger-5">
                <div class="feature-card-modern">
                    <div class="feature-icon"><i class="fas fa-comments"></i></div>
                    <h5>Direct Messaging</h5>
                    <p>Communicate directly with hospitals and donors through our integrated messaging system.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up stagger-6">
                <div class="feature-card-modern">
                    <div class="feature-icon"><i class="fas fa-bell"></i></div>
                    <h5>Smart Alerts</h5>
                    <p>Receive customized alerts for emergency requests matching your blood type and location.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how-it-works" class="how-it-works">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Process</span>
            <h2>How It Works</h2>
            <p>Three simple steps to save a life through BloodLink.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-line"></div>
                    <div class="mt-3">
                        <h5>Register & Profile</h5>
                        <p>Create your account as a donor or hospital. Fill in your blood type and location details.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-line"></div>
                    <div class="mt-3">
                        <h5>Receive Requests</h5>
                        <p>Get notified when hospitals need your blood type. Review and respond to requests instantly.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="mt-3">
                        <h5>Donate & Save</h5>
                        <p>Visit the hospital, donate blood, and track your impact. Every donation saves up to 3 lives.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BLOOD TYPES -->
<section id="blood-types" class="blood-types-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Knowledge</span>
            <h2>Blood Type Compatibility</h2>
            <p>Know your blood type and understand who can receive your donation.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="blood-type-grid">
                    <div class="blood-type-item"><div class="type-label">O-</div><div class="type-info">Universal Donor</div></div>
                    <div class="blood-type-item"><div class="type-label">O+</div><div class="type-info">Most Common</div></div>
                    <div class="blood-type-item"><div class="type-label">A-</div><div class="type-info">Rare</div></div>
                    <div class="blood-type-item"><div class="type-label">A+</div><div class="type-info">Common</div></div>
                    <div class="blood-type-item"><div class="type-label">B-</div><div class="type-info">Very Rare</div></div>
                    <div class="blood-type-item"><div class="type-label">B+</div><div class="type-info">Less Common</div></div>
                    <div class="blood-type-item"><div class="type-label">AB-</div><div class="type-info">Rarest</div></div>
                    <div class="blood-type-item"><div class="type-label">AB+</div><div class="type-info">Universal Recipient</div></div>
                </div>
                <p class="text-muted mt-3" style="font-size: 0.9rem;">
                    <i class="fas fa-info-circle me-1"></i> O- is the universal donor type, AB+ is the universal recipient.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- STATS COUNTER -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon-bg"><i class="fas fa-droplet"></i></div>
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Registered Donors</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon-bg"><i class="fas fa-hospital"></i></div>
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Partner Hospitals</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon-bg"><i class="fas fa-tint"></i></div>
                    <div class="stat-number">1,200+</div>
                    <div class="stat-label">Donations Made</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-icon-bg"><i class="fas fa-heartbeat"></i></div>
                    <div class="stat-number">3,600+</div>
                    <div class="stat-label">Lives Impacted</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Donation Tips -->
<section style="padding:5rem 0;background:var(--bg-body);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="font-size:2rem;"><i class="fas fa-lightbulb me-2 text-danger"></i>Donation Tips</h2>
            <p class="text-muted">Prepare yourself for a safe and comfortable donation experience</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100" style="border-radius:var(--radius-md);">
                    <div class="card-body py-4">
                        <div style="width:48px;height:48px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fas fa-bed" style="color:var(--primary);font-size:1.3rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Rest Well</h6>
                        <p class="small text-muted mb-0">Get at least 8 hours of sleep before your donation.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100" style="border-radius:var(--radius-md);">
                    <div class="card-body py-4">
                        <div style="width:48px;height:48px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fas fa-apple-alt" style="color:var(--primary);font-size:1.3rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Eat Well</h6>
                        <p class="small text-muted mb-0">Have a healthy meal and avoid fatty foods before donating.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100" style="border-radius:var(--radius-md);">
                    <div class="card-body py-4">
                        <div style="width:48px;height:48px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fas fa-tint" style="color:var(--primary);font-size:1.3rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Stay Hydrated</h6>
                        <p class="small text-muted mb-0">Drink plenty of water before and after your donation.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100" style="border-radius:var(--radius-md);">
                    <div class="card-body py-4">
                        <div style="width:48px;height:48px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fas fa-id-card" style="color:var(--primary);font-size:1.3rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Bring ID</h6>
                        <p class="small text-muted mb-0">Bring a valid ID and your blood donor card if you have one.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Make a Difference?</h2>
            <p>Join BloodLink today and become part of a life-saving community. Your next donation could save someone's life.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}?role=donor" class="btn-hero-primary">
                    <i class="fas fa-heart me-2"></i> Become a Donor
                </a>
                <a href="{{ route('login') }}" class="btn-hero-secondary">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer-modern">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-brand">
                    <span class="brand-icon"><i class="fas fa-droplet"></i></span>
                    BloodLink
                </div>
                <p style="font-size: 0.9rem;">A smart platform connecting blood donors with hospitals to save lives quickly and efficiently.</p>
            </div>
            <div class="col-md-2">
                <h5>Quick Links</h5>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/#features') }}">Features</a>
                    <a href="{{ url('/#how-it-works') }}">How It Works</a>
                    <a href="{{ url('/#blood-types') }}">Blood Types</a>
                </div>
            </div>
            <div class="col-md-3">
                <h5>For Donors</h5>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('register') }}?role=donor">Register as Donor</a>
                    <a href="{{ route('login') }}">Donor Login</a>
                    <a href="#">Donation Guidelines</a>
                </div>
            </div>
            <div class="col-md-3">
                <h5>For Hospitals</h5>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('register') }}?role=hospital">Register Hospital</a>
                    <a href="{{ route('login') }}">Hospital Login</a>
                    <a href="#">Partner Benefits</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    &copy; {{ date('Y') }} BloodLink. All rights reserved.
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <span>Made with <i class="fas fa-heart text-danger"></i> for saving lives</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
