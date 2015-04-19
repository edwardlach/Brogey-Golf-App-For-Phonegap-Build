<?php

function connect_to_database(){

	$host="Localhost"; // Host name 
	$username="brogeygo"; // Mysql username 
	$password="EdandTylershotpar$100"; // Mysql password 
	$db_name="brogeygo_wor1"; // Database name 
	$tbl_name="bg_app_users"; // Table name 
	
	// Connect to server and select database.
	$link = mysqli_connect($host, $username, $password, $db_name);
	
	if (mysqli_connect_error()) {
	
	 	 die("Could not connect to database");
	
	}else{
	
		return $link;
	
	}

}


function initials($name_for_initials){

	$initials_array = explode(" ", $name_for_initials);

	$first_name = $initials_array[0];
	
	$last_name = $initials_array[1];
	
	$first_name_array = str_split($first_name);
	
	$last_name_array = str_split($last_name);

	$first_initial = $first_name_array[0];
	
	$last_initial = $last_name_array[0];
	
	$initials = $first_initial.$last_initial;
	
	return $initials;

}


function user_name($link){

	$user_name_query_user_id = $_SESSION['id'];

	$user_name_query = mysqli_query( $link, "SELECT full_name "
					."FROM bg_app_users "
					."WHERE user_id = '$user_name_query_user_id'");

	$user_name_row = mysqli_fetch_row( $user_name_query );
	
	$user_name = $user_name_row[0];
	
	return $user_name;

}



function check_array_for_dups($array_to_check_for_dups){

	$length = count($array_to_check_for_dups);
	$has_dup = 0;
	
	for( $i = 0; $i < $length; $i += 1 ){
	
		$item_1 = $array_to_check_for_dups[$i];
		
		for( $j = $i + 1; $j < $length; $j += 1 ){
			
			$item_2 = $array_to_check_for_dups[$j];
			
			if( $item_1 == $item_2 ){
				
				$has_dup = $item_1;
				break 2;
			
			}
		
		}	
	
	}

	return $has_dup;

}




?>