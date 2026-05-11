<?php require '../config/config.php';

/* ── SECTION 1: REGISTRATION ── */
if (isset($_POST['create'])) {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = $_POST['password'];

    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $firstname)) { echo 'First name must start with capital letter.'; exit(); }
    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $lastname))  { echo 'Last name must start with capital letter.'; exit(); }
    if (!preg_match('/^09[0-9]{9}$/', $phone))         { echo 'Phone number must start with 09 and be 11 digits.'; exit(); }

    $check = $db->prepare('SELECT id FROM clients WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) { echo 'This email is already registered. Please log in instead.'; exit(); }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $db->prepare('INSERT INTO clients (firstname, lastname, email, phonenumber, password) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$firstname, $lastname, $email, $phone, $hashed]);
        $_SESSION['client_id'] = $db->lastInsertId();
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname']  = $lastname;
        $_SESSION['email']     = $email;
        $_SESSION['is_admin']  = 0;
        echo 'success';
    } catch (PDOException $e) {
        echo ($e->getCode() == 23000) ? 'This email is already registered.' : 'Registration failed. Please try again.';
    }
    exit();
}

/* ── SECTION 2: LOGIN ── */
if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($email) || empty($password)) { echo 'Please enter your email and password.'; exit(); }
    try {
        $stmt = $db->prepare('SELECT * FROM clients WHERE email = ?');
        $stmt->execute([$email]);
        $client = $stmt->fetch();
        if ($client && password_verify($password, $client['password'])) {
            $_SESSION['client_id'] = $client['id'];
            $_SESSION['firstname'] = $client['firstname'];
            $_SESSION['lastname']  = $client['lastname'];
            $_SESSION['email']     = $client['email'];
            $_SESSION['is_admin']  = $client['is_admin'];
            echo 'success';
        } else {
            echo 'Invalid email or password. Please try again.';
        }
    } catch (PDOException $e) {
        echo 'Login failed. Please try again.';
    }
    exit();
}

/* ── SECTION 3: LOGOUT ── */
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: /REGISTRATIONSFORM/index.php');
    exit();
}

/* ── SECTION 4: BOOKING ── */
if (isset($_POST['book'])) {
    if (!isset($_SESSION['client_id'])) { echo 'Please log in to book a session.'; exit(); }

    $client_id    = $_SESSION['client_id'];
    $category     = trim($_POST['category']     ?? '');
    $coach_ids    = json_decode($_POST['coach_ids'] ?? '[]', true);
    $book_date    = trim($_POST['book_date']    ?? '');
    $book_time    = trim($_POST['book_time']    ?? '');
    $session_type = trim($_POST['session_type'] ?? '');
    $duration     = (int)($_POST['duration']    ?? 0);
    $notes        = trim($_POST['notes']        ?? '');

    if (empty($category) || empty($coach_ids) || empty($book_date) || empty($book_time)) {
        echo 'Please fill in all required fields.'; exit();
    }
    if (!is_array($coach_ids) || count($coach_ids) === 0) {
        echo 'No coaches selected.'; exit();
    }

    try {
        $stmt = $db->prepare("
            INSERT INTO bookings
                (client_id, coach_id, category, book_date, book_time, session_type, duration_minutes, notes, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        foreach ($coach_ids as $coach_id) {
            $stmt->execute([$client_id, (int)$coach_id, $category, $book_date, $book_time, $session_type, $duration, $notes]);
        }
        echo 'success';
    } catch (PDOException $e) {
        echo 'Booking failed. Please try again.';
    }
    exit();
}

/* ── SECTION 5: CANCEL BOOKING (client) ── */
if (isset($_POST['cancel_booking'])) {
    if (!isset($_SESSION['client_id'])) { echo 'Not logged in.'; exit(); }

    $booking_id = (int)($_POST['booking_id'] ?? 0);
    $client_id  = $_SESSION['client_id'];

    try {
        $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND client_id = ? AND status = 'pending'");
        $stmt->execute([$booking_id, $client_id]);
        echo $stmt->rowCount() > 0 ? 'success' : 'Booking not found or already processed.';
    } catch (PDOException $e) {
        echo 'Failed to cancel booking.';
    }
    exit();
}

/* ── SECTION 6: UPDATE PROFILE ── */
if (isset($_POST['update_profile'])) {
    if (!isset($_SESSION['client_id'])) { echo 'Not logged in.'; exit(); }

    $client_id = $_SESSION['client_id'];
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $phone     = trim($_POST['phone']     ?? '');

    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $firstname)) { echo 'First name must start with a capital letter.'; exit(); }
    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $lastname))  { echo 'Last name must start with a capital letter.'; exit(); }
    if (!preg_match('/^09[0-9]{9}$/', $phone))         { echo 'Phone must start with 09 and be 11 digits.'; exit(); }
    if (empty($email))                                  { echo 'Email is required.'; exit(); }

    // Check if email is taken by another account
    $check = $db->prepare('SELECT id FROM clients WHERE email = ? AND id != ?');
    $check->execute([$email, $client_id]);
    if ($check->fetch()) { echo 'This email is already used by another account.'; exit(); }

    try {
        $stmt = $db->prepare("UPDATE clients SET firstname=?, lastname=?, email=?, phonenumber=? WHERE id=?");
        $stmt->execute([$firstname, $lastname, $email, $phone, $client_id]);

        // Update session so navbar shows new name immediately
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname']  = $lastname;
        $_SESSION['email']     = $email;

        echo 'success';
    } catch (PDOException $e) {
        echo 'Update failed. Please try again.';
    }
    exit();
}

