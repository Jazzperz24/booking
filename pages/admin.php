<?php
// ============================================================
// pages/admin.php — inside /pages/ folder
// $base = '../'  to reach root-level files
// NOTE: Admin does NOT use navbar.php (it has its own sidebar)
//       but still needs $base set for any links inside
// ============================================================
$page = 'admin';
$base = '../';
require '../config/config.php';


if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: loginpage.php');
    exit();
}

$totalClients    = $db->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$totalCoaches    = $db->query("SELECT COUNT(*) FROM coaches")->fetchColumn();
$totalBookings   = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pendingBookings = $db->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();

$recentBookings = $db->query("
    SELECT b.*, CONCAT(c.firstname,' ',c.lastname) AS client_name, co.name AS coach_name
    FROM bookings b
    JOIN clients c  ON b.client_id = c.id
    JOIN coaches co ON b.coach_id  = co.id
    ORDER BY b.created_at DESC LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

$allCoaches = $db->query("SELECT * FROM coaches ORDER BY category, name")->fetchAll(PDO::FETCH_ASSOC);

$allClients = $db->query("
    SELECT c.*, COUNT(b.id) AS booking_count
    FROM clients c LEFT JOIN bookings b ON b.client_id = c.id
    GROUP BY c.id ORDER BY c.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$catStats = $db->query("
    SELECT category, COUNT(*) as total FROM bookings GROUP BY category ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kaya Pa?</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Admin has its own sidebar, no navbar.css needed -->
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/admin.css">
</head>
<body>

<!-- Sidebar — links use relative paths within /pages/ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <span>Kaya Pa?</span>
        <small>Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Dashboard</div>
        <div class="nav-item active" onclick="showTab('dashboard', this)">
            <i class="fa-solid fa-gauge-high"></i> Overview
        </div>
        <div class="nav-section-label">Management</div>
        <div class="nav-item" onclick="showTab('bookings', this)">
            <i class="fa-solid fa-calendar-check"></i> Bookings
        </div>
        <div class="nav-item" onclick="showTab('coaches', this)">
            <i class="fa-solid fa-user-tie"></i> Coaches
        </div>
        <div class="nav-item" onclick="showTab('clients', this)">
            <i class="fa-solid fa-users"></i> Clients
        </div>
        <div class="nav-section-label">Analytics</div>
        <div class="nav-item" onclick="showTab('reports', this)">
            <i class="fa-solid fa-chart-bar"></i> Reports
        </div>
    </nav>
    <div class="sidebar-bottom">
        <!-- Both files are in /pages/ so no ../ needed for these links -->
        <a href="index.php" onclick="window.location.href='../index.php';return false;">
            <i class="fa-solid fa-house"></i> View Site
        </a>
        <a href="loginpage.php?logout=1" style="margin-top:4px">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:14px">
            <button id="menuBtn" onclick="document.getElementById('sidebar').classList.toggle('open')"
                style="background:none;border:none;color:var(--muted);font-size:18px;cursor:pointer;display:none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="topbar-title" id="topbarTitle">Overview</div>
        </div>
        <div class="topbar-right">
            <span class="admin-badge"><i class="fa-solid fa-shield-halved"></i> Admin</span>
        </div>
    </div>

    <div class="content">

        <!-- DASHBOARD TAB -->
        <div class="tab-panel active" id="tab-dashboard">
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-icon">👥</div><div class="stat-num"><?= $totalClients ?></div><div class="stat-label">Registered Clients</div></div>
                <div class="stat-card"><div class="stat-icon">🏋️</div><div class="stat-num"><?= $totalCoaches ?></div><div class="stat-label">Active Coaches</div></div>
                <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-num"><?= $totalBookings ?></div><div class="stat-label">Total Bookings</div></div>
                <div class="stat-card"><div class="stat-icon">⏳</div><div class="stat-num"><?= $pendingBookings ?></div><div class="stat-label">Pending Bookings</div></div>
            </div>
            <div class="table-card">
                <div class="table-header">
                    <div class="table-title">Recent Bookings</div>
                    <button class="btn-sm btn-edit" onclick="showTab('bookings', null)">View All</button>
                </div>
                <table>
                    <thead><tr><th>#</th><th>Client</th><th>Coach</th><th>Category</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach (array_slice($recentBookings, 0, 6) as $b): ?>
                        <tr><td><?= $b['id'] ?></td><td><?= htmlspecialchars($b['client_name']) ?></td><td><?= htmlspecialchars($b['coach_name']) ?></td><td><?= htmlspecialchars($b['category']) ?></td><td><?= htmlspecialchars($b['book_date']) ?></td><td><span class="badge badge-<?= $b['status'] ?>"><?= $b['status'] ?></span></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentBookings)): ?><tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">No bookings yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BOOKINGS TAB -->
        <div class="tab-panel" id="tab-bookings">
            <div class="table-card">
                <div class="table-header">
                    <div class="table-title">All Bookings</div>
                    <input type="text" class="search-box" placeholder="Search bookings..." oninput="filterTable(this,'bookingsTable')">
                </div>
                <table id="bookingsTable">
                    <thead><tr><th>#</th><th>Client</th><th>Coach</th><th>Category</th><th>Date</th><th>Time</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentBookings as $b): ?>
                        <tr id="brow_<?= $b['id'] ?>">
                            <td><?= $b['id'] ?></td><td><?= htmlspecialchars($b['client_name']) ?></td><td><?= htmlspecialchars($b['coach_name']) ?></td><td><?= htmlspecialchars($b['category']) ?></td><td><?= htmlspecialchars($b['book_date']) ?></td><td><?= htmlspecialchars($b['book_time']) ?></td><td><?= htmlspecialchars($b['session_type']) ?></td>
                            <td><span class="badge badge-<?= $b['status'] ?>" id="bstatus_<?= $b['id'] ?>"><?= $b['status'] ?></span></td>
                            <td style="display:flex;gap:6px;flex-wrap:wrap">
                                <?php if ($b['status'] === 'pending'): ?>
                                <button class="btn-sm btn-confirm" onclick="updateBooking(<?= $b['id'] ?>,'confirmed')">Confirm</button>
                                <button class="btn-sm btn-cancel" onclick="updateBooking(<?= $b['id'] ?>,'cancelled')">Cancel</button>
                                <?php endif; ?>
                                <button class="btn-sm btn-delete" onclick="deleteBooking(<?= $b['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentBookings)): ?><tr><td colspan="9" style="text-align:center;color:var(--muted);padding:30px">No bookings found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- COACHES TAB -->
        <div class="tab-panel" id="tab-coaches">
            <div class="form-card">
                <div class="form-card-title"><i class="fa-solid fa-plus" style="color:var(--gold);margin-right:8px"></i>Add New Coach</div>
                <form id="addCoachForm">
                    <div class="form-row">
                        <div class="form-group"><label>Full Name</label><input type="text" name="name" required placeholder="e.g. Maria Santos"></div>
                        <div class="form-group"><label>Category</label><select name="category" required><option value="">— Select —</option><option value="Dance">Dance</option><option value="Fitness">Fitness</option><option value="Sports">Sports</option><option value="Wellness/Yoga">Wellness/Yoga</option><option value="Belle">Belle</option></select></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Specialty</label><input type="text" name="specialty" placeholder="e.g. Hip-Hop & Ballet"></div>
                        <div class="form-group"><label>Rate (₱/hr)</label><input type="number" name="rate" placeholder="e.g. 800" min="0"></div>
                    </div>
                    <div class="form-group"><label>Bio</label><textarea name="bio" placeholder="Short description..."></textarea></div>
                    <button type="submit" class="btn-gold-sm"><i class="fa-solid fa-plus"></i> Add Coach</button>
                </form>
            </div>
            <div class="table-card">
                <div class="table-header"><div class="table-title">All Coaches (<?= count($allCoaches) ?>)</div><input type="text" class="search-box" placeholder="Search coaches..." oninput="filterTable(this,'coachesTable')"></div>
                <table id="coachesTable">
                    <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Specialty</th><th>Rate/hr</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($allCoaches as $coach): ?>
                        <tr id="crow_<?= $coach['id'] ?>"><td><?= $coach['id'] ?></td><td><?= htmlspecialchars($coach['name']) ?></td><td><?= htmlspecialchars($coach['category']) ?></td><td><?= htmlspecialchars($coach['specialty'] ?? '—') ?></td><td>₱<?= number_format($coach['rate'] ?? 0) ?></td><td><button class="btn-sm btn-delete" onclick="deleteCoach(<?= $coach['id'] ?>)"><i class="fa-solid fa-trash"></i> Delete</button></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($allCoaches)): ?><tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">No coaches yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CLIENTS TAB -->
        <div class="tab-panel" id="tab-clients">
            <div class="table-card">
                <div class="table-header"><div class="table-title">Registered Clients (<?= count($allClients) ?>)</div><input type="text" class="search-box" placeholder="Search clients..." oninput="filterTable(this,'clientsTable')"></div>
                <table id="clientsTable">
                    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Bookings</th><th>Joined</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($allClients as $cl): ?>
                        <tr id="clrow_<?= $cl['id'] ?>"><td><?= $cl['id'] ?></td><td><?= htmlspecialchars($cl['firstname'].' '.$cl['lastname']) ?></td><td><?= htmlspecialchars($cl['email']) ?></td><td><?= htmlspecialchars($cl['phonenumber'] ?? '—') ?></td><td><span class="badge badge-confirmed"><?= $cl['booking_count'] ?></span></td><td><?= date('M d, Y', strtotime($cl['created_at'] ?? 'now')) ?></td><td><button class="btn-sm btn-delete" onclick="deleteClient(<?= $cl['id'] ?>)"><i class="fa-solid fa-trash"></i> Remove</button></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($allClients)): ?><tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px">No clients yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- REPORTS TAB -->
        <div class="tab-panel" id="tab-reports">
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-num"><?= $totalBookings ?></div><div class="stat-label">Total Bookings</div></div>
                <div class="stat-card"><div class="stat-icon" style="background:rgba(74,222,128,.1)">✅</div><div class="stat-num" style="color:var(--green)"><?= $db->query("SELECT COUNT(*) FROM bookings WHERE status='confirmed'")->fetchColumn() ?></div><div class="stat-label">Confirmed</div></div>
                <div class="stat-card"><div class="stat-icon" style="background:rgba(251,191,36,.1)">⏳</div><div class="stat-num" style="color:var(--yellow)"><?= $pendingBookings ?></div><div class="stat-label">Pending</div></div>
                <div class="stat-card"><div class="stat-icon" style="background:rgba(248,113,113,.1)">❌</div><div class="stat-num" style="color:var(--red)"><?= $db->query("SELECT COUNT(*) FROM bookings WHERE status='cancelled'")->fetchColumn() ?></div><div class="stat-label">Cancelled</div></div>
            </div>
            <div class="table-card">
                <div class="table-header"><div class="table-title">Bookings by Category</div></div>
                <div style="padding:20px 24px">
                    <?php
                    $catEmoji = ['Dance'=>'💃','Fitness'=>'🏋️','Sports'=>'⚽','Wellness/Yoga'=>'🧘','Belle'=>'✨'];
                    $maxCount = max(array_column($catStats, 'total') ?: [1]);
                    foreach ($catStats as $cs):
                        $pct = round(($cs['total'] / $maxCount) * 100);
                    ?>
                    <div class="report-bar-row">
                        <div class="report-bar-label">
                            <span><?= ($catEmoji[$cs['category']] ?? '🏅').' '.htmlspecialchars($cs['category']) ?></span>
                            <strong><?= $cs['total'] ?> bookings</strong>
                        </div>
                        <div class="report-bar-track"><div class="report-bar-fill" style="width:<?= $pct ?>%"></div></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($catStats)): ?><p style="color:var(--muted);text-align:center;padding:20px">No booking data yet.</p><?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Admin uses its own JS, NOT navbar.js (no navbar on admin) -->
<script src="/REGISTRATIONSFORM/assets/scripts/admin.js"></script>
</body>
</html>