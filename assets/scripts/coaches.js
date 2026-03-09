/**
 * FILE: coaches.js
 * PAGE: Coach Selection (coaches.php)
 * PATH: assets/scripts/coaches.js
 * DESC: Handles the 2-step coach selection flow —
 *       category selection, coach grid rendering,
 *       multi-select toggle (max 3), and routing to booking
 *
 * DEPENDS ON: jQuery, SweetAlert2, coachData (PHP-injected JSON)
 */


/* ============================================================
   SECTION 1: State Variables
   — Track what the user has selected across both steps
   ============================================================ */

let selectedCategory = null;   // Currently chosen category string e.g. "Dance"
let selectedCoaches  = [];     // Array of selected coach objects (max 3)

// Emoji map for each category — used when rendering coach avatars
const catEmoji = {
    'Dance':         '💃',
    'Fitness':       '🏋️',
    'Sports':        '⚽',
    'Wellness/Yoga': '🧘',
    'Belle':         '✨'
};


/* ============================================================
   SECTION 2: Step Navigation
   — Functions to move between Step 1 and Step 2
   ============================================================ */

/**
 * Go back to Step 1 and clear any previously selected coaches
 */
function goStep1() {
    selectedCoaches = [];
    setStep(1);
}

/**
 * Advance to Step 2 — only if a category has been chosen.
 * Also triggers the coach grid to be built for the chosen category.
 */
function goStep2() {
    if (!selectedCategory) return;
    setStep(2);
    buildCoachGrid(selectedCategory);
}

/**
 * Set the active step — updates the panel visibility and the step indicator bar
 * @param {number} n - Step number (1 or 2)
 */
function setStep(n) {
    // Hide all panels, show the target one
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel' + n).classList.add('active');

    // Update step circles: done = past step, active = current step
    [1, 2].forEach(i => {
        const item = document.getElementById('stepItem' + i);
        item.classList.remove('active', 'done');
        if (i < n) item.classList.add('done');
        if (i === n) item.classList.add('active');
    });

    // Update the connecting line between steps
    document.getElementById('stepLine1').classList.toggle('done', n > 1);

    // Scroll back to the top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}


/* ============================================================
   SECTION 3: Category Selection (Step 1)
   — Highlights the clicked category card and enables the Next button
   ============================================================ */

/**
 * Called when a category card is clicked.
 * Stores the selection and marks the card as selected.
 * @param {string} cat - Category name e.g. "Fitness"
 */
function selectCategory(cat) {
    selectedCategory = cat;
    selectedCoaches  = [];  // Reset coach selection when category changes

    // Toggle .selected class on all category cards
    document.querySelectorAll('.cat-card').forEach(c =>
        c.classList.toggle('selected', c.dataset.category === cat)
    );

    // Enable the "Continue" button now that a category is chosen
    document.getElementById('btnNext1').disabled = false;
}


/* ============================================================
   SECTION 4: Coach Grid Builder (Step 2)
   — Dynamically renders coach cards from the PHP-injected coachData
   ============================================================ */

/**
 * Renders coach cards for the selected category into #coachesGrid
 * @param {string} cat - The currently selected category
 */
function buildCoachGrid(cat) {
    const grid = document.getElementById('coachesGrid');
    const list = coachData[cat] || [];

    // Reset counter and disable Next button until at least 1 coach is selected
    document.getElementById('selectedCount').textContent = '0';
    document.getElementById('btnNext2').disabled = true;

    // Show empty state if no coaches exist for this category
    if (!list.length) {
        grid.innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-user-slash"></i>
                No coaches available in this category yet.
            </div>`;
        return;
    }

    // Render a card for each coach
    grid.innerHTML = list.map(c => `
        <div class="coach-card" id="coach_${c.id}" onclick="toggleCoach(${c.id})">
            <div class="coach-check"><i class="fa-solid fa-check"></i></div>
            <div class="coach-avatar">${catEmoji[cat] || '👤'}</div>
            <div class="coach-name">${esc(c.name)}</div>
            <div class="coach-specialty">${esc(c.specialty || cat)}</div>
            <div class="coach-bio">${esc(c.bio || 'Professional coach.')}</div>
            <div class="coach-rate">
                Rate: <strong>₱${Number(c.rate || 0).toLocaleString()}/hr</strong>
            </div>
        </div>
    `).join('');
}


/* ============================================================
   SECTION 5: Coach Toggle (Step 2)
   — Handles selecting and deselecting individual coach cards
   ============================================================ */

/**
 * Toggles a coach card selected/deselected.
 * Enforces the 3-coach maximum limit.
 * @param {number|string} id - Coach ID
 */
function toggleCoach(id) {
    const list  = coachData[selectedCategory] || [];
    const coach = list.find(c => c.id == id);
    if (!coach) return;

    const card = document.getElementById('coach_' + id);
    const idx  = selectedCoaches.findIndex(c => c.id == id);

    if (idx > -1) {
        // ── Deselect: remove from array, un-highlight card ──
        selectedCoaches.splice(idx, 1);
        card.classList.remove('selected');
    } else {
        // ── Select: enforce max 3 coaches ──
        if (selectedCoaches.length >= 3) {
            Swal.fire({
                icon: 'warning',
                title: 'Maximum 3 coaches',
                text: 'You can only select up to 3 coaches per booking.',
                confirmButtonColor: '#d4a853'
            });
            return;
        }
        selectedCoaches.push(coach);
        card.classList.add('selected');
    }

    // Update the "X / 3 selected" counter display
    document.getElementById('selectedCount').textContent = selectedCoaches.length;

    // Disable all non-selected cards once 3 are chosen
    document.querySelectorAll('.coach-card').forEach(c => {
        const cid = c.id.replace('coach_', '');
        const isSelected = selectedCoaches.some(s => s.id == cid);
        c.classList.toggle('disabled', selectedCoaches.length >= 3 && !isSelected);
    });

    // Enable the "Proceed to Booking" button once at least 1 coach is selected
    document.getElementById('btnNext2').disabled = selectedCoaches.length === 0;
}


/* ============================================================
   SECTION 6: Proceed to Booking
   — Saves selection to sessionStorage and redirects to booking.php
   ============================================================ */

/**
 * Stores the chosen category and coaches in sessionStorage,
 * then navigates to the booking page.
 */
function goToBooking() {
    if (!selectedCoaches.length) return;

    // Save for booking.php to read on load
    sessionStorage.setItem('selectedCategory', selectedCategory);
    sessionStorage.setItem('selectedCoaches', JSON.stringify(selectedCoaches));

    window.location.href = 'booking.php';
}


/* ============================================================
   SECTION 7: Utility
   ============================================================ */

/**
 * Escapes HTML special characters to prevent XSS when
 * injecting coach data strings into innerHTML
 * @param {string} s - Raw string
 * @returns {string} - Escaped string safe for innerHTML
 */
function esc(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}