<?php
$page = 'home';
$base = '';
require 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaya Pa? - Find Your Coach</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/stylemeow.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<?php require 'includes/navbar.php'; ?>

<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-lines"></div>
    <div class="hero-tag">&#10022; Your Journey Starts Here</div>
    <h1>You Can Do It.<span class="line2">Kaya Pa!</span></h1>
    <p>Connect with expert coaches in Dance, Fitness, Sports, Wellness & Belle.</p>
    <div class="hero-btns">
        <a href="pages/aboutus.php" class="btn-outline"><i class="fa-solid fa-circle-info"></i> Learn More</a>
    </div>
    <div class="scroll-hint"><i class="fa-solid fa-chevron-down"></i> Scroll</div>
</section>

<div class="page-wrap">

    <span class="section-label">What We Offer</span>
    <h2 class="section-heading">Choose Your Coaching Category</h2>
    <div class="categories-row">
        <div class="cat-card">
            <span class="cat-icon">&#128131;</span>
            <div class="cat-name">Dance</div>
        </div>
        <div class="cat-card">
            <span class="cat-icon">&#127947;</span>
            <div class="cat-name">Fitness</div>
        </div>
        <div class="cat-card">
            <span class="cat-icon">&#9917;</span>
            <div class="cat-name">Sports</div>
        </div>
        <div class="cat-card">
            <span class="cat-icon">&#129384;</span>
            <div class="cat-name">Wellness / Yoga</div>
        </div>
        <div class="cat-card">
            <span class="cat-icon">&#10024;</span>
            <div class="cat-name">Ballet</div>
        </div>
    </div>

    <span class="section-label">How It Works</span>
    <h2 class="section-heading">3 Simple Steps to Get Started</h2>
    <div class="steps-row">
        <div class="step-card">
            <div class="step-num">01</div>
            <div class="step-title">Choose a Category</div>
            <div class="step-desc">Pick from Dance, Fitness, Sports, Wellness, or Belle coaching to match your goals.</div>
        </div>
        <div class="step-card">
            <div class="step-num">02</div>
            <div class="step-title">Select Up to 3 Coaches</div>
            <div class="step-desc">Browse coach profiles, read bios and rates, then pick up to 3 coaches.</div>
        </div>
        <div class="step-card">
            <div class="step-num">03</div>
            <div class="step-title">Book Your Session</div>
            <div class="step-desc">Set your preferred date, time, and session type — and you're all set!</div>
        </div>
    </div>

    <span class="section-label">Why Kaya Pa?</span>
    <h2 class="section-heading">We Go Further With You</h2>
    <div class="why-grid">
        <div class="why-img">
            <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?auto=format&fit=crop&w=1350&q=80" alt="Training">
        </div>
        <div class="why-list">
            <div class="why-item"><div class="why-icon">&#127885;</div><div class="why-text"><strong>Certified Expert Coaches</strong><span>Every coach is vetted, certified, and passionate about transforming lives.</span></div></div>
            <div class="why-item"><div class="why-icon">&#128197;</div><div class="why-text"><strong>Flexible Scheduling</strong><span>Book sessions around your life — mornings, evenings, or weekends.</span></div></div>
            <div class="why-item"><div class="why-icon">&#127919;</div><div class="why-text"><strong>Personalized Programs</strong><span>No cookie-cutter plans. Your coach builds a program around your unique goals.</span></div></div>
            <div class="why-item"><div class="why-icon">&#127760;</div><div class="why-text"><strong>In-person &amp; Online</strong><span>Train at our facility or connect virtually — the choice is yours.</span></div></div>
        </div>
    </div>

    <span class="section-label">Success Stories</span>
    <h2 class="section-heading">What Our Members Say</h2>
    <div class="testi-grid">
        <div class="testi-card">
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-quote">"I've tried so many gyms but Kaya Pa? is different. My coach genuinely cares about my progress."</div>
            <div class="testi-author"><div class="testi-avatar">&#128105;</div><div><div class="testi-name">Maria L.</div><div class="testi-role">Fitness Member since 2023</div></div></div>
        </div>
        <div class="testi-card">
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-quote">"Booking was so easy. I picked 3 coaches and started training the very next day."</div>
            <div class="testi-author"><div class="testi-avatar">&#128104;</div><div><div class="testi-name">Renz A.</div><div class="testi-role">Sports Member since 2024</div></div></div>
        </div>
        <div class="testi-card">
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-quote">"The dance coaches here are world-class. I went from beginner to performing on stage in 6 months!"</div>
            <div class="testi-author"><div class="testi-avatar">&#128131;</div><div><div class="testi-name">Jessa M.</div><div class="testi-role">Dance Member since 2022</div></div></div>
        </div>
    </div>

    <div class="cta-banner">
        <h2>Ready to Start Your <span>Journey?</span></h2>
        <p>Register now and book your first coaching session today.</p>
        <div class="cta-banner-btns">
            <a href="pages/registrationform.php" class="btn-gold"><i class="fa-solid fa-user-plus"></i> Register Free</a>
            
        </div>
    </div>

</div>

<footer>
    <div class="footer-inner">
        <div class="footer-logo">Kaya Pa?</div>
        <div class="footer-links">
            <a href="index.php">Home</a>
            <a href="pages/aboutus.php">About</a>
            <a href="pages/coaches.php">Coaches</a>
            <a href="pages/registrationform.php">Register</a>
            <a href="pages/loginpage.php">Login</a>
        </div>
        <div class="footer-copy">&#169; 2026 Kaya Pa? Gym. All Rights Reserved.</div>
    </div>
</footer>

<script src="assets/scripts/navbar.js"></script>
</body>
</html>