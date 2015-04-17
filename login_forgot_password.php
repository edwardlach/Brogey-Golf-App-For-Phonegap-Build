<?php
session_start();
date_default_timezone_set('America/New_York');


$host="Localhost"; // Host name 
$username="brogeygo"; // Mysql username 
$password="EdandTylershotpar$100"; // Mysql password 
$db_name="brogeygo_wor1"; // Database name 
$tbl_name="bg_app_users"; // Table name 

// Connect to server and select database.
$link = mysqli_connect($host, $username, $password, $db_name);

if (mysqli_connect_error()) {

 	 die("Could not connect to database");

}


$is_email = $_POST['is_email'];
$email_address = $_POST['text'];


$find_name_query = mysqli_query( $link, "SELECT full_name "
					."FROM bg_app_users "
					."WHERE email_address = '$email_address' "
					."LIMIT 1" );

$find_name_row = mysqli_fetch_row( $find_name_query );

$name = $find_name_row[0];

$find_id_query = mysqli_query( $link, "SELECT user_id "
					."FROM bg_app_users "
					."WHERE email_address = '$email_address' "
					."LIMIT 1" );

$find_id_row = mysqli_fetch_row( $find_id_query );

$id = $find_id_row[0];



$resetid = uniqid();

$insert_reset_info = mysqli_query( $link, "INSERT INTO bg_app_reset_password "
					 ."(user_id, reset_id, reset_request_time) "
					 ."VALUES ('$id', '$resetid', now())" );



$to      = $email_address;
$subject = 'Reset Brogey Golf Password';

if( $is_email == 1 ){

	$message = '
	<p>What&apos;s up '.$name.',</p>
	<p>You are receiving this message because you have requested to reset your password, also, you are incredibly good looking and we love talking to you.  Follow the link below to get down to business and get back to Brogey Golfing!  The link will expire after an hour.  If you were not able to finish resetting your password  within that time frame go back to the Brogey login page, put in your username or email and hit the forgot password button again, we will be happy to send you another link.</p>
	<p><a href="http://www.brogeygolf.com/login_reset_password.php?resetid='.$resetid.'"><b>Reset Password</b></a></p>
	<p>That&apos;s all folks!</p>
	<p>With Love, </p>
	<p>Brogey Bosses Ed and Tyler</p>';

}else{

	$message = '
	<p>What&apos;s up '.$name.',</p>
	<p>You are receiving this message because you or someone else has requested to reset a brogey golf password.  Unfortunately, this email address is not in our database.  If you are not a Brogey Golf member and are receiving this message in error, we greatly apologize.  If you are a Brogey Golf member and are trying to reset your password, please return to the login page and try a different username or password than the one entered.  We apologize for any inconvenience but we would like to point out that you are exceptionally good looking.</p> 
	<p>Have a wonderful day!</p>
	<p>With Love, </p>
	<p>Brogey Bosses Ed and Tyler</p>';

}	

$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
    'From: Brogey Golf <brogeybossed@brogeygolf.com>' . "\r\n" .
    'Reply-To: brogeygolfreviews@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


mail($to, $subject, $message, $headers);










?>