/* ── SECTION 7: CHANGE PASSWORD ── */
if (isset($_POST['change_password'])) {
    if (!isset($_SESSION['client_id'])) { echo 'Not logged in.'; exit(); }

    $client_id       = $_SESSION['client_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password']     ?? '';
    $confirm_new      = $_POST['confirm_new_password'] ?? '';

    if (empty($current_password) || empty($new_password)) { echo 'All fields are required.'; exit(); }
    if ($new_password !== $confirm_new) { echo 'New passwords do not match.'; exit(); }
    if (strlen($new_password) < 6) { echo 'New password must be at least 6 characters.'; exit(); }

    // Get current password hash from DB
    $stmt = $db->prepare("SELECT password FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($current_password, $row['password'])) {
        echo 'Current password is incorrect.'; exit();
    }

    try {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $db->prepare("UPDATE clients SET password = ? WHERE id = ?")->execute([$hashed, $client_id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to update password.';
    }
    exit();
}

/* ── Update Profile ── */
if (isset($_POST['update_profile'])) {
    if (!isset($_SESSION['client_id'])) { echo 'Not logged in.'; exit(); }

    $id        = $_SESSION['client_id'];
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $phone     = trim($_POST['phone']     ?? '');

    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $firstname)) { echo 'First name must start with a capital letter.'; exit(); }
    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $lastname))  { echo 'Last name must start with a capital letter.'; exit(); }
    if (!empty($phone) && !preg_match('/^09[0-9]{9}$/', $phone)) { echo 'Phone must start with 09 and be 11 digits.'; exit(); }

    // Check email not taken by another user
    $check = $db->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
    $check->execute([$email, $id]);
    if ($check->fetch()) { echo 'This email is already used by another account.'; exit(); }

    try {
        $stmt = $db->prepare("UPDATE clients SET firstname=?, lastname=?, email=?, phonenumber=? WHERE id=?");
        $stmt->execute([$firstname, $lastname, $email, $phone, $id]);

        // Update session with new name
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname']  = $lastname;
        $_SESSION['email']     = $email;

        echo 'success';
    } catch (PDOException $e) {
        echo 'Profile update failed. Please try again.';
    }
    exit();
}

/* ── Change Password ── */
if (isset($_POST['change_password'])) {
    if (!isset($_SESSION['client_id'])) { echo 'Not logged in.'; exit(); }

    $id           = $_SESSION['client_id'];
    $current_pw   = $_POST['current_password']   ?? '';
    $new_pw       = $_POST['new_password']        ?? '';
    $confirm_pw   = $_POST['confirm_new_password'] ?? '';

    if (empty($current_pw) || empty($new_pw) || empty($confirm_pw)) {
        echo 'Please fill in all password fields.'; exit();
    }

    if ($new_pw !== $confirm_pw) { echo 'New passwords do not match.'; exit(); }
    if (strlen($new_pw) < 6)     { echo 'New password must be at least 6 characters.'; exit(); }

    // Get current password hash
    $stmt = $db->prepare("SELECT password FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($current_pw, $row['password'])) {
        echo 'Current password is incorrect.'; exit();
    }

    try {
        $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
        $db->prepare("UPDATE clients SET password = ? WHERE id = ?")->execute([$hashed, $id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Password update failed. Please try again.';
    }
    exit();
}