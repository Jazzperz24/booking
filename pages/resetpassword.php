<?php

$page = 'login';
$base = '../';
require '../config/config.php';

$token = trim($_GET['token'] ?? '');

if (empty($token)) { ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Invalid Link - Kaya Pa?</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
<link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/login.css">
</head><body>
<?php require '../includes/navbar.php'; ?>
<div class="login-page"><div class="login-card" style="text-align:center">
    <div class="card-icon"><i class="fa-solid fa-triangle-exclamation" style="color:#f87171"></i></div>
    <h1 style="color:#f87171">Invalid <span style="color:#f87171">Link</span></h1>
    <p class="card-subtitle">This password reset link is missing or broken.</p>
    <a href="forgotpassword.php" class="btn-login" style="display:block;text-decoration:none;margin-top:16px;text-align:center">
        <span class="btn-text"><i class="fa-solid fa-paper-plane"></i> Request New Link</span>
    </a>
</div></div>
</body></html>
<?php exit(); }

$now  = date('Y-m-d H:i:s');
$stmt = $db->prepare("SELECT * FROM clients WHERE reset_token = ? AND reset_expire > ?");
$stmt->execute([$token, $now]);
$user = $stmt->fetch();

if (!$user) { ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Expired Link - Kaya Pa?</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
<link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/login.css">
</head><body>
<?php require '../includes/navbar.php'; ?>
<div class="login-page"><div class="login-card" style="text-align:center">
    <div class="card-icon"><i class="fa-solid fa-clock" style="color:#fbbf24"></i></div>
    <h1>Link <span>Expired</span></h1>
    <p class="card-subtitle">
        This reset link has expired or already been used.<br>
        Links are valid for <strong style="color:var(--gold)">1 hour</strong> only.
    </p>
    <a href="forgotpassword.php" class="btn-login" style="display:block;text-decoration:none;margin-top:16px;text-align:center">
        <span class="btn-text"><i class="fa-solid fa-paper-plane"></i> Request New Link</span>
    </a>
    <p class="card-footer-text" style="margin-top:16px">
        <a href="loginpage.php"><i class="fa-solid fa-arrow-left"></i> Back to Sign In</a>
    </p>
</div></div>
</body></html>
<?php exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Kaya Pa?</title>
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
        .alert-error {
            background: rgba(248,113,113,0.10);
            border: 1px solid rgba(248,113,113,0.30);
            color: #f87171;
        }
        .strength-wrap {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }
        .strength-wrap span {
            flex: 1;
            height: 4px;
            border-radius: 2px;
            background: var(--surface2);
            transition: background .3s;
        }
        .strength-label {
            font-size: 11px;
            color: var(--muted);
            margin-top: 5px;
            display: block;
        }
        .match-hint {
            font-size: 11px;
            margin-top: 5px;
            display: none;
        }
        .reset-for {
            background: rgba(212,168,83,0.08);
            border: 1px solid rgba(212,168,83,0.20);
            border-radius: 10px;
            padding: 11px 15px;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .reset-for i { color: var(--gold); font-size: 15px; }
        .reset-for strong { color: var(--text); }
    </style>
</head>
<body>

<?php require '../includes/navbar.php'; ?>

<div class="login-page">
    <div class="login-card">

        <div class="card-icon">
            <i class="fa-solid fa-lock-open" style="color:var(--gold)"></i>
        </div>

        <h1>Reset <span>Password</span></h1>
        <p class="card-subtitle">Choose a strong new password for your account.</p>

        <div class="reset-for">
            <i class="fa-solid fa-circle-user"></i>
            <span>Resetting password for
                <strong><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></strong>
                &mdash; <?= htmlspecialchars($user['email']) ?>
            </span>
        </div>

        <div class="alert alert-error" id="errorAlert">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right:6px"></i>
            <span id="errorMsg"></span>
        </div>

        <form id="resetForm" novalidate>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label for="new_pw">
                    <i class="fa-solid fa-lock" style="color:var(--gold);margin-right:5px"></i>
                    New Password
                </label>
                <div class="input-icon-wrap">
                    <input type="password" id="new_pw" name="password"
                           placeholder="Min. 6 characters" autocomplete="new-password" required>
                    <i class="fa-solid fa-eye toggle-eye" id="eye1"></i>
                </div>

                <div class="strength-wrap">
                    <span id="sb1"></span>
                    <span id="sb2"></span>
                    <span id="sb3"></span>
                    <span id="sb4"></span>
                </div>
                <span class="strength-label" id="strengthLabel"></span>
            </div>

            <div class="form-group">
                <label for="confirm_pw">
                    <i class="fa-solid fa-lock" style="color:var(--gold);margin-right:5px"></i>
                    Confirm New Password
                </label>
                <div class="input-icon-wrap">
                    <input type="password" id="confirm_pw" name="confirm_password"
                           placeholder="Repeat your password" autocomplete="new-password" required>
                    <i class="fa-solid fa-eye toggle-eye" id="eye2"></i>
                </div>
                <span class="match-hint" id="matchHint"></span>
            </div>

            <button type="submit" class="btn-login" id="resetBtn">
                <span class="spinner"></span>
                <span class="btn-text">
                    <i class="fa-solid fa-shield-halved"></i>&nbsp; Update Password
                </span>
            </button>
        </form>

        <p class="card-footer-text" style="margin-top:20px">
            <a href="loginpage.php">
                <i class="fa-solid fa-arrow-left"></i> Back to Sign In
            </a>
        </p>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>
<script>
$(function () {

    function eyeToggle(btnId, inputId) {
        $('#' + btnId).on('click', function () {
            const inp = $('#' + inputId);
            inp.attr('type', inp.attr('type') === 'password' ? 'text' : 'password');
            $(this).toggleClass('fa-eye fa-eye-slash');
        });
    }
    eyeToggle('eye1', 'new_pw');
    eyeToggle('eye2', 'confirm_pw');

    const colors = { 1:'#f87171', 2:'#fbbf24', 3:'#60a5fa', 4:'#4ade80' };
    const labels = { 1:'Weak', 2:'Fair', 3:'Good', 4:'Strong' };

    function strength(pw) {
        let s = 0;
        if (pw.length >= 8)          s++;
        if (/[A-Z]/.test(pw))        s++;
        if (/[0-9]/.test(pw))        s++;
        if (/[^A-Za-z0-9]/.test(pw)) s++;
        return s;
    }

    $('#new_pw').on('input', function () {
        const pw = $(this).val();
        const sc = strength(pw);
        for (let i = 1; i <= 4; i++) {
            $('#sb' + i).css('background', i <= sc ? colors[sc] : '#1e1e28');
        }
        $('#strengthLabel').text(pw.length ? (labels[sc] || 'Weak') : '');
        checkMatch();
    });

    function checkMatch() {
        const pw  = $('#new_pw').val();
        const cpw = $('#confirm_pw').val();
        if (!cpw.length) { $('#matchHint').hide(); return; }
        if (pw === cpw) {
            $('#matchHint').css('color','#4ade80').text('✓ Passwords match').show();
            $('#confirm_pw').removeClass('is-invalid');
        } else {
            $('#matchHint').css('color','#f87171').text('✗ Passwords do not match').show();
            $('#confirm_pw').addClass('is-invalid');
        }
    }
    $('#confirm_pw').on('input', checkMatch);

    $('#resetForm').on('submit', function (e) {
        e.preventDefault();

        const pw  = $('#new_pw').val();
        const cpw = $('#confirm_pw').val();
        const btn = $('#resetBtn');

        $('#errorAlert').hide();

        if (pw.length < 6) {
            $('#errorMsg').text('Password must be at least 6 characters.');
            $('#errorAlert').fadeIn(200);
            return;
        }
        if (pw !== cpw) {
            $('#errorMsg').text('Passwords do not match.');
            $('#errorAlert').fadeIn(200);
            return;
        }

        btn.addClass('loading').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url:  '../includes/process.php',
            data: $(this).serialize() + '&reset_password=1',
            success: function (res) {
                btn.removeClass('loading').prop('disabled', false);

                if (res.trim() === 'success') {
                    Swal.fire({
                        icon:             'success',
                        title:            'Password Updated! 🎉',
                        html:             'Your new password has been saved.<br>You can now sign in.',
                        confirmButtonText:'Go to Sign In',
                        confirmButtonColor:'#d4a853',
                        background:       '#16161d',
                        color:            '#f4f0e8'
                    }).then(function () {
                        window.location.href = 'loginpage.php';
                    });
                } else {
                    $('#errorMsg').text(res.trim());
                    $('#errorAlert').fadeIn(200);
                }
            },
            error: function () {
                btn.removeClass('loading').prop('disabled', false);
                $('#errorMsg').text('Connection error. Please try again.');
                $('#errorAlert').fadeIn(200);
            }
        });
    });

});
</script>
</body>
</html>