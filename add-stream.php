<?php
// include DB constants and connection
include_once __DIR__ . '/constants/constant.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: streams.php');
    exit;
}

$class_id = isset($_POST['class_id']) ? trim($_POST['class_id']) : '';
$stream_name = isset($_POST['stream_name']) ? trim($_POST['stream_name']) : '';

// basic validation
if ($class_id === '' || $stream_name === '') {
    $_SESSION['flash_error'] = 'Please provide both class and stream name.';
    header('Location: streams.php');
    exit;
}

// normalize stream name
$stream_name_clean = strtoupper($stream_name);

// prevent duplicate: check if a stream with same name exists for that class
$checkSql = "SELECT stream_id FROM streams WHERE class_id = ? AND UPPER(stream_name) = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $checkSql)) {
    mysqli_stmt_bind_param($stmt, 'is', $class_id, $stream_name_clean);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['flash_error'] = 'A stream with that name already exists for the selected class.';
        mysqli_stmt_close($stmt);
        header('Location: streams.php');
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['flash_error'] = 'Database error (validation).';
    header('Location: streams.php');
    exit;
}

// insert
$insertSql = "INSERT INTO streams (class_id, stream_name) VALUES (?, ?)";
if ($stmt = mysqli_prepare($conn, $insertSql)) {
    mysqli_stmt_bind_param($stmt, 'is', $class_id, $stream_name_clean);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_success'] = 'Stream saved successfully.';
    } else {
        $_SESSION['flash_error'] = 'Failed to save stream. Please try again.';
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['flash_error'] = 'Database error (insert).';
}

header('Location: streams.php');
exit;
?>