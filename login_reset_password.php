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

$reset_id = $_GET['resetid'];

$id_query = mysqli_query( $link, "SELECT user_id "
				."FROM bg_app_reset_password "
				."WHERE reset_id = '$reset_id' "
				."AND reset_request_time >= NOW() - INTERVAL 1 HOUR ");

$id_row = mysqli_fetch_row( $id_query );

$id = $id_row[0];


$user_name_query = mysqli_query( $link, "SELECT full_name "
				 ."FROM bg_app_users "
				 ."WHERE user_id = '$id'" );
				 
$user_name_row = mysqli_fetch_row( $user_name_query );

$user_name = $user_name_row[0];
				 


?>
<!doctype html>

<html>

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

<body> 

<div data-role="page" id="reset_password">
	
	<div data-role="header" data-position="fixed">
		<div class="site_title">
			<div id="site_title">
				<span id="site_title"><?php echo $user_name; ?> reset password</span>
			</div>
		</div>
	</div><!-- /header -->



	<div data-role="content" class="login_page_content">
		
		
		<div class="messages">
		<?php 
			
			if( $user_name ){
				
				echo "<div class='info_message'>How&apos;s it going ".$user_name."!  You&apos;re almost there, type your new password into both fields, hit the Change Password button and you&apos;ll be ready to rejoin the action!</div>";
				
			}else{
			
				echo "<div class='info_message'>I'm sorry, it's been more than an hour since you requested a password reset.  No worries, head back to the login page, type in your username or email address and we'll send you another email with a fresh link to reset your password.</div>";
			
			}
			
		?>
		</div>
		<form id="login_form" method="post" enctype="multipart/form_data">
			<div class="form-group input_form">
				<label for="password" class="input_label">Password</label>
					<input type="password" name="password" id="password" />
			</div>
			<div class="form-group input_form">
				<label for="confirm_password" class="input_label">Confirm Password</label>
					<input type="password" name="confirm_password" id="confirm_password" />
			</div>
			<div>
				<input id=<?php echo '"'.$id.'"'; ?> class="btn btn-default login_submit" type="submit" value="Change Password" name="Submit" />
			</div>
		
		
		</form>
				
	</div><!-- /content -->
	
	
</div><!-- /page -->


<script type="text/javascript">

$('.login_submit').on('click', function(){
	
	var id = $(this).attr('id');
	var password = $('#password').val();
	var confirm_password = $('#confirm_password').val();
	
	if( password == confirm_password ){
		
		$.post("login_update_password.php",
		  {
		    password:password,
		    confirm_password:confirm_password,
		    id:id
		  },
		  function(data,status){
		 	
		  	$(location).attr('href', 'index.php');		
		    
		  });
		  
	}else{
		
		alert("The passwords did not match, please reenter");
	
		location.reload();
	
	}
	  
	event.preventDefault(); 
	

});






</script>



</body>

</html>