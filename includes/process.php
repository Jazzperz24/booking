<?php
// ============================================================
// FILE: process.php
// PATH: includes/process.php
// NOTE: session_start() is already called inside config.php
//       DO NOT call it again here
// ============================================================
require '../config/config.php';


/* ============================================================
   SECTION 1: REGISTRATION
   - Validates fields
   - Inserts new client
   - Sets session (AUTO-LOGIN after registration)
   ============================================================ */
if (isset($_POST['create'])) {

    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = $_POST['password'];

    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $firstname)) {
        echo 'First name must start with capital letter.'; exit();
    }
    if (!preg_match('/^[A-Z][a-zA-Z]*$/', $lastname)) {
        echo 'Last name must start with capital letter.'; exit();
    }
    if (!preg_match('/^09[0-9]{9}$/', $phone)) {
        echo 'Phone number must start with 09 and be 11 digits.'; exit();
    }

    // Check if email is already taken
    $check = $db->prepare('SELECT id FROM clients WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        echo 'This email is already registered. Please log in instead.'; exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare('INSERT INTO clients (firstname, lastname, email, phonenumber, password) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$firstname, $lastname, $email, $phone, $hashed]);

        // AUTO-LOGIN: set session immediately after registration
        // so the user lands on homepage already logged in
        $_SESSION['client_id'] = $db->lastInsertId();
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname']  = $lastname;
        $_SESSION['email']     = $email;
        $_SESSION['is_admin']  = 0;

        echo 'success';

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo 'This email is already registered.';
        } else {
            echo 'Registration failed. Please try again.';
        }
    }
    exit();
}


/* ============================================================
   SECTION 2: LOGIN
   - Checks email + password
   - Sets session on success
   ============================================================ */
if (isset($_POST['login'])) {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo 'Please enter your email and password.'; exit();
    }

    try {
        $stmt = $db->prepare('SELECT * FROM clients WHERE email = ?');
        $stmt->execute([$email]);
        $client = $stmt->fetch();

        if ($client && password_verify($password, $client['password'])) {

            // Set session — this is what makes the navbar show
            // the user's name and the Coaches / Book Now links
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


/* ============================================================
   SECTION 3: LOGOUT
   - Destroys the session
   - Redirects back to homepage
   - Triggered by navbar logout link: loginpage.php?logout=1
   ============================================================ */
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: /REGISTRATIONSFORM/index.php');
    exit();
}