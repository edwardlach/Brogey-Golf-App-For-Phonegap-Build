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


$username_input = $_POST['username_input'];


$find_email_query = mysqli_query( $link, "SELECT email_address "
					."FROM bg_app_users "
					."WHERE email_address = '$username_input' "
					."OR username = '$username_input' LIMIT 1" );

$find_email_row = mysqli_fetch_row( $find_email_query );

$email = $find_email_row[0];		

$email_info = array();


if( $email ){

	array_push( $email_info, array( 
					'text'		=> $email, 
					'is_email'	=> 1
				 )
	);
	
}else{

	array_push( $email_info, array( 
					'text'		=> $username_input, 
					'is_email'	=> 0
				 )
	);

}

echo json_encode($email_info);



?>