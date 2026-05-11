/* ── Tab Switching ── */
const tabTitles = {
    dashboard: 'Overview', bookings: 'Bookings',
    coaches: 'Manage Coaches', clients: 'Clients', reports: 'Reports & Stats'
};

function showTab(name, el) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    document.getElementById('topbarTitle').textContent = tabTitles[name];
    if (el) el.classList.add('active');
}

/* ── Table Filter ── */
function filterTable(input, tableId) {
    const val = input.value.toLowerCase();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
}

/* ── Modal Helpers ── */
function closeModal(id) { document.getElementById(id).style.display = 'none'; }
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) e.target.style.display = 'none';
});

/* ── Update Booking ── */
function updateBooking(id, status) {
    Swal.fire({
        title: status === 'confirmed' ? 'Confirm Booking?' : 'Cancel Booking?',
        text: `This will mark the booking as ${status}.`,
        icon: 'question', showCancelButton: true,
        confirmButtonColor: status === 'confirmed' ? '#4ade80' : '#f87171',
        cancelButtonColor: '#3a3a4a', background: '#16161d', color: '#f4f0e8',
        confirmButtonText: 'Yes, proceed'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('../includes/admin_process.php', { action: 'update_booking', id, status }, res => {
            if (res.trim() === 'success') {
                const badge = document.getElementById('bstatus_' + id);
                badge.textContent = status;
                badge.className = 'badge badge-' + status;
                const row = document.getElementById('brow_' + id);
                row.querySelectorAll('.btn-confirm, .btn-cancel').forEach(b => b.remove());
                Swal.fire({ icon: 'success', title: 'Updated!', timer: 1400, showConfirmButton: false, background: '#16161d', color: '#f4f0e8' });
            }
        });
    });
}

/* ── Delete Booking ── */
function deleteBooking(id) {
    Swal.fire({
        title: 'Delete Booking?', text: 'This cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#f87171', cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.post('../includes/admin_process.php', { action: 'delete_booking', id }, res => {
            if (res.trim() === 'success') {
                document.getElementById('brow_' + id)?.remove();
                Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false, background: '#16161d', color: '#f4f0e8' });
            }
        });
    });
}

/* ── Add Coach (with photo upload) ── */
$('#addCoachForm').submit(function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'add_coach');
    $.ajax({
        url: '../includes/admin_process.php',
        type: 'POST', data: formData,
        processData: false, contentType: false,
        success: function(res) {
            if (res.trim() === 'success') {
                Swal.fire({
                    icon: 'success', title: 'Coach Added!',
                    text: 'Refresh to see the new coach.',
                    background: '#16161d', color: '#f4f0e8', confirmButtonColor: '#d4a853'
                }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res, background: '#16161d', color: '#f4f0e8' });
            }
        }
    });
});

/* ── Photo Preview ── */
document.getElementById('coachPhoto')?.addEventListener('change', function() {
    const preview = document.getElementById('photoPreview');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(this.files[0]);
    }
});

/* ── Open Edit Coach Modal ── */
function openEditCoach(id, name, category, specialty, rate, bio, photo) {
    document.getElementById('edit_coach_id').value       = id;
    document.getElementById('edit_coach_name').value     = name;
    document.getElementById('edit_coach_category').value = category;
    document.getElementById('edit_coach_specialty').value = specialty;
    document.getElementById('edit_coach_rate').value     = rate;
    document.getElementById('edit_coach_bio').value      = bio;
    const photoDiv = document.getElementById('edit_coach_current_photo');
    if (photo) {
        photoDiv.innerHTML = `<img src="/REGISTRATIONSFORM/assets/images/coaches/${photo}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid var(--gold)"><span style="font-size:12px;color:var(--muted);margin-left:8px">Current photo</span>`;
    } else {
        photoDiv.innerHTML = '<span style="font-size:12px;color:var(--muted)">No photo uploaded</span>';
    }
    document.getElementById('editCoachModal').style.display = 'flex';
}

/* ── Submit Edit Coach ── */
$('#editCoachForm').submit(function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'edit_coach');
    $.ajax({
        url: '../includes/admin_process.php',
        type: 'POST', data: formData,
        processData: false, contentType: false,
        success: function(res) {
            if (res.trim() === 'success') {
                Swal.fire({ icon: 'success', title: 'Coach Updated!', timer: 1400, showConfirmButton: false, background: '#16161d', color: '#f4f0e8' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res, background: '#16161d', color: '#f4f0e8' });
            }
        }
    });
});

/* ── Delete Coach ── */
function deleteCoach(id) {
    Swal.fire({
        title: 'Delete Coach?', text: 'All their bookings will also be affected.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#f87171', cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.post('../includes/admin_process.php', { action: 'delete_coach', id }, res => {
            if (res.trim() === 'success') {
                document.getElementById('crow_' + id)?.remove();
                Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false, background: '#16161d', color: '#f4f0e8' });
            }
        });
    });
}

/* ── Open Edit Client Modal ── */
function openEditClient(id, firstname, lastname, email, phone) {
    document.getElementById('edit_client_id').value        = id;
    document.getElementById('edit_client_firstname').value = firstname;
    document.getElementById('edit_client_lastname').value  = lastname;
    document.getElementById('edit_client_email').value     = email;
    document.getElementById('edit_client_phone').value     = phone;
    document.getElementById('editClientModal').style.display = 'flex';
}

/* ── Submit Edit Client ── */
$('#editClientForm').submit(function(e) {
    e.preventDefault();
    $.post('../includes/admin_process.php', $(this).serialize() + '&action=edit_client', function(res) {
        if (res.trim() === 'success') {
            Swal.fire({ icon: 'success', title: 'Client Updated!', timer: 1400, showConfirmButton: false, background: '#16161d', color: '#f4f0e8' })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res, background: '#16161d', color: '#f4f0e8' });
        }
    });
});

/* ── Delete Client ── */
function deleteClient(id) {
    Swal.fire({
        title: 'Remove Client?', text: 'Their account and all bookings will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#f87171', cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.post('../includes/admin_process.php', { action: 'delete_client', id }, res => {
            if (res.trim() === 'success') {
                document.getElementById('clrow_' + id)?.remove();
                Swal.fire({ icon: 'success', title: 'Removed', timer: 1200, showConfirmButton: false, background: '#16161d', color: '#f4f0e8' });
            }
        });
    });
}

/* ── Mobile Menu ── */
if (window.innerWidth <= 768) document.getElementById('menuBtn').style.display = 'block';