<?php
$page = 'dashboard';
$base = '../';
require '../config/config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: /REGISTRATIONSFORM/pages/loginpage.php');
    exit();
}

$client_id = $_SESSION['client_id'];

// Get client info
$clientStmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
$clientStmt->execute([$client_id]);
$client = $clientStmt->fetch(PDO::FETCH_ASSOC);

// Get all bookings for this client
$bookings = $db->prepare("
    SELECT b.*, co.name AS coach_name, co.specialty, co.category, co.photo
    FROM bookings b
    JOIN coaches co ON b.coach_id = co.id
    WHERE b.client_id = ?
    ORDER BY b.created_at DESC
");
$bookings->execute([$client_id]);
$bookings = $bookings->fetchAll(PDO::FETCH_ASSOC);

// Count by status
$total     = count($bookings);
$confirmed = count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed'));
$pending   = count(array_filter($bookings, fn($b) => $b['status'] === 'pending'));
$cancelled = count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Kaya Pa?</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/dashboard.css">
</head>
<body>
<?php require '../includes/navbar.php'; ?>

<div class="page-wrap">

    <!-- ── Header ── -->
    <div class="dash-header">
        <div class="dash-welcome">
            <div class="dash-avatar">
                <?= strtoupper(substr($client['firstname'], 0, 1)) ?>
            </div>
            <div>
                <div class="dash-greeting">Welcome back,</div>
                <h1><?= htmlspecialchars($client['firstname'] . ' ' . $client['lastname']) ?></h1>
                <div class="dash-email"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($client['email']) ?></div>
            </div>
        </div>
        <a href="/REGISTRATIONSFORM/pages/coaches.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> New Booking
        </a>
    </div>

    <!-- ── Tab Navigation ── -->
    <div class="dash-tabs">
        <button class="dash-tab active" onclick="switchTab('bookings', this)">
            <i class="fa-solid fa-calendar-check"></i> My Bookings
        </button>
        <button class="dash-tab" onclick="switchTab('profile', this)">
            <i class="fa-solid fa-user-pen"></i> Edit Profile
        </button>
        <button class="dash-tab" onclick="switchTab('password', this)">
            <i class="fa-solid fa-lock"></i> Change Password
        </button>
    </div>

    <!-- ── BOOKINGS TAB ── -->
    <div class="dash-tab-panel active" id="tab-bookings">

        <!-- Stats -->
        <div class="dash-stats">
            <div class="dash-stat">
                <div class="dash-stat-icon" style="background:rgba(212,168,83,0.12)">
                    <i class="fa-solid fa-calendar" style="color:var(--gold)"></i>
                </div>
                <div class="dash-stat-num"><?= $total ?></div>
                <div class="dash-stat-label">Total</div>
            </div>
            <div class="dash-stat">
                <div class="dash-stat-icon" style="background:rgba(74,222,128,0.1)">
                    <i class="fa-solid fa-circle-check" style="color:#4ade80"></i>
                </div>
                <div class="dash-stat-num" style="color:#4ade80"><?= $confirmed ?></div>
                <div class="dash-stat-label">Confirmed</div>
            </div>
            <div class="dash-stat">
                <div class="dash-stat-icon" style="background:rgba(251,191,36,0.1)">
                    <i class="fa-solid fa-clock" style="color:#fbbf24"></i>
                </div>
                <div class="dash-stat-num" style="color:#fbbf24"><?= $pending ?></div>
                <div class="dash-stat-label">Pending</div>
            </div>
            <div class="dash-stat">
                <div class="dash-stat-icon" style="background:rgba(248,113,113,0.1)">
                    <i class="fa-solid fa-circle-xmark" style="color:#f87171"></i>
                </div>
                <div class="dash-stat-num" style="color:#f87171"><?= $cancelled ?></div>
                <div class="dash-stat-label">Cancelled</div>
            </div>
        </div>

        <!-- Booking Cards -->
        <?php if (empty($bookings)): ?>
        <div class="dash-empty">
            <i class="fa-solid fa-calendar-xmark"></i>
            <p>You have no bookings yet.</p>
            <a href="/REGISTRATIONSFORM/pages/coaches.php" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Browse Coaches
            </a>
        </div>
        <?php else: ?>
        <div class="dash-bookings">
            <?php
            $catEmoji = ['Dance'=>'💃','Fitness'=>'🏋️','Sports'=>'⚽','Wellness/Yoga'=>'🧘','Belle'=>'✨'];
            foreach ($bookings as $b):
                $emoji = $catEmoji[$b['category']] ?? '🏅';
                $hasPhoto = !empty($b['photo']) && file_exists('../assets/images/coaches/' . $b['photo']);
            ?>
            <div class="dash-booking-card" id="bcard_<?= $b['id'] ?>">
                <div class="dash-booking-left">
                    <div class="dash-booking-avatar">
                        <?php if ($hasPhoto): ?>
                            <img src="/REGISTRATIONSFORM/assets/images/coaches/<?= htmlspecialchars($b['photo']) ?>" alt="Coach">
                        <?php else: ?>
                            <?= $emoji ?>
                        <?php endif; ?>
                    </div>
                    <div class="dash-booking-info">
                        <div class="dash-booking-coach"><?= htmlspecialchars($b['coach_name']) ?></div>
                        <div class="dash-booking-spec"><?= htmlspecialchars($b['specialty'] ?? $b['category']) ?></div>
                        <div class="dash-booking-meta">
                            <span><i class="fa-solid fa-calendar-day"></i> <?= date('M d, Y', strtotime($b['book_date'])) ?></span>
                            <span><i class="fa-solid fa-clock"></i> <?= date('h:i A', strtotime($b['book_time'])) ?></span>
                            <span><i class="fa-solid fa-user"></i> <?= htmlspecialchars($b['session_type']) ?></span>
                            <span><i class="fa-solid fa-hourglass-half"></i> <?= $b['duration_minutes'] ?> mins</span>
                        </div>
                        <?php if (!empty($b['notes'])): ?>
                        <div class="dash-booking-notes">
                            <i class="fa-solid fa-note-sticky"></i> <?= htmlspecialchars($b['notes']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="dash-booking-right">
                    <span class="dash-status dash-status-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span>
                    <?php if ($b['status'] === 'pending'): ?>
                    <button class="btn-cancel-booking" onclick="cancelBooking(<?= $b['id'] ?>)">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── EDIT PROFILE TAB ── -->
    <div class="dash-tab-panel" id="tab-profile">
        <div class="dash-form-card">
            <div class="dash-form-title"><i class="fa-solid fa-user-pen"></i> Edit Profile</div>
            <form id="editProfileForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstname" id="edit_firstname"
                               value="<?= htmlspecialchars($client['firstname']) ?>" required>
                        <span class="field-hint">Must start with a capital letter</span>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastname" id="edit_lastname"
                               value="<?= htmlspecialchars($client['lastname']) ?>" required>
                        <span class="field-hint">Must start with a capital letter</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="edit_email"
                           value="<?= htmlspecialchars($client['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" id="edit_phone"
                           value="<?= htmlspecialchars($client['phonenumber'] ?? '') ?>"
                           placeholder="09XXXXXXXXX" maxlength="11">
                    <span class="field-hint">Must start with 09 and be 11 digits</span>
                </div>
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary" id="btnSaveProfile">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── CHANGE PASSWORD TAB ── -->
    <div class="dash-tab-panel" id="tab-password">
        <div class="dash-form-card">
            <div class="dash-form-title"><i class="fa-solid fa-lock"></i> Change Password</div>
            <form id="changePasswordForm">
                <div class="form-group">
                    <label>Current Password</label>
                    <div class="input-icon-wrap">
                        <input type="password" name="current_password" id="current_password"
                               placeholder="Enter your current password" required>
                        <i class="fa-solid fa-eye toggle-eye" data-target="current_password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <div class="input-icon-wrap">
                        <input type="password" name="new_password" id="new_password"
                               placeholder="Enter new password" required>
                        <i class="fa-solid fa-eye toggle-eye" data-target="new_password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <div class="input-icon-wrap">
                        <input type="password" name="confirm_new_password" id="confirm_new_password"
                               placeholder="Repeat new password" required>
                        <i class="fa-solid fa-eye toggle-eye" data-target="confirm_new_password"></i>
                    </div>
                </div>
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary" id="btnChangePassword">
                        <i class="fa-solid fa-key"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/dashboard.js"></script>
</body>
</html>