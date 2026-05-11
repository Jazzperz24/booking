<?php
if (!isset($page)) $page = '';
if (!isset($base)) $base = '../';
?>
<header class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="<?= $base ?>index.php" class="nav-brand">Kaya <span>Pa?</span></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="<?= $base ?>index.php" class="nav-link <?= $page==='home'?'active':'' ?>">Home</a></li>
            <li><a href="<?= $base ?>pages/aboutus.php" class="nav-link <?= $page==='about'?'active':'' ?>">About</a></li>
            <?php if (isset($_SESSION['client_id'])): ?>
                <li><a href="<?= $base ?>pages/coaches.php" class="nav-link <?= $page==='coaches'?'active':'' ?>">Coaches</a></li>
                <li><a href="<?= $base ?>pages/booking.php" class="nav-btn-gold <?= $page==='booking'?'active':'' ?>"><i class="fa-solid fa-calendar-plus"></i> Book Now</a></li>
                <li class="nav-user-item">
                    <button class="nav-user-btn" id="userMenuBtn">
                        <i class="fa-solid fa-circle-user"></i>
                        <?= htmlspecialchars($_SESSION['firstname'] ?? 'Account') ?>
                        <i class="fa-solid fa-chevron-down nav-chevron"></i>
                    </button>
                    <div class="nav-dropdown" id="userDropdown">
                        <a href="<?= $base ?>pages/dashboard.php" class="dropdown-item">
                            <i class="fa-solid fa-gauge"></i> My Dashboard
                        </a>
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
                <li><a href="<?= $base ?>pages/registrationform.php" class="nav-link <?= $page==='registrationform'?'active':'' ?>">Register</a></li>
                <li><a href="<?= $base ?>pages/loginpage.php" class="nav-btn-gold <?= $page==='login'?'active':'' ?>"><i class="fa-solid fa-right-to-bracket"></i> Log In</a></li>
            <?php endif; ?>
        </ul>
        <button class="nav-toggle" id="navToggle" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>