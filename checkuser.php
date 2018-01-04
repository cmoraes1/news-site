<?php
require 'database.php';
session_start();

$stmt = $mysqli->prepare("select count(*), user_id, password_hash from users where email_address=?");

if(!$stmt) {
	printf("Query Prep Failed: :(", $mysqli->error);
	exit;
}

$email_address = $mysqli->real_escape_string($_POST['email_address']);
$stmt->bind_param('s', $email_address);
$stmt->execute();
$stmt->bind_result($cnt, $user_id, $password_hash);
$stmt->fetch();

$pwd_guess = $_POST['pswd'];

//if password if correct then login successful
if ($cnt == 1 && password_verify($pwd_guess, $password_hash)) {
	$_SESSION['user_id'] = $user_id;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
	header("Location: loginsuccess.php");
	echo "Login successful";
}
else
{
	echo "Login failed";
	//back button to return to homepage
	echo '<div id="back">';
		echo '<a href="homepage.php">Back</a>';
	echo '</div>';
}

?>
