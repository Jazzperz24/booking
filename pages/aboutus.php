<?php
// ============================================================
// pages/aboutus.php — inside /pages/ folder
// $base = '../'  because we need to go up one level to root
//               to reach assets/, includes/, config/
// ============================================================
$page = 'about';
$base = '../';
require '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Kaya Pa?</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">   <!-- ../  to go up to root -->
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/about.css">
</head>
<body>

<?php require '../includes/navbar.php'; ?>   <!-- ../  to go up to root -->

<section class="hero">
    <div class="hero-tag">&#10022; About Kaya Pa?</div>
    <h1>Who <span>We Are</span></h1>
    <p>More than just a gym — a community built on strength, discipline, and the belief that you can always push further.</p>
</section>

<div class="page-wrap">

    <div class="stats-row">
        <div class="stat-card"><div class="stat-num">15+</div><div class="stat-label">Years of Excellence</div></div>
        <div class="stat-card"><div class="stat-num">2K+</div><div class="stat-label">Members Trained</div></div>
        <div class="stat-card"><div class="stat-num">30+</div><div class="stat-label">Expert Coaches</div></div>
        <div class="stat-card"><div class="stat-num">5</div><div class="stat-label">Coaching Categories</div></div>
    </div>

    <span class="section-label">Our Story</span>
    <h2 class="section-heading">From a Small Gym to<br>a Full Community</h2>
    <div class="story-grid">
        <div class="story-text">
            <p>Founded in 2010, <strong>Kaya Pa?</strong> started with a simple mission: to make high-quality coaching accessible to everyone.</p>
            <p>Over the years, we expanded into a full-service coaching platform offering Dance, Fitness, Sports, Wellness, and Belle coaching.</p>
            <p>Our philosophy is rooted in <strong>discipline</strong>, <strong>consistency</strong>, and <strong>community</strong>.</p>
        </div>
        <div class="story-img">
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1350&q=80" alt="Kaya Pa Gym">
        </div>
    </div>

    <span class="section-label">What We Stand For</span>
    <h2 class="section-heading">Our Core Values</h2>
    <div class="values-grid">
        <div class="value-card"><span class="value-icon">💪</span><div class="value-title">Discipline</div><div class="value-desc">We train the mind as much as the body. Consistency over motivation, every single day.</div></div>
        <div class="value-card"><span class="value-icon">🤝</span><div class="value-title">Community</div><div class="value-desc">Every member is part of the family. We push each other and grow as one.</div></div>
        <div class="value-card"><span class="value-icon">🎯</span><div class="value-title">Excellence</div><div class="value-desc">We hire only the best coaches and settle for nothing less than exceptional results.</div></div>
        <div class="value-card"><span class="value-icon">🌱</span><div class="value-title">Growth</div><div class="value-desc">Whether you're a beginner or a pro, there is always a next level.</div></div>
    </div>

    <span class="section-label">Meet the Team</span>
    <h2 class="section-heading">The People Behind Kaya Pa?</h2>
    <div class="team-grid">
        <div class="team-card"><div class="team-avatar">🏋️</div><div class="team-name">Jasper Garces</div><div class="team-role">Head Coach / Papa Jazz</div><div class="team-bio">Specializing in heavy lifting and powerlifting with 10+ years of experience.</div></div>
        <div class="team-card"><div class="team-avatar">🏃</div><div class="team-name">Kenneth Pagal</div><div class="team-role">Cardio Specialist</div><div class="team-bio">Expert in HIIT and endurance training, pushing cardiovascular limits.</div></div>
        <div class="team-card"><div class="team-avatar">🥗</div><div class="team-name">Wilson Oliverio</div><div class="team-role">Nutrition Coach</div><div class="team-bio">Helping clients fuel their bodies for maximum athletic performance.</div></div>
    </div>

    <div class="cta-section">
        <h2>Ready to Say <span>"Kaya Pa!"</span>?</h2>
        <p>Join thousands of members who transformed their lives. Book a coach today.</p>
        <a href="registrationform.php" class="btn-gold"><i class="fa-solid fa-dumbbell"></i> Book a Coach</a>
    </div>

</div>

<footer>
    <p>© 2026 <span style="color:var(--gold)">Kaya Pa?</span> Gym. All Rights Reserved.</p>
</footer>

<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>   <!-- ../  to go up to root -->
</body>
</html>