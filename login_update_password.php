<?php
session_start();
require "lib/password.php";


$host="Localhost"; // Host name 
$username="brogeygo"; // Mysql username 
$password="EdandTylershotpar$100"; // Mysql password 
$db_name="brogeygo_wor1"; // Database name 
$tbl_name="bg_app_users"; // Table name 

// Connect to server and select database.
$link = mysqli_connect($host, $username, $password, $db_name);


$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$id = $_POST['id'];


$hash = password_hash($password, PASSWORD_DEFAULT);

$update_password = mysqli_query( $link, "UPDATE bg_app_users "
				       ."SET password = '$hash' "
				       ."WHERE user_id = '$id'" );	

$_SESSION['id'] = $id;


?>