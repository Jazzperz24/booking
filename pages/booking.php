<?php $page = "booking"; ?>
<?php require "../config/config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Session</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/stylemeow.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/booking.css">
</head>
<body>
<?php require "../includes/navbar.php"; ?>
<div class="page-wrap">

    <div class="page-header">
        <div class="tag">&#10022; Book a Session</div>
        <h1>Book Your <span>Session</span></h1>
        <p>Fill in your details to confirm your booking with the selected coaches.</p>
    </div>

    <div id="guardMsg" class="guard" style="display:none;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <p>No coaches selected. Please go back and choose your coaches first.</p>
        <a href="coaches.php" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Go to Coaches</a>
    </div>

    <div id="bookingContent" style="display:none;">
        <div class="booking-layout">
            <div class="card">
                <div class="card-title"><i class="fa-regular fa-calendar-check"></i> Booking Details</div>
                <form id="bookingForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" id="bookDate" name="book_date" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" id="bookTime" name="book_time" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Session Type</label>
                        <select id="sessionType" name="session_type">
                            <option value="private">Private (1-on-1)</option>
                            <option value="group">Group Session</option>
                            <option value="online">Online / Virtual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <select id="duration" name="duration">
                            <option value="30">30 minutes</option>
                            <option value="60">1 hour</option>
                            <option value="90">1.5 hours</option>
                            <option value="120">2 hours</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes / Special Requests</label>
                        <textarea id="bookNotes" name="notes" placeholder="Any goals, injuries, or special requests..."></textarea>
                    </div>
                    <div class="btn-row">
                        <a href="coaches.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnConfirm">
                            <i class="fa-solid fa-check"></i> Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-list-check"></i> Your Selection</div>
                <div class="summary-coaches" id="summaryCoaches"></div>
                <div class="summary-cat-box">
                    Category
                    <strong id="summaryCategory">—</strong>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/booking.js"></script>
</body>
</html>