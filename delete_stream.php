<?php
    // include constant file
    include("constants/constant.php");
    if(isset($_GET['id'])){
        $stream_id = $_GET['id'];
        $delete = "DELETE FROM streams WHERE $stream_id = stream_id";
        $sql = mysqli_query($conn, $delete);
        if($sql == true){
            header("Location: streams.php");
        }
    }
?>