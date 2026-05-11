document.addEventListener('DOMContentLoaded', function () {


    /* ============================================================
       SECTION 1: Scroll Shadow Effect
       — Adds a shadow to the navbar when the page is scrolled down
       ============================================================ */

    const navbar = document.getElementById('navbar');

    /**
     * Adds the .scrolled class to the navbar when the user
     * has scrolled more than 10px, which triggers a CSS box-shadow.
     * Removes it again when scrolled back to the top.
     */
    window.addEventListener('scroll', function () {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });


    /* ============================================================
       SECTION 2: Mobile Hamburger Menu Toggle
       — Opens and closes the nav link list on small screens
       ============================================================ */

    const navToggle = document.getElementById('navToggle');
    const navLinks  = document.getElementById('navLinks');

    if (navToggle && navLinks) {
        /**
         * Toggles the mobile menu open/closed by adding .open
         * to both the hamburger button (animates to X) and the
         * nav-links list (slides down via CSS max-height transition).
         */
        navToggle.addEventListener('click', function () {
            navToggle.classList.toggle('open');
            navLinks.classList.toggle('open');

            // Update aria-label for accessibility
            const isOpen = navToggle.classList.contains('open');
            navToggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
        });

        /**
         * Closes the mobile menu when the user clicks any nav link.
         * Prevents the menu staying open after navigating.
         */
        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navToggle.classList.remove('open');
                navLinks.classList.remove('open');
            });
        });
    }


    /* ============================================================
       SECTION 3: User Dropdown Toggle (Logged-in State)
       — Opens and closes the account dropdown menu
       ============================================================ */

    const userMenuBtn  = document.getElementById('userMenuBtn');
    const userItem     = userMenuBtn ? userMenuBtn.closest('.nav-user-item') : null;

    if (userMenuBtn && userItem) {
        /**
         * Toggles the .open class on the parent .nav-user-item,
         * which triggers the CSS dropdown fade-in animation.
         */
        userMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();  // Prevent the document click from immediately closing it
            userItem.classList.toggle('open');
        });

        /**
         * Closes the dropdown when the user clicks anywhere
         * outside the user menu area.
         */
        document.addEventListener('click', function () {
            userItem.classList.remove('open');
        });

        /**
         * Prevents clicks inside the dropdown from bubbling up
         * to the document listener above (which would close it).
         */
        userItem.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }


    /* ============================================================
       SECTION 4: Close Mobile Menu on Resize
       — Resets the mobile menu if the window is resized to desktop
       ============================================================ */

    /**
     * If the user resizes the window wider than 768px while
     * the mobile menu is open, close it automatically to avoid
     * a stuck open state.
     */
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            if (navToggle) navToggle.classList.remove('open');
            if (navLinks)  navLinks.classList.remove('open');
        }
    });

});