$(document).ready(function () {

    /* ── Eye Toggle ── */
    $('.toggle-eye').click(function () {
        const input = $('#password');
        const type  = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    /* ── Form Submit via AJAX ── */
    $('form').submit(function (e) {
        e.preventDefault();

        const email    = $('#email').val().trim();
        const password = $('#password').val();

        if (!email || !password) {
            Swal.fire({
                title: 'Missing Fields',
                text: 'Please enter your email and password.',
                icon: 'warning',
                confirmButtonColor: '#d4a853',
                background: '#16161d', color: '#f4f0e8'
            });
            return;
        }

        const btn = $('#loginBtn');
        btn.addClass('loading').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: '../includes/process.php',
            data: { login: 1, email: email, password: password },
            success: function (response) {
                btn.removeClass('loading').prop('disabled', false);

                if (response.trim() === 'success') {
                    // Show success popup then redirect to homepage (already logged in)
                    Swal.fire({
                        title: 'Welcome back! \uD83D\uDC4B',
                        text: 'Login successful.',
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
                        title: 'Login Failed',
                        text: response,
                        icon: 'error',
                        confirmButtonColor: '#d4a853',
                        background: '#16161d', color: '#f4f0e8'
                    });
                    $('#password').addClass('is-invalid');
                    setTimeout(function () {
                        $('#password').removeClass('is-invalid');
                    }, 2000);
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

    /* ── Clear error state on retype ── */
    $('input').on('input', function () {
        $(this).removeClass('is-invalid');
    });

});