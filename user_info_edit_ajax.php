<?php
session_start();

include "simple_functions.php";
$link = connect_to_database();

$user_id = $_SESSION['id'];



if( $_POST['first'] ){

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$full_name = $first_name." ".$last_name;
	
	$run_settings_update = mysqli_query( $link,
		"UPDATE bg_app_users "
		."SET first_name = '$first_name', "
		."full_name = '$full_name' "
		."WHERE user_id = '$user_id'"
	);
						
	echo $first_name;
	
}

if( $_POST['last'] ){

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$full_name = $first_name." ".$last_name;
	
	$run_settings_update = mysqli_query( $link,
		"UPDATE bg_app_users "
		."SET last_name = '$last_name', "
		."full_name = '$full_name' "
		."WHERE user_id = '$user_id'"
	);
	
	echo $last_name;
	
}

if( $_POST['email_address'] ){

	$email_address = $_POST['email_address'];
	
	if (filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
	
		$previously_used_email_query = mysqli_query( $link,
			"SELECT * FROM bg_app_users "
			."WHERE email_address = '$email_address' "
			."AND user_id != '$user_id'"
		);
		
		$already_used = mysqli_num_rows( $previously_used_email_query );
		
		if( $already_used > 0 ){
			
			$email_error = $email_address." is already being used by another account";
			
			echo $email_error."...error";
		
		}else{
		
			$run_settings_update = mysqli_query( $link,
				"UPDATE bg_app_users "
				."SET email_address = '$email_address' "
				."WHERE user_id = '$user_id'"
			);
			
			echo $email_address."...success";
		
		}
	}else{
		
		echo $email_address." is not a valid email address...error";
	
	}
	
}

if( $_POST['username'] ){

	$username = $_POST['username'];
	
	$previously_used_username_query = mysqli_query( $link,
		"SELECT * FROM bg_app_users "
		."WHERE username = '$username' "
		."AND user_id != '$user_id'"
	);
	
	$already_used = mysqli_num_rows( $previously_used_username_query );
	
	if( $already_used > 0 ){
		
		$username_error = $username." is already being used by another account";
	
		echo $username_error."...error";
	
	}else{
	
		$run_settings_update = mysqli_query( $link,
			"UPDATE bg_app_users "
			."SET username = '$username' "
			."WHERE user_id = '$user_id'"
		);
		
		echo $username."...success";
	
	}
	
}







?>