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

$stream_id = isset($_POST['stream_id']) ? (int) $_POST['stream_id'] : 0;
$class_id = isset($_POST['class_id']) ? (int) $_POST['class_id'] : 0;
$stream_name = isset($_POST['stream_name']) ? trim($_POST['stream_name']) : '';

if ($stream_id <= 0 || $class_id <= 0 || $stream_name === '') {
    $_SESSION['flash_error'] = 'Please provide class and stream name.';
    header('Location: streams.php');
    exit;
}

// normalize stream name
$stream_name_clean = strtoupper($stream_name);

// check duplicate (exclude current stream)
$checkSql = "SELECT stream_id FROM streams WHERE class_id = ? AND UPPER(stream_name) = ? AND stream_id <> ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $checkSql)) {
    mysqli_stmt_bind_param($stmt, 'isi', $class_id, $stream_name_clean, $stream_id);
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

// perform update
$updateSql = "UPDATE streams SET class_id = ?, stream_name = ? WHERE stream_id = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $updateSql)) {
    mysqli_stmt_bind_param($stmt, 'isi', $class_id, $stream_name_clean, $stream_id);
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['flash_success'] = 'Stream updated successfully.';
        } else {
            $_SESSION['flash_error'] = 'No changes were made.';
        }
    } else {
        $_SESSION['flash_error'] = 'Failed to update stream. Please try again.';
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['flash_error'] = 'Database error (update).';
}

header('Location: streams.php');
exit;
?>
<?php
    include("partials/header.php");
    if(isset($_GET['id'])){
        $stream_id = $_GET['id'];
        
    }
    include("partials/footer.php");
?>

<div class="container w-1/2 m-5 p-5 border border-black rounded-lg">
    <h1 class="text-left uppercase">update streams</h1>
</div>