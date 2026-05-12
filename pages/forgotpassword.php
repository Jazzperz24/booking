<?php

$page = 'login';
$base = '../';
require '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Kaya Pa?</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/login.css">
    <style>
        
        .alert {
            display: none;
            border-radius: 10px;
            padding: 13px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
        }
        .alert-success {
            background: rgba(74,222,128,0.10);
            border: 1px solid rgba(74,222,128,0.30);
            color: #4ade80;
        }
        .alert-error {
            background: rgba(248,113,113,0.10);
            border: 1px solid rgba(248,113,113,0.30);
            color: #f87171;
        }
        .alert i { margin-right: 7px; }

    
        .steps-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
        }
        .step-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: var(--border);
            transition: background .3s;
        }
        .step-dot.active { background: var(--gold); }
        .step-line-sm {
            width: 32px; height: 1px;
            background: var(--border);
        }
    </style>
</head>
<body>

<?php require '../includes/navbar.php'; ?>

<div class="login-page">
    <div class="login-card">

        <div class="card-icon">
            <i class="fa-solid fa-envelope-open-text" style="color:var(--gold)"></i>
        </div>

        <h1>Forgot <span>Password?</span></h1>
        <p class="card-subtitle">
            No worries! Enter your registered email below and we'll
            send a reset link straight to your inbox.
        </p>

        
        <div class="steps-row">
            <div class="step-dot active" id="dot1"></div>
            <div class="step-line-sm"></div>
            <div class="step-dot" id="dot2"></div>
            <div class="step-line-sm"></div>
            <div class="step-dot" id="dot3"></div>
        </div>

        
        <div class="alert alert-success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <strong>Email sent!</strong><br>
            <span id="successMsg"></span>
        </div>

        
        <div class="alert alert-error" id="errorAlert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span id="errorMsg"></span>
        </div>

        
        <form id="forgotForm" novalidate>

            <div class="form-group">
                <label for="forgot_email">
                    <i class="fa-solid fa-envelope" style="color:var(--gold);margin-right:5px"></i>
                    Your Email Address
                </label>
                <input
                    type="email"
                    id="forgot_email"
                    name="email"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required
                >
            </div>

            <button type="submit" class="btn-login" id="forgotBtn">
                <span class="spinner"></span>
                <span class="btn-text">
                    <i class="fa-solid fa-paper-plane"></i>&nbsp; Send Reset Link
                </span>
            </button>

        </form>

        <div class="divider">or</div>

        <p class="card-footer-text">
            Remember your password?
            <a href="loginpage.php">
                <i class="fa-solid fa-right-to-bracket" style="margin-right:3px"></i>Sign in
            </a>
        </p>
        <p class="card-footer-text" style="margin-top:8px">
            No account yet?
            <a href="registrationform.php">Register for free</a>
        </p>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>
<script>
$(function () {

    function showError(msg) {
        $('#errorMsg').text(msg);
        $('#errorAlert').fadeIn(200);
        $('#successAlert').hide();
    }

    function showSuccess(email) {
        $('#successMsg').html(
            'A reset link was sent to <strong>' + email + '</strong>. ' +
            'Check your inbox (and spam/junk folder). ' +
            'The link expires in <strong>1 hour</strong>.'
        );
        $('#successAlert').fadeIn(200);
        $('#errorAlert').hide();
        $('#forgotForm')[0].reset();
        $('#dot1, #dot2').addClass('active');
    }

    $('#forgotForm').on('submit', function (e) {
        e.preventDefault();

        const email = $('#forgot_email').val().trim();
        const btn   = $('#forgotBtn');

        $('#errorAlert, #successAlert').hide();

        if (!email) {
            showError('Please enter your email address.');
            $('#forgot_email').addClass('is-invalid');
            return;
        }

       
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('Please enter a valid email address.');
            $('#forgot_email').addClass('is-invalid');
            return;
        }

        btn.addClass('loading').prop('disabled', true);

        $.ajax({
            type:    'POST',
            url:     '../includes/process.php',
            data:    { forgot_password: 1, email: email },
            success: function (res) {
                btn.removeClass('loading').prop('disabled', false);

                if (res.trim() === 'success') {
                    showSuccess(email);
                } else {
                    showError(res.trim());
                }
            },
            error: function () {
                btn.removeClass('loading').prop('disabled', false);
                showError('Connection error. Please try again.');
            }
        });
    });

    
    $('#forgot_email').on('input', function () {
        $(this).removeClass('is-invalid');
        $('#errorAlert').hide();
    });

});
</script>
</body>
</html>