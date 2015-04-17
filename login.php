<?php
session_start();

require "lib/password.php";



function record($login_info){

	$_SESSION['login_error_message'] = "";

	$host="Localhost"; // Host name 
	$username="brogeygo"; // Mysql username 
	$password="EdandTylershotpar$100"; // Mysql password 
	$db_name="brogeygo_wor1"; // Database name 
	$tbl_name="bg_app_users"; // Table name 
	
	// Connect to server and select database.
	$link = mysqli_connect($host, $username, $password, $db_name);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$username = mysqli_real_escape_string($link,  $login_info['username']);
	$password = mysqli_real_escape_string($link, $login_info['password']);	
	
	$username_query = "SELECT `user_id`, `username`, `password`, `email_address` FROM `bg_app_users` WHERE `username` = '$username' OR `email_address` = '$username' LIMIT 1";
	
	$login_query = mysqli_query($link, $username_query);
	
	$row = mysqli_fetch_row($login_query);
	
	$ID 		= $row[0];
	$db_username 	= strtolower( $row[1] );
	$hash		= $row[2];
	$db_email	= strtolower( $row[3] );
	
	$username_low	= strtolower( $username );
	
	if( $username_low == $db_username && password_verify( $password, $hash ) ){
		$_SESSION['username']	= $db_username;
		$_SESSION['id']		= $ID;
		

		header( "Location: index.php" );
		
		
	}elseif( $username_low == $db_email && password_verify( $password, $hash ) ){
		$_SESSION['username']	= $db_username;
		$_SESSION['id']		= $ID;
		

		header( "Location: index.php" );
	
	}else{
	
		$_SESSION['login_error_message'] = "<div class='error_message'>Oops that username or password combination was incorrect. </br> Please try again.</div>";
		
	} 	



}



if( isset( $_POST['Submit'] ) ){
	
	$login_info = array(
		'username'	=> $_POST['username'],
		'password'	=> $_POST['password']
	);
	
	record($login_info);

}





?>
<!doctype html>

<html id="login">

<head>

	<title>Brogey Golf</title>
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

<body id="login"> 

<div data-role="page" id="login">
	
	



	<div data-role="content" class="login_page_content">
		
		
		<div class="messages">
		<?php
		
			if( !$_SESSION['login_error_message'] == "" ){
				
				echo $_SESSION['login_error_message'];
			}
		
		?>
		</div>
		
		<div id="login_actions">
			<!-- <div id="brogey_golf_logo">Brogey Golf</div> -->
			<img src="/images/neon_light_logo.png" id="login_logo" style="width:280px;height:39px">
			<form id="login_form" action="login.php" method="post" enctype="multipart/form_data">
				<div class="form-group input_form">
					<label for="username_input" class="input_label">Username/Email</label>
						<input type="text" name="username" id="username_input" />
				</div>
				<div class="form-group input_form">
					<label for="password_input" class="input_label">Password</label>
						<input type="password" name="password" id="password_input" />
				</div>
				<div>
					<input id="submit" class="btn btn-default login_submit" type="submit" value="Login" name="Submit" />
				</div>
			
			
			</form>
			</br>
			<a href="user_registration.php" class="signup">Sign Up</a>
			<a class="forgot_password">Forgot Password?</a>
		</div>		
	</div><!-- /content -->
	
	

</div><!-- /page -->


<script type="text/javascript">

$('#submit').on('click', function(){

	$(location).attr('href', 'index.php');

});




$('.forgot_password').on('click', function(){

	var username_input = $('#username_input').val();
	
	if( username_input == "" ){
	
		$('.messages').html('Please enter an email or username into the Username field to reset your password');	
		$('.messages').addClass("info_message");
	
	}else{
	
		$.post("login_find_email.php",
		  {
		    username_input:username_input
		  },
		  function(data,status){
			
			var jsonData = jQuery.parseJSON( data );
			
			var is_email = jsonData[0].is_email;
			var text = jsonData[0].text;
			
		  	$.post("login_forgot_password.php",
			  {
			    text:text,
			    is_email:is_email
			  },
			  function(data,status){
			  	$('.messages').html('An email to reset your password has been sent to ' + text);
			  	$('.messages').addClass("info_message");	
			    
			  });
		    
		  });

		
	
		
	}
	

});


</script>



</body>

</html>