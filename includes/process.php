<?php

// ── PHPMailer autoload ──
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

require '../config/config.php';

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

    $check = $db->prepare('SELECT id FROM clients WHERE email = ? AND id != ?');
    $check->execute([$email, $client_id]);
    if ($check->fetch()) { echo 'This email is already used by another account.'; exit(); }

    try {
        $stmt = $db->prepare("UPDATE clients SET firstname=?, lastname=?, email=?, phonenumber=? WHERE id=?");
        $stmt->execute([$firstname, $lastname, $email, $phone, $client_id]);
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

    $client_id        = $_SESSION['client_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password']      ?? '';
    $confirm_new      = $_POST['confirm_new_password'] ?? '';

    if (empty($current_password) || empty($new_password)) { echo 'All fields are required.'; exit(); }
    if ($new_password !== $confirm_new) { echo 'New passwords do not match.'; exit(); }
    if (strlen($new_password) < 6) { echo 'New password must be at least 6 characters.'; exit(); }

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

/* ════════════════════════════════════════════════════════════
   SECTION 8: FORGOT PASSWORD
   — Validates email, generates token, stores it in DB,
     then sends a reset link via PHPMailer (Gmail SMTP).
   ════════════════════════════════════════════════════════════ */
if (isset($_POST['forgot_password'])) {

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        echo 'Please enter your email address.';
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Please enter a valid email address.';
        exit();
    }

    // Check if email exists in the database
    $stmt = $db->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Security: always say "success" even if email not found
    // (prevents attackers from knowing which emails are registered)
    if (!$user) {
        echo 'success'; // Silent fail — don't reveal if email exists
        exit();
    }

    // Generate a secure unique token
    $token = bin2hex(random_bytes(32));

    // Store expiry using PHP time (avoids MySQL timezone mismatch)
    $expire = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now

    // Save token + expiry to the database
    $update = $db->prepare("UPDATE clients SET reset_token = ?, reset_expire = ? WHERE email = ?");
    $update->execute([$token, $expire, $email]);

    // Build the reset link
    $resetLink = "http://localhost/REGISTRATIONSFORM/pages/resetpassword.php?token=" . $token;

    // ── Send email via PHPMailer ──
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

    
        $mail->Username   = 'garzjazz22@gmail.com';
    
        $mail->Password   = 'myzhkbbhewlnrjtm';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        
        $mail->addAddress($email); // sends to whoever requested the reset

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Kaya Pa? Password';
        $mail->Body    = "
            <div style='font-family:Arial,sans-serif;max-width:500px;margin:0 auto;
                        padding:32px;background:#0e0e12;color:#f4f0e8;border-radius:12px;
                        border:1px solid rgba(212,168,83,0.2)'>

                <h2 style='color:#d4a853;margin-top:0'>
                    🔑 Kaya Pa? — Password Reset
                </h2>

                <p style='color:#b0aead;line-height:1.7'>
                    Hi <strong style='color:#f4f0e8'>" . htmlspecialchars($user['firstname']) . "</strong>,<br>
                    We received a request to reset your password.
                    Click the button below to set a new one.
                    This link will expire in <strong style='color:#d4a853'>1 hour</strong>.
                </p>

                <a href='$resetLink'
                   style='display:inline-block;margin:20px 0;padding:13px 28px;
                          background:linear-gradient(135deg,#d4a853,#f0c97a);
                          color:#0e0e12;text-decoration:none;border-radius:9px;
                          font-weight:700;font-size:15px;'>
                    Reset My Password
                </a>

                <p style='color:#7b7b8e;font-size:12px;margin-top:24px;
                           border-top:1px solid rgba(212,168,83,0.15);padding-top:16px'>
                    If you didn't request this, you can safely ignore this email.<br>
                    Your password will not change unless you click the link above.
                </p>

                <p style='color:#7b7b8e;font-size:11px;margin-top:8px'>
                    Or copy this link into your browser:<br>
                    <span style='color:#d4a853;word-break:break-all'>$resetLink</span>
                </p>
            </div>
        ";

        // Plain text fallback for email clients that don't render HTML
        $mail->AltBody = "Kaya Pa? Password Reset\n\nHi " . $user['firstname'] . ",\n\nClick this link to reset your password (expires in 1 hour):\n\n$resetLink\n\nIf you didn't request this, ignore this email.";

        $mail->send();
        echo 'success';

    } catch (Exception $e) {
        // Log the real error server-side, show generic message to user
        error_log('PHPMailer Error: ' . $mail->ErrorInfo);
        echo 'Failed to send email. Please try again later.';
    }

    exit();
}

/* ════════════════════════════════════════════════════════════
   SECTION 9: RESET PASSWORD
   — Validates token using PHP time (no MySQL timezone issue),
     hashes new password, clears the token from DB.
   ════════════════════════════════════════════════════════════ */
if (isset($_POST['reset_password'])) {

    $token   = trim($_POST['token']            ?? '');
    $pw      = $_POST['password']              ?? '';
    $confirm = $_POST['confirm_password']      ?? '';

    if (empty($token)) {
        echo 'Invalid reset link.';
        exit();
    }

    if (empty($pw) || empty($confirm)) {
        echo 'Please fill in all fields.';
        exit();
    }

    if ($pw !== $confirm) {
        echo 'Passwords do not match.';
        exit();
    }

    if (strlen($pw) < 6) {
        echo 'Password must be at least 6 characters.';
        exit();
    }

    // Use PHP time for comparison — avoids MySQL NOW() timezone mismatch
    $currentTime = date('Y-m-d H:i:s');

    $stmt = $db->prepare("
        SELECT * FROM clients
        WHERE reset_token = ?
        AND reset_expire > ?
    ");
    $stmt->execute([$token, $currentTime]);
    $user = $stmt->fetch();

    if (!$user) {
        echo 'Invalid or expired reset link. Please request a new one.';
        exit();
    }

    // Hash the new password
    $hashed = password_hash($pw, PASSWORD_DEFAULT);

    // Update password and clear the token so it can't be reused
    $update = $db->prepare("
        UPDATE clients
        SET password     = ?,
            reset_token  = NULL,
            reset_expire = NULL
        WHERE id = ?
    ");
    $update->execute([$hashed, $user['id']]);

    echo 'success';
    exit();
}