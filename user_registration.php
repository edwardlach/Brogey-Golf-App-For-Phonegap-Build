<?php
session_start();
date_default_timezone_set('America/New_York');

require "lib/password.php";

$invite_id = $_GET['leagueid'];

function record($registration_info, $invite_id) {
	
	
	
		
	
	
	$_SESSION['registration_email_error_message'] = "";
	$_SESSION['registration_password_error_message'] = "";
	$_SESSION['registration_username_error_message'] = "";
	
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
	
	$first_name 		= mysqli_real_escape_string( $link, $registration_info['first_name'] );
	$last_name 		= mysqli_real_escape_string( $link, $registration_info['last_name'] );
	$full_name		= $first_name." ".$last_name;
	$email			= mysqli_real_escape_string( $link, $registration_info['email'] );
	$username		= mysqli_real_escape_string( $link, $registration_info['username'] );
	$password		= mysqli_real_escape_string( $link, $registration_info['password'] );
	$confirm_password	= mysqli_real_escape_string( $link, $registration_info['confirm_password'] );
	
	
	$hash = password_hash($password, PASSWORD_DEFAULT);
	
	$previously_used_email_query = "SELECT * FROM `bg_app_users` WHERE `email_address` = '$email'";
	$previously_used_username_query = "SELECT * FROM `bg_app_users` WHERE `username` = '$username'";
	
	$query = "INSERT INTO `bg_app_users`(`first_name`, `last_name`, `full_name`, `email_address`, `username`, `password`) 
		  VALUES('$first_name', '$last_name', '$full_name', '$email', '$username', '$hash')";
	
	
	
	$previous_email_results = mysqli_query($link, $previously_used_email_query);
	$previous_username_results = mysqli_query($link, $previously_used_username_query);
 	
 	
 	$row = mysqli_fetch_row($previous_email_results);
 	$username_row = mysqli_fetch_row($previous_username_results);
 	
 	$previously_used_email = $row[3];
 	$previously_used_username = $username_row[4];
 	
 	
 	if( !$previously_used_email == "" || !$previously_used_username == "" ){
 		
 		if( !$previously_used_email == "" ){	
 		
 			$_SESSION['registration_email_error_message'] = "<h2>The email address ".$previously_used_email." is already associated with an account.</h2>";
 		
 		}
 		
 		if( !$previously_used_username == "" ){	
 		
 			$_SESSION['registration_username_error_message'] = "<h2>The username ".$previously_used_username." has already been used.</h2>";
 		
 		}
 		
 		header( 'Location: user_registration.php?leagueid='.$invite_id );
 	
 	}else{
 		
		if( $password == $confirm_password ){
			
			$run_registration_query = mysqli_query($link, $query);
			
	 		if( !$invite_id == "" ){
	 		
		 		header( 'Location: league_auto_invite.php?leagueid='.$invite_id.'&email='.$email );
		 	
		 	}else{
		 	
		 		header( 'Location: login.php' );
		 	
		 	}
	 	
	 	}else{
	 		
	 		$_SESSION['registration_password_error_message'] = "<h2>The password and confirmation password do not match!</h2>";
 		
 			header( 'Location: user_registration.php?leagueid='.$invite_id );
 		
 		}
 	
 	}
 	
 	
 	
			
	


}



if ( isset( $_POST['submit_registration'] ) ) {
	
	$registration_info = array(
		
		'first_name'		=> $_POST['first_name'],
		'last_name'		=> $_POST['last_name'],
		'email'			=> $_POST['email'],
		'username'		=> $_POST['username'],
		'password'		=> $_POST['password'],
		'confirm_password'	=> $_POST['confirm_password']
	
	);
		
	record($registration_info, $invite_id);


}





?>


<!doctype html>

<html>

<head>

	<title>User Registration</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript">
	
		$(document).bind("mobileinit", function () {
		    $.mobile.ajaxEnabled = false;
		});
	
	</script>			
	<script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-61063633-1', 'auto');
	  ga('send', 'pageview');
	
	</script>
	
	

</head>


<body> 

<div data-role="page" id="user_registration" >
	
	
	<div data-role="header" data-position="fixed">	
		<div class="site_title">
			<a id="home_page_link" href="brogeygolf.com" data-ajax="false">	
				<div id="site_title">
					<span id="site_title">Brogey Golf Registration</span>
				</div>
			</a>
		</div>
	</div><!-- /header -->
	


	<div data-role="content">
		
		<div id="messages">
		
			<?php
			
				if( !$_SESSION['registration_password_error_message'] == "" ){
				
					echo $_SESSION['registration_password_error_message'];
				
				}
				
				if( !$_SESSION['registration_email_error_message'] == "" ){
				
					echo $_SESSION['registration_email_error_message'];
					
				}
				
				if( !$_SESSION['registration_username_error_message'] == "" ){
					
					echo $_SESSION['registration_username_error_message'];
				
				}
				
				
			?>	
		
		</div><!-- /messages-->
				
		<form action="user_registration.php?leagueid=<?php echo $invite_id; ?>" method="post"> 
			<div class="form-group">
				<label for="registration_first_name">First Name:</label>
	   				<input type="text" name="first_name" id="registration_first_name" required />
	   		</div>
	   		<div class="form-group">
	   			<label for="registration_last_name">Last Name:</label>
	   				<input type="text" name="last_name" id="registration_last_name" required />
	   		</div>
	   		<div class="form-group">
	   			<label for="registration_email">Email:</label>
	   				<input type="email" name="email" id="registration_email" required />
	   		</div>
	   		<div class="form-group">	
	   			<label for="registration_user_name">User Name:</label>
	   				<input type="text" name="username" id="registration_user_name" required />
	   		</div>
	   		<div class="form-group">
	   			<label for="registration_password">Password:</label>
	   				<input type="password" name="password" id="registration_password" required />
	   		</div>
	   		<div class="form-group">
				<label for="confirm_password_input">Confirm Password: </label>
					<input type="password" name="confirm_password" id="confirm_password_input" required />
			</div>
	   			</br>
   			<div>
   				<input class="btn btn-default" type="submit" name="submit_registration" value="Sign up">				
   			</div>
		</form>			
				
				
	</div><!-- /content -->
	


</div><!-- /page -->

<script type="text/javascript">
	
   

</script>



</body>

</html>