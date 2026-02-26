<?php
// lightweight endpoint returning <option> elements for streams belonging to a class
include("constants/constant.php");

if(isset($_GET['class_id'])){
    $class_id = (int) $_GET['class_id'];
    $query = "SELECT stream_id, stream_name FROM streams WHERE class_id = $class_id ORDER BY stream_name";
    $res = mysqli_query($conn, $query);
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            echo '<option value="' . $row['stream_id'] . '">' . htmlspecialchars($row['stream_name']) . '</option>';
        }
    }
}

// no trailing output; the script is used by fetch() from JS
