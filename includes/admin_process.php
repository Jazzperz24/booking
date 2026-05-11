<?php
require '../config/config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo 'unauthorized'; exit();
}

$action = $_POST['action'] ?? '';

/* ── Add Coach ── */
if ($action === 'add_coach') {
    $name      = trim($_POST['name']      ?? '');
    $category  = trim($_POST['category']  ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $rate      = (float)($_POST['rate']   ?? 0);
    $bio       = trim($_POST['bio']       ?? '');
    $photo     = '';

    $allowed_cats = ['Dance','Fitness','Sports','Wellness/Yoga','Belle'];
    if (empty($name) || !in_array($category, $allowed_cats)) {
        echo 'Please fill in all required fields.'; exit();
    }

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = '../assets/images/coaches/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) { echo 'Invalid image format.'; exit(); }
        $filename = 'coach_' . time() . '_' . rand(100,999) . '.' . $ext;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
            echo 'Photo upload failed.'; exit();
        }
        $photo = $filename;
    }

    try {
        $stmt = $db->prepare("INSERT INTO coaches (name, category, specialty, rate, bio, photo) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name, $category, $specialty, $rate, $bio, $photo]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to add coach.';
    }
    exit();
}

/* ── Edit Coach ── */
if ($action === 'edit_coach') {
    $id        = (int)($_POST['coach_id'] ?? 0);
    $name      = trim($_POST['name']      ?? '');
    $category  = trim($_POST['category']  ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $rate      = (float)($_POST['rate']   ?? 0);
    $bio       = trim($_POST['bio']       ?? '');

    if (!$id || empty($name)) { echo 'Invalid data.'; exit(); }

    // Get current photo
    $current = $db->prepare("SELECT photo FROM coaches WHERE id = ?");
    $current->execute([$id]);
    $currentPhoto = $current->fetchColumn();
    $photo = $currentPhoto;

    // Handle new photo upload
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = '../assets/images/coaches/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext     = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) { echo 'Invalid image format.'; exit(); }
        $filename = 'coach_' . time() . '_' . rand(100,999) . '.' . $ext;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
            // Delete old photo if exists
            if ($currentPhoto && file_exists($uploadDir . $currentPhoto)) {
                unlink($uploadDir . $currentPhoto);
            }
            $photo = $filename;
        }
    }

    try {
        $stmt = $db->prepare("UPDATE coaches SET name=?, category=?, specialty=?, rate=?, bio=?, photo=? WHERE id=?");
        $stmt->execute([$name, $category, $specialty, $rate, $bio, $photo, $id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to update coach.';
    }
    exit();
}

/* ── Delete Coach ── */
if ($action === 'delete_coach') {
    $id = (int)$_POST['id'];
    try {
        // Delete photo file if exists
        $coach = $db->prepare("SELECT photo FROM coaches WHERE id = ?");
        $coach->execute([$id]);
        $photo = $coach->fetchColumn();
        if ($photo && file_exists('../assets/images/coaches/' . $photo)) {
            unlink('../assets/images/coaches/' . $photo);
        }
        $db->prepare("DELETE FROM coaches WHERE id = ?")->execute([$id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to delete coach.';
    }
    exit();
}

/* ── Edit Client ── */
if ($action === 'edit_client') {
    $id        = (int)($_POST['client_id'] ?? 0);
    $firstname = trim($_POST['firstname']  ?? '');
    $lastname  = trim($_POST['lastname']   ?? '');
    $email     = trim($_POST['email']      ?? '');
    $phone     = trim($_POST['phone']      ?? '');

    if (!$id || empty($firstname) || empty($lastname) || empty($email)) {
        echo 'Please fill in all required fields.'; exit();
    }

    // Check email not taken by another client
    $check = $db->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
    $check->execute([$email, $id]);
    if ($check->fetch()) { echo 'Email already used by another client.'; exit(); }

    try {
        $stmt = $db->prepare("UPDATE clients SET firstname=?, lastname=?, email=?, phonenumber=? WHERE id=?");
        $stmt->execute([$firstname, $lastname, $email, $phone, $id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to update client.';
    }
    exit();
}

/* ── Update Booking Status ── */
if ($action === 'update_booking') {
    $id     = (int)$_POST['id'];
    $status = $_POST['status'];
    if (!in_array($status, ['confirmed','cancelled'])) { echo 'Invalid status.'; exit(); }
    try {
        $db->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$status, $id]);
        echo 'success';
    } catch (PDOException $e) { echo 'Failed to update booking.'; }
    exit();
}

/* ── Delete Booking ── */
if ($action === 'delete_booking') {
    $id = (int)$_POST['id'];
    try {
        $db->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
        echo 'success';
    } catch (PDOException $e) { echo 'Failed to delete booking.'; }
    exit();
}

/* ── Delete Client ── */
if ($action === 'delete_client') {
    $id = (int)$_POST['id'];
    try {
        $db->prepare("DELETE FROM bookings WHERE client_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM clients WHERE id = ?")->execute([$id]);
        echo 'success';
    } catch (PDOException $e) { echo 'Failed to remove client.'; }
    exit();
}

echo 'Unknown action.';