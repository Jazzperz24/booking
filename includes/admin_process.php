<?php
// ============================================================
// FILE: admin_process.php
// PATH: includes/admin_process.php
// DESC: Handles all admin AJAX actions from admin.js
// NOTE: session_start() already called in config.php
// ============================================================
require '../config/config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo 'unauthorized'; exit();
}

$action = $_POST['action'] ?? '';

// ── Add coach ──
if ($action === 'add_coach') {
    $name      = trim($_POST['name'] ?? '');
    $category  = trim($_POST['category'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $rate      = (float)($_POST['rate'] ?? 0);
    $bio       = trim($_POST['bio'] ?? '');

    $allowed_cats = ['Dance','Fitness','Sports','Wellness/Yoga','Belle'];
    if (empty($name) || !in_array($category, $allowed_cats)) {
        echo 'Please fill in all required fields.'; exit();
    }

    try {
        $stmt = $db->prepare("INSERT INTO coaches (name, category, specialty, rate, bio) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $category, $specialty, $rate, $bio]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to add coach. Please try again.';
    }
    exit();
}

// ── Delete coach ──
if ($action === 'delete_coach') {
    $id = (int)$_POST['id'];
    try {
        $db->prepare("DELETE FROM coaches WHERE id = ?")->execute([$id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to delete coach.';
    }
    exit();
}

// ── Update booking status ──
if ($action === 'update_booking') {
    $id     = (int)$_POST['id'];
    $status = $_POST['status'];

    if (!in_array($status, ['confirmed', 'cancelled'])) {
        echo 'Invalid status.'; exit();
    }

    try {
        $db->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$status, $id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to update booking.';
    }
    exit();
}

// ── Delete booking ──
if ($action === 'delete_booking') {
    $id = (int)$_POST['id'];
    try {
        $db->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to delete booking.';
    }
    exit();
}

// ── Delete client (remove bookings first to avoid FK errors) ──
if ($action === 'delete_client') {
    $id = (int)$_POST['id'];
    try {
        $db->prepare("DELETE FROM bookings WHERE client_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM clients WHERE id = ?")->execute([$id]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'Failed to remove client.';
    }
    exit();
}

echo 'Unknown action.';