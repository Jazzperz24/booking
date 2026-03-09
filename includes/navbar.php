<?php
// ============================================================
// FILE: navbar.php
// PATH: includes/navbar.php
//
// LOGGED OUT  → Home · About · Register · Log In
// LOGGED IN   → Home · About · Coaches · Book Now · [Name ▾]
//
// HOW TO USE — set these two variables before requiring:
//
//   index.php (root):
//     $page = 'home';
//     $base = '';
//     require 'includes/navbar.php';
//
//   pages/*.php (inside /pages/):
//     $page = 'about';
//     $base = '../';
//     require '../includes/navbar.php';
//
// $page values:
//   'home'             → index.php
//   'about'            → pages/aboutus.php
//   'coaches'          → pages/coaches.php
//   'login'            → pages/loginpage.php
//   'registrationform' → pages/registrationform.php
//   'admin'            → pages/admin.php
//   'booking'          → pages/booking.php
// ============================================================

if (!isset($page)) $page = '';
if (!isset($base)) $base = '../'; // default: caller is inside /pages/
?>

<header class="navbar" id="navbar">
    <div class="nav-inner">

        <!-- ── Brand / Logo ── -->
        <a href="<?= $base ?>index.php" class="nav-brand">
            Kaya <span>Pa?</span>
        </a>

        <!-- ── Nav Links ── -->
        <ul class="nav-links" id="navLinks">

            <!-- Always visible -->
            <li>
                <a href="<?= $base ?>index.php"
                   class="nav-link <?= $page === 'home' ? 'active' : '' ?>">
                    Home
                </a>
            </li>

            <li>
                <a href="<?= $base ?>pages/aboutus.php"
                   class="nav-link <?= $page === 'about' ? 'active' : '' ?>">
                    About
                </a>
            </li>

            <?php if (isset($_SESSION['client_id'])): ?>
                <!-- ── LOGGED IN: Coaches · Book Now · Username ── -->
                <li>
                    <a href="<?= $base ?>pages/coaches.php"
                       class="nav-link <?= $page === 'coaches' ? 'active' : '' ?>">
                        Coaches
                    </a>
                </li>
                <li>
                    <a href="<?= $base ?>pages/booking.php"
                       class="nav-btn-gold <?= $page === 'booking' ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-plus"></i> Book Now
                    </a>
                </li>
                <li class="nav-user-item">
                    <button class="nav-user-btn" id="userMenuBtn">
                        <i class="fa-solid fa-circle-user"></i>
                        <?= htmlspecialchars($_SESSION['firstname'] ?? 'Account') ?>
                        <i class="fa-solid fa-chevron-down nav-chevron"></i>
                    </button>
                    <div class="nav-dropdown" id="userDropdown">
                        <?php if (!empty($_SESSION['is_admin'])): ?>
                        <a href="<?= $base ?>pages/admin.php" class="dropdown-item">
                            <i class="fa-solid fa-shield-halved"></i> Admin Panel
                        </a>
                        <?php endif; ?>
                        <a href="<?= $base ?>pages/logout.php" class="dropdown-item">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </div>
                </li>

            <?php else: ?>
                <!-- ── NOT LOGGED IN: Register · Log In ── -->
                <li>
                    <a href="<?= $base ?>pages/registrationform.php"
                       class="nav-link <?= $page === 'registrationform' ? 'active' : '' ?>">
                        Register
                    </a>
                </li>
                <li>
                    <a href="<?= $base ?>pages/loginpage.php"
                       class="nav-btn-gold <?= $page === 'login' ? 'active' : '' ?>">
                        <i class="fa-solid fa-right-to-bracket"></i> Log In
                    </a>
                </li>

            <?php endif; ?>

        </ul>

        <!-- ── Mobile Hamburger ── -->
        <button class="nav-toggle" id="navToggle" aria-label="Open menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
</header>