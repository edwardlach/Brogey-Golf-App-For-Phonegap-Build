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


$email_address = $_POST['email'];
$invitee_id = $_POST['invitee'];
$name = $_POST['name'];
$leagueid = $_POST['leagueid'];

if( !$leagueid == "" ){

	$url_link = "http://brogeygolf.com/user_registration.php?leagueid=".$leagueid;
	
	$league_name_query = mysqli_query( $link, "SELECT league_name FROM bg_app_league WHERE league_id = '$leagueid'" );
	$league_name_row = mysqli_fetch_row( $league_name_query );
	$league_name = $league_name_row[0];
	
	$faq_link = "http://brogeygolf.com/email_faq.php?leagueid=".$leagueid;

}else{

	$url_link = "http://brogeygolf.com/user_registration.php";
	
	$faq_link = "http://brogeygolf.com/email_faq.php";

}



$invitee_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users WHERE user_id = '$invitee_id'" );
$invitee_row = mysqli_fetch_row( $invitee_query );
$invitee = $invitee_row[0];


$already_member_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users "
						."WHERE email_address = '$email_address'" );
$member_name_row = mysqli_fetch_row($already_member_query);
$member_name = $member_name_row[0];
	

if( $member_name != "" ){

	echo $email_address." is already associated with a brogey account. If you are a league admin go ahead and add "
	.$member_name." to your league!";

}elseif (filter_var($email_address, FILTER_VALIDATE_EMAIL) && $league_name) {

	$to      = $email_address;
	$subject = 'Invitation to join Brogey Golf';
	
	$message = '
	<p>How&apos;s it going '.$name.'!</p>
	<p>You are receiving this message because '.$invitee.' is an amazing human being and thought you would like to join the '.$league_name.'  Brogey Golf League.  If you have questions about how the league works follow this <a href="'.$faq_link.'">link</a> and hopefully we can answer your inquiries.  Follow the link below to register with us at Brogey Golf and start ballin out in the new league.</p>
	<p><a href="'.$url_link.'"><b>Signup for Brogey Golf!</b></a></p>
	<p>That&apos;s all folks!</p>
	<p>With Love, </p>
	<p>Brogey Bosses Ed and Tyler</p>
	<p>and of course '.$invitee.'</p>';
	
	
	$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
	    'From: Brogey Golf <brogeybossed@brogeygolf.com>' . "\r\n" .
	    'Reply-To: brogeygolfreviews@gmail.com' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
	
	
	mail($to, $subject, $message, $headers);
	



}elseif(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {

	$to      = $email_address;
	$subject = 'Invitation to join Brogey Golf';
	
	$message = '
	<p>How&apos;s it going '.$name.'!</p>
	<p>You are receiving this message because '.$invitee.' is an amazing human being and thought you would like to join a Brogey Golf League.  If you have questions about how the league works follow this <a href="'.$faq_link.'">link</a> and hopefully we can answer your inquiries. Follow the link below to register with us at Brogey Golf.</p>
	<p><a href="'.$url_link.'"><b>Signup for Brogey Golf!</b></a></p>
	<p>That&apos;s all folks!</p>
	<p>With Love, </p>
	<p>Brogey Bosses Ed and Tyler</p>
	<p>and of course '.$invitee.'</p>';
	
	
	$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
	    'From: Brogey Golf <brogeybossed@brogeygolf.com>' . "\r\n" .
	    'Reply-To: brogeygolfreviews@gmail.com' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
	
	
	mail($to, $subject, $message, $headers);
	



}














?>