/**
 * FILE: admin.js
 * PAGE: Admin Panel (admin.php)
 * PATH: assets/scripts/admin.js
 * DESC: Handles all admin panel interactions —
 *       tab switching, table search filtering,
 *       booking confirm/cancel/delete,
 *       coach add/delete, and client delete
 *
 * DEPENDS ON: jQuery, SweetAlert2
 * POSTS TO:   ../includes/admin_process.php
 */


/* ============================================================
   SECTION 1: Tab Switching
   — Shows the correct content panel when a sidebar nav item is clicked
   ============================================================ */

// Maps tab names to their display titles shown in the topbar
const tabTitles = {
    dashboard: 'Overview',
    bookings:  'Bookings',
    coaches:   'Manage Coaches',
    clients:   'Clients',
    reports:   'Reports & Stats'
};

/**
 * Switch to the specified admin tab.
 * Hides all other panels and updates the topbar title.
 *
 * @param {string} name - Tab key e.g. 'bookings'
 * @param {Element|null} el - The nav-item element that was clicked
 */
function showTab(name, el) {
    // Hide all tab panels
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

    // Remove active state from all nav items
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

    // Show the selected panel
    document.getElementById('tab-' + name).classList.add('active');

    // Update the topbar title
    document.getElementById('topbarTitle').textContent = tabTitles[name];

    // Highlight the clicked nav item
    if (el) el.classList.add('active');
}


/* ============================================================
   SECTION 2: Table Search Filter
   — Filters table rows in real-time as the user types in the search box
   ============================================================ */

/**
 * Hides table rows that don't match the search input value.
 * Called via oninput on the search box elements.
 *
 * @param {HTMLInputElement} input - The search input element
 * @param {string} tableId         - ID of the <table> element to filter
 */
function filterTable(input, tableId) {
    const val = input.value.toLowerCase();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
}


/* ============================================================
   SECTION 3: Update Booking Status
   — Confirm or cancel a booking via AJAX
   ============================================================ */

/**
 * Shows a SweetAlert confirmation, then POSTs the status change
 * to admin_process.php. Updates the badge and removes action buttons inline.
 *
 * @param {number} id     - Booking row ID
 * @param {string} status - New status: 'confirmed' or 'cancelled'
 */
function updateBooking(id, status) {
    Swal.fire({
        title: status === 'confirmed' ? 'Confirm Booking?' : 'Cancel Booking?',
        text: `This will mark the booking as ${status}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: status === 'confirmed' ? '#4ade80' : '#f87171',
        cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8',
        confirmButtonText: 'Yes, proceed'
    }).then(result => {
        if (!result.isConfirmed) return;

        // POST to admin_process.php
        $.post('../includes/admin_process.php', { action: 'update_booking', id, status }, res => {
            if (res.trim() === 'success') {
                // Update the status badge text and colour class in-place
                const badge = document.getElementById('bstatus_' + id);
                badge.textContent = status;
                badge.className   = 'badge badge-' + status;

                // Remove the Confirm/Cancel buttons from the row since status changed
                const row = document.getElementById('brow_' + id);
                row.querySelectorAll('.btn-confirm, .btn-cancel').forEach(b => b.remove());

                Swal.fire({
                    icon: 'success', title: 'Updated!',
                    timer: 1400, showConfirmButton: false,
                    background: '#16161d', color: '#f4f0e8'
                });
            }
        });
    });
}


/* ============================================================
   SECTION 4: Delete Booking
   — Permanently removes a booking row from the database
   ============================================================ */

/**
 * Shows a warning confirmation, then deletes the booking via AJAX.
 * Removes the row from the DOM on success.
 *
 * @param {number} id - Booking row ID
 */
function deleteBooking(id) {
    Swal.fire({
        title: 'Delete Booking?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f87171', cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8'
    }).then(r => {
        if (!r.isConfirmed) return;

        $.post('../includes/admin_process.php', { action: 'delete_booking', id }, res => {
            if (res.trim() === 'success') {
                // Remove the row from the table without a page reload
                document.getElementById('brow_' + id)?.remove();

                Swal.fire({
                    icon: 'success', title: 'Deleted',
                    timer: 1200, showConfirmButton: false,
                    background: '#16161d', color: '#f4f0e8'
                });
            }
        });
    });
}


/* ============================================================
   SECTION 5: Add New Coach (Form Submission)
   — Submits the Add Coach form via AJAX to admin_process.php
   ============================================================ */

/**
 * Intercepts the #addCoachForm submission, POSTs all fields,
 * and shows a success or error SweetAlert response.
 */
$('#addCoachForm').submit(function (e) {
    e.preventDefault();  // Prevent normal form submission

    $.post(
        '../includes/admin_process.php',
        $(this).serialize() + '&action=add_coach',  // Include action param
        res => {
            if (res.trim() === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Coach Added!',
                    text: 'Refresh the page to see the new coach in the table.',
                    background: '#16161d', color: '#f4f0e8',
                    confirmButtonColor: '#d4a853'
                });
                this.reset();  // Clear the form fields
            } else {
                Swal.fire({
                    icon: 'error', title: 'Error',
                    text: res,
                    background: '#16161d', color: '#f4f0e8'
                });
            }
        }
    );
});


/* ============================================================
   SECTION 6: Delete Coach
   — Removes a coach record from the database
   ============================================================ */

/**
 * Confirms and deletes a coach via AJAX. Removes the table row on success.
 *
 * @param {number} id - Coach row ID
 */
function deleteCoach(id) {
    Swal.fire({
        title: 'Delete Coach?',
        text: 'All their bookings will also be affected.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f87171', cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8'
    }).then(r => {
        if (!r.isConfirmed) return;

        $.post('../includes/admin_process.php', { action: 'delete_coach', id }, res => {
            if (res.trim() === 'success') {
                document.getElementById('crow_' + id)?.remove();

                Swal.fire({
                    icon: 'success', title: 'Deleted',
                    timer: 1200, showConfirmButton: false,
                    background: '#16161d', color: '#f4f0e8'
                });
            }
        });
    });
}


/* ============================================================
   SECTION 7: Delete Client
   — Removes a client account and their bookings
   ============================================================ */

/**
 * Confirms and deletes a client via AJAX. Removes the table row on success.
 *
 * @param {number} id - Client row ID
 */
function deleteClient(id) {
    Swal.fire({
        title: 'Remove Client?',
        text: 'Their account and all bookings will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f87171', cancelButtonColor: '#3a3a4a',
        background: '#16161d', color: '#f4f0e8'
    }).then(r => {
        if (!r.isConfirmed) return;

        $.post('../includes/admin_process.php', { action: 'delete_client', id }, res => {
            if (res.trim() === 'success') {
                document.getElementById('clrow_' + id)?.remove();

                Swal.fire({
                    icon: 'success', title: 'Removed',
                    timer: 1200, showConfirmButton: false,
                    background: '#16161d', color: '#f4f0e8'
                });
            }
        });
    });
}


/* ============================================================
   SECTION 8: Mobile Menu Toggle
   — Shows the hamburger menu button only on small screens
   ============================================================ */

// Show the mobile hamburger button if the viewport is narrow
if (window.innerWidth <= 768) {
    document.getElementById('menuBtn').style.display = 'block';
}