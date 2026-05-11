/* ── Tab Switching ── */
function switchTab(name, el) {
    document.querySelectorAll('.dash-tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.dash-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    if (el) el.classList.add('active');
}

/* ── Eye Toggle ── */
document.querySelectorAll('.toggle-eye').forEach(function(icon) {
    icon.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        const input  = document.getElementById(target);
        const type   = input.type === 'password' ? 'text' : 'password';
        input.type   = type;
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
});

/* ── Cancel Booking ── */
function cancelBooking(id) {
    Swal.fire({
        title: 'Cancel Booking?',
        text: 'Are you sure you want to cancel this booking?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f87171',
        cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8',
        confirmButtonText: 'Yes, cancel it'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('../includes/process.php', { cancel_booking: 1, booking_id: id }, function(res) {
            if (res.trim() === 'success') {
                const card = document.getElementById('bcard_' + id);
                card.querySelector('.dash-status').textContent = 'Cancelled';
                card.querySelector('.dash-status').className = 'dash-status dash-status-cancelled';
                card.querySelector('.btn-cancel-booking')?.remove();
                Swal.fire({
                    icon: 'success', title: 'Booking Cancelled',
                    timer: 1500, showConfirmButton: false,
                    background: '#16161d', color: '#f4f0e8'
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res, background: '#16161d', color: '#f4f0e8' });
            }
        });
    });
}

/* ── Edit Profile ── */
$('#editProfileForm').submit(function(e) {
    e.preventDefault();
    const btn = $('#btnSaveProfile');
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

    $.post('../includes/process.php', $(this).serialize() + '&update_profile=1', function(res) {
        btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save Changes');
        if (res.trim() === 'success') {
            Swal.fire({
                icon: 'success', title: 'Profile Updated!',
                text: 'Your information has been saved.',
                background: '#16161d', color: '#f4f0e8',
                confirmButtonColor: '#d4a853'
            }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Update Failed', text: res, background: '#16161d', color: '#f4f0e8' });
        }
    });
});

/* ── Change Password ── */
$('#changePasswordForm').submit(function(e) {
    e.preventDefault();
    const newPw  = $('#new_password').val();
    const confPw = $('#confirm_new_password').val();

    if (newPw !== confPw) {
        Swal.fire({
            icon: 'error', title: 'Passwords do not match!',
            background: '#16161d', color: '#f4f0e8', confirmButtonColor: '#d4a853'
        });
        return;
    }

    const btn = $('#btnChangePassword');
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Updating...');

    $.post('../includes/process.php', $(this).serialize() + '&change_password=1', function(res) {
        btn.prop('disabled', false).html('<i class="fa-solid fa-key"></i> Update Password');
        if (res.trim() === 'success') {
            Swal.fire({
                icon: 'success', title: 'Password Changed!',
                text: 'Your password has been updated.',
                background: '#16161d', color: '#f4f0e8',
                confirmButtonColor: '#d4a853'
            });
            document.getElementById('changePasswordForm').reset();
        } else {
            Swal.fire({ icon: 'error', title: 'Failed', text: res, background: '#16161d', color: '#f4f0e8' });
        }
    });
});