<?php

$mysqli = new mysqli('localhost', 'camillamoraes', 'lr97lr', 'news_website');

if($mysqli->connect_errno) {
	printf("Connection Failed: $s\n", $mysqli->connect_error);
	exit;
}
?>
