<?php
session_start();
date_default_timezone_set('America/New_York');
include 'simple_functions.php';
$link = connect_to_database();



$user_id = $_SESSION['id'];
$user_name_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users WHERE user_id = '$user_id'" );
$user_name_row = mysqli_fetch_row( $user_name_query );
$user_name = $user_name_row[0];





$to      = "questions@brogeygolf.com";
$from    = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];


$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
    'From: '.$user_name.' <'.$from.'>' . "\r\n" .
    'Reply-To: '.$from.'' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


mail($to, $subject, $message, $headers);



?>