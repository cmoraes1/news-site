<!DOCTYPE html>
<html>

<head>
	<meta charset = "utf-8">
	<title> Login Page </title>
	<link rel="stylesheet" type="text/css" href="login.css">
</head>

<body>

<h1>Register Here:</h1>
<!-- register user -->
<div id="newuser">
	<form action="createuser.php" method="POST">
		<label for="first_name">First Name: <input type="text" name="first_name"></label><br>
		<label for="last_name">Last Name: <input type="text" name="last_name"></label><br>
		<label for="password">Password: <input type="password" name="pswd"></label><br>
		<label for="password_verify">Verify Password: <input type="password" name="pswd_v"></label><br>
		<label for="email">Email: <input type="text" name="email"></label><br>
		<input type="submit" name="submit" value="Submit">
	</form>
</div>

<?php
require 'database.php';

//back button to return to homepage
echo '<div id="backpage">';
	echo '<a href="homepage.php">Back to Homepage</a>';
echo '</div>';

//if entries are valid
if(isset($_POST['submit'])) {
	$error_list=array();
	$valid_entry=true;
	//if user doesn't enter a first name
	if(empty($_POST['first_name'])){
		$valid_entry=false;
		$error_list[]='<li> You did not enter your first name</li>';
	}
	//first name entry not valid
	else if(!ctype_alpha($_POST['first_name'])){
		$valid_entry=false;
		$error_list[]='<li> Your first name can only contain letters</li>';
	}
	//last name not entered
	if(empty($_POST['last_name'])){
		$valid_entry=false;
		$error_list[]='<li>You did not enter your last name </li>';
	}
	//last name entry not valid
	else if(!ctype_alpha($_POST['last_name'])){
		$valid_entry=false;
		$error_list[]='<li> Your last name can only contain letters</li>';
	}
	//password field empty
	if(empty($_POST['pswd']) or empty($_POST['pswd_v'])){
		$valid_entry=false;
		$error_list[]='<li> Please enter your password</li>';
	}
	//passwords don't match
	else if(strcmp($_POST['pswd'], $_POST['pswd_v']) != 0 ){
		$valid_entry=false;
		$error_list[]='<li> Your passwords do not match</li>';
	}
	//email field empty
	if(empty($_POST['email'])){
		$valid_entry=false;
		$error_list[]='<li> Please Enter your email</li>';
	}
	else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$valid_entry=false;
		$error_list[]='<li> Please enter a valid email</li>';
	}
	else {
		$stmt = $mysqli->prepare("select user_id, email_address from users where email_address='" . mysql_real_escape_string($_POST['email']) . "'");
		if(!$stmt) {
			printf("Query Prep Failed: %s\n", $mysqli->error);
			echo'check email';
			exit;
		}

		$stmt->execute();
		$result = $stmt->get_result();
		if(mysqli_num_rows($result) != 0) {
			$valid=false;
			$error_list[]="<li>There is already an account associated with this email<br>Use another email or <a href='homepage.php'>login</a> on the home page. </li>";
		}
	}
	//if entries are valid
	if($valid_entry){
		$first_name = $mysqli->real_escape_string($_POST['first_name']);
		$last_name = $mysqli->real_escape_string($_POST['last_name']);
		$email = $mysqli->real_escape_string($_POST['email']);
		$password = $_POST['pswd'];	
      		$password_hash = password_hash($password, PASSWORD_BCRYPT);	

		$stmt = $mysqli->prepare("insert into users (first_name, last_name, email_address, password_hash) values (?, ?, ?, ?);");
		if(!$stmt) {
		        printf("Query Prep Failed: :C", $mysqli-> error);
		        exit;
		}

		$stmt->bind_param('ssss', $first_name, $last_name, $email, $password_hash);
		$stmt->execute();
		$stmt->close();
		session_start();

		$command="select user_id from users order by user_id desc limit 1";
		$stmt = $mysqli->prepare($command);
		if(!$stmt) {
		        printf("Query Prep Failed: :C", $mysqli-> error);
		        exit;
		}

		$stmt->execute();
		$stmt->bind_result($user_id);
		$stmt->fetch();
		$_SESSION['user_id']=$user_id;
		$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
		$stmt->close();

		header("Location:homepage.php");
	}
	else {
		echo "You have the following error(s) in your input:<br>";
		echo "<ul>";
		foreach($error_list as $error){
			echo $error;
		}
		echo "</ul>";
	}
}
?>

<div id="rectangle"></div>

</body>
</html>
