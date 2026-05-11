$(document).ready(function () {

    /* ── Eye Toggle ── */
    $('.toggle-eye').click(function () {
        const target = $(this).data('target');
        const input  = $('#' + target);
        const type   = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    /* ── Password Strength Bar ── */
    function getStrength(pw) {
        let score = 0;
        if (pw.length >= 8)          score++;
        if (/[A-Z]/.test(pw))        score++;
        if (/[0-9]/.test(pw))        score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    }

    const strengthColors = ['#f87171', '#fbbf24', '#60a5fa', '#4ade80'];
    const strengthLabels = ['Weak', 'Fair', 'Good', 'Strong'];

    $('#password').on('input', function () {
        const pw    = $(this).val();
        const score = getStrength(pw);
        $('.strength-bar-wrap span').each(function (i) {
            $(this).css('background', i < score ? strengthColors[score - 1] : '');
        });
        $('.strength-label').text(pw.length ? (strengthLabels[score - 1] || 'Weak') : '');
    });

    /* ── Real-time Field Validation ── */
    $('#firstname').on('blur', function () {
        const ok = /^[A-Z][a-zA-Z]*$/.test($(this).val().trim());
        $(this).toggleClass('is-invalid', !ok).toggleClass('is-valid', ok);
        $('#error-firstname').toggleClass('show', !ok);
    });

    $('#lastname').on('blur', function () {
        const ok = /^[A-Z][a-zA-Z]*$/.test($(this).val().trim());
        $(this).toggleClass('is-invalid', !ok).toggleClass('is-valid', ok);
        $('#error-lastname').toggleClass('show', !ok);
    });

    $('#phone').on('blur', function () {
        const ok = /^09[0-9]{9}$/.test($(this).val().trim());
        $(this).toggleClass('is-invalid', !ok).toggleClass('is-valid', ok);
        $('#error-phone').toggleClass('show', !ok);
    });

    $('#confirm_password').on('input', function () {
        const match = $(this).val() === $('#password').val();
        $(this).toggleClass('is-invalid', !match).toggleClass('is-valid', match);
    });

    /* ── Form Submit via AJAX ── */
    $('form').submit(function (e) {
        e.preventDefault();

        const firstname        = $('#firstname').val().trim();
        const lastname         = $('#lastname').val().trim();
        const email            = $('#email').val().trim();
        const phone            = $('#phone').val().trim();
        const password         = $('#password').val();
        const confirm_password = $('#confirm_password').val();

        if (password !== confirm_password) {
            Swal.fire({
                title: 'Passwords do not match!',
                text: 'Please make sure both password fields are the same.',
                icon: 'error',
                confirmButtonColor: '#d4a853',
                background: '#16161d', color: '#f4f0e8'
            });
            return;
        }

        const btn = $('#registerBtn');
        btn.addClass('loading').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: '../includes/process.php',
            data: {
                create: 1,
                firstname: firstname,
                lastname: lastname,
                email: email,
                phone: phone,
                password: password,
                confirm_password: confirm_password
            },
            success: function (response) {
                btn.removeClass('loading').prop('disabled', false);

                if (response.trim() === 'success') {
                    // process.php already set the session (auto-login)
                    // Show popup then go straight to homepage — already logged in
                    Swal.fire({
                        title: 'Welcome! \uD83C\uDF89',
                        text: 'Account created! Taking you home...',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#16161d', color: '#f4f0e8'
                    });
                    setTimeout(function () {
                        window.location.href = '/REGISTRATIONSFORM/index.php';
                    }, 1500);
                } else {
                    Swal.fire({
                        title: 'Registration Failed',
                        text: response,
                        icon: 'error',
                        confirmButtonColor: '#d4a853',
                        background: '#16161d', color: '#f4f0e8'
                    });
                }
            },
            error: function () {
                btn.removeClass('loading').prop('disabled', false);
                Swal.fire({
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#d4a853',
                    background: '#16161d', color: '#f4f0e8'
                });
            }
        });
    });

});