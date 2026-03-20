<?php
// ============================================================
// pages/coaches.php — inside /pages/ folder
// $base = '../'  to reach root-level files
// ============================================================
$page = 'coaches';
$base = '../';
require '../config/config.php';

$coaches = [];
try {
    $stmt = $db->query("SELECT * FROM coaches ORDER BY category, name");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $coaches[$row['category']][] = $row;
    }
} catch (PDOException $e) { /* table may not exist yet */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Coach - Kaya Pa?</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/coaches.css">
</head>
<body>

<?php require '../includes/navbar.php'; ?>

<div class="page-wrap">

    <div class="page-header">
        <div class="tag">&#10022; Find a Coach</div>
        <h1>Choose Your <span>Coach</span></h1>
        <p>Pick a category, select up to 2 coaches, then proceed to booking.</p>
    </div>

    <div class="steps-bar">
        <div class="step-item active" id="stepItem1">
            <div class="step-circle">1</div>
            <div class="step-label">Category</div>
        </div>
        <div class="step-line" id="stepLine1"></div>
        <div class="step-item" id="stepItem2">
            <div class="step-circle">2</div>
            <div class="step-label">Select Coaches</div>
        </div>
    </div>

    <!-- STEP 1: Category -->
    <div class="panel active" id="panel1">
        <p class="section-title">What type of <span>coaching</span> are you looking for?</p>
        <div class="categories-grid">
            <?php
            $catMeta = [
                'Dance'         => ['icon' => '💃',  'label' => 'Dance'],
                'Fitness'       => ['icon' => '🏋️', 'label' => 'Fitness'],
                'Sports'        => ['icon' => '⚽',  'label' => 'Sports'],
                'Wellness/Yoga' => ['icon' => '🧘',  'label' => 'Wellness / Yoga'],
                'Belle'         => ['icon' => '✨',  'label' => 'Ballet'],
            ];
            foreach ($catMeta as $cat => $meta):
                $count = isset($coaches[$cat]) ? count($coaches[$cat]) : 0;
            ?>
            <div class="cat-card" data-category="<?= htmlspecialchars($cat) ?>"
                 onclick="selectCategory('<?= htmlspecialchars($cat) ?>')">
                <span class="cat-icon"><?= $meta['icon'] ?></span>
                <div class="cat-name"><?= htmlspecialchars($meta['label']) ?></div>
                <div class="cat-count"><?= $count ?> coach<?= $count !== 1 ? 'es' : '' ?> available</div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="btn-row">
            <button class="btn btn-primary" id="btnNext1" disabled onclick="goStep2()">
                Continue <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- STEP 2: Select Coaches -->
    <div class="panel" id="panel2">
        <div class="step2-header">
            <p class="section-title" style="margin-bottom:0">Select up to <span>2 coaches</span></p>
            <div class="selected-counter">
                <i class="fa-solid fa-user-check"></i>
                <span id="selectedCount">0</span><span> / 2 selected</span>
            </div>
        </div>
        <div class="coaches-grid" id="coachesGrid"></div>
        <div class="btn-row">
            <button class="btn btn-secondary" onclick="goStep1()">
                <i class="fa-solid fa-arrow-left"></i> Back
            </button>
            <button class="btn btn-primary" id="btnNext2" disabled onclick="goToBooking()">
                Proceed to Booking <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const coachData = <?php echo json_encode($coaches); ?>;
</script>
<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/coaches.js"></script>
</body>
</html>