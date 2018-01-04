<!DOCTYPE html>
<head>
	<title>Create Article</title>
	<meta charset="utf-8"/>
	<link rel="stylesheet" type="text/css" href="create_article.css">
</head>

<body>
<div id="banner"></div>
<h1>News Website</h2>

<div id="logoutuser">
	<a href="logout.php">Log Out</a>
</div>

<!-- article form -->
<p id="articlepost">Post an Article</p>
<!-- textbox -->
<div id="articleform">
	<form action="create_article.php" method="POST" id="articles">
		<label>Title: <input type="text" name="title" <?php
		if(isset($_POST['title'])){
			printf("value=%s", htmlentities($_POST['title']));
		}
		?> ></label>
		<br>
		<label>Post an article below: </label><br>
		<textarea name="article"><?php if(isset($_POST['article'])){echo htmlentities($_POST['article']);} ?></textarea>
		<br>
		<label>Article Link: <input type="text" name="article_link" 
		<?php
		if(isset($_POST['article_link'])){
			printf("value=%s", htmlentities($_POST['article_link']));
		}
		?></label>
		<br>
		<input type="submit" value="Submit" name='submit_button'/>
	</form>
</div>

<a href="homepage.php" id="back">Back to Homepage</a>

<?php
session_start();
require 'database.php';

if(isset($_POST['submit_button'])){
	$error = array();
	$valid_entry=true;
	echo  empty($_POST['title']);
	echo isset($_POST['article']);
	//no title entered
	if(empty($_POST['title'])){
		echo 'invalid title';
		$error[] = '<li>You did not enter a title.</li><br>';
		$valid_entry=false;
	}
	//no article content entered
	if(empty($_POST['article'])){
		$error[] = '<li>You did not enter any content.</li>';
		$valid_entry=false;
	}
	//entry not valid
	if(!$valid_entry){
		echo("<h5>You have the following error(s) in your input:<br>");
		echo"<ul>";
		foreach($error as $error){
			echo $error;	         
		}
		echo "</ul>";
	}
	else{
		//insert information into articles table
		$stmt = $mysqli->prepare("insert into articles (title, content, owner_id, article_link) values (?,?,?,?)");
		if(!$stmt) {
			printf("Query Prep Failed: :(, $mysqli->error");
			exit;
		}
		$owner_id=$_SESSION['user_id'];
		$title = $mysqli->real_escape_string($_POST['title']);
		$article = $mysqli->real_escape_string($_POST['article']);
		$article_link=$mysqli->real_escape_string($_POST['article_link']);

		$stmt->bind_param('ssis', $title, $article, $owner_id, $article_link);
		$stmt->execute();
		$stmt->close();
		header("Location: homepage.php");
	}
}
?>

</body>
</html>
