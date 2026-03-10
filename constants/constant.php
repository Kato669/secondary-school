<?php
session_start();

define('LOCALHOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'Root@123');
define('DB_NAME', 'sms');
define('SITEURL', 'http://localhost/secondary-school/');

$conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME)
        or die("Database connection failed.");

?>