<?php
// server-side handler for quick registration
// this script expects POST data from quick_reg.php

include_once __DIR__ . '/constants/constant.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// reject non-POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quick_reg.php');
    exit;
}

// simple CSRF protection
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['flash_error'] = 'Invalid form submission.';
    header('Location: quick_reg.php');
    exit;
}

// helper
function clean($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}

$student_name       = clean($conn, $_POST['student_name']      ?? '');
$lin_number         = strtoupper(clean($conn, $_POST['lin_number'] ?? ''));
$gender             = clean($conn, $_POST['gender']            ?? '');
$nationality        = clean($conn, $_POST['nationality']       ?? '');
$class_id           = (int) ($_POST['class']                   ?? 0);
$stream_id          = (int) ($_POST['stream']                  ?? 0);
$term               = clean($conn, $_POST['term']              ?? '');
$year_of_study      = clean($conn, $_POST['year_of_study']     ?? '');
$residential_status = strtoupper(clean($conn, $_POST['residential_status'] ?? ''));
$raw_entry_status   = clean($conn, $_POST['entry_status']      ?? '');

$entry_status_map = [
    'New Student' => 'NEW',
    'Continuing'  => 'CONTINUING',
];
$entry_status = $entry_status_map[$raw_entry_status] ?? strtoupper($raw_entry_status);

$errors = [];
if (empty($student_name))   $errors[] = "Student name is required.";
if (empty($lin_number))     $errors[] = "LIN number is required.";
if (empty($gender))         $errors[] = "Gender is required.";
if ($class_id === 0)        $errors[] = "Class is required.";
if (empty($term))           $errors[] = "Term is required.";
if (empty($year_of_study))  $errors[] = "Year of study is required.";
if (empty($residential_status)) $errors[] = "Residential status is required.";
if (empty($entry_status))   $errors[] = "Entry status is required.";
if (empty($nationality))    $errors[] = "Nationality is required.";

// duplicate LIN check
if (!$errors) {
    $stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE lin = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $lin_number);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'A student with that LIN number already exists.';
        }
        mysqli_stmt_close($stmt);
    }
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode('<br>', $errors);
    header('Location: quick_reg.php');
    exit;
}

// transaction insert
mysqli_begin_transaction($conn);

try {
    $insert1 = "INSERT INTO students (student_name, lin, gender, nationality, entry_date)
                VALUES (?, ?, ?, ?, NOW())";
    $stmt1 = mysqli_prepare($conn, $insert1);
    if (!$stmt1) throw new Exception('Prepare failed: ' . mysqli_error($conn));
    mysqli_stmt_bind_param($stmt1, 'ssss', $student_name, $lin_number, $gender, $nationality);
    mysqli_stmt_execute($stmt1);
    $student_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt1);

    $stream_val = $stream_id > 0 ? $stream_id : null;
    $insert2 = "INSERT INTO student_additional_info
                (student_id, class_id, stream_id, term, year_of_study, entry_status, residence_status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = mysqli_prepare($conn, $insert2);
    if (!$stmt2) throw new Exception('Prepare failed: ' . mysqli_error($conn));
    mysqli_stmt_bind_param($stmt2, 'iiissss', $student_id, $class_id, $stream_val, $term, $year_of_study, $entry_status, $residential_status);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    mysqli_commit($conn);
    $_SESSION['flash_success'] = 'Student registered successfully (ID: ' . $student_id . ').';
    header('Location: quick_reg.php');
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['flash_error'] = 'Database error: ' . htmlspecialchars($e->getMessage());
    header('Location: quick_reg.php');
    exit;
}
?>