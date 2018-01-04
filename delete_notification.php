<?php
require 'database.php';
session_start();

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$notification_id= $mysqli->real_escape_string($_POST['notification_id']);
$command= 'delete from notifications where notification_id='.$notification_id;
$stmt=$mysqli->prepare($command);
if(!$stmt) {
		printf("Query Prep Failed: :(, $mysqli->error");
		exit;
}
$stmt->execute();

header("Location: notifications.php");
?>
