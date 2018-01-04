<!DOCTYPE html>
<head>
	<title> Edit Article </title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="edit_article.css">
</head>

<body>

<div id="banner"></div>
<h1>News Website</h1>

<div id="logout_button">
	<a href="logout.php">Log Out</a>
</div>

<?php
session_start();
include 'database.php';

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$command = 'select articles.content, articles.title, articles.article_id, articles.owner_id, articles.article_link from articles where article_id=?;';

$article_id=$_POST['article_id'];

//back button to return to page with article
echo '<div id="back">';
	$location = "read_article.php?id=".$article_id;
	echo "<a href='".$location."'>Back to Article</a>";
echo '</div>';

$stmt = $mysqli->prepare($command);
if(!$stmt){
    printf("Query Prep Failed: Failed", $mysqli->error);
    exit;
}

$stmt->bind_param('s', $article_id);
$stmt->execute();
$stmt->bind_result($article, $title, $article_id, $owner_id, $article_link);
$stmt->fetch();
?>

<body>
<p id="articlepost">Edit Your Article</p>
<!-- edit article textbox -->
<div id="articleform">
	<form action="edit_article.php" method="POST" id="articles">
		<label>Title: <input type="text" name="title" value="<?php echo htmlentities($title); ?>"></label>
		<br>
		<br>
		<label id="header">Edit your article below: </label><br>
		<br>
		<textarea name="article" id="edit_text"><?php echo htmlentities($article); ?></textarea>
		<br>
		<br>
		<label>Article Link: <input type="text" name="article_link" value=<?php echo htmlentities($article_link); ?>></label>
		<br>
		<br>
		<?php 
		printf("<input type= \"hidden\" name= \"article_id\" value= %s >", $article_id);
		printf("<input type= \"hidden\" name= \"token\" value= %s >", $_SESSION['token']); 
		?>
		<input type="submit" value="Submit" name='submit_button'/>
	</form>
</div>

<?php
require 'database.php';

if(isset($_POST['submit_button'])){
   	$error = array();
	$valid_entry=true;
	
	//if there are errors in input
	if(empty($_POST['title'])){
		echo 'invalid title';
		$error[] = '<li>You did not enter a title.</li><br>';
		$valid_entry=false;
	}
	if(empty($_POST['article'])){
		$error[] = '<li>You did not enter any content.</li>';
	        $valid_entry=false;
	}
	if(!$valid_entry){
		echo("<h5>You have the following error(s) in your input:<br>");
		echo"<ul>";
		foreach($error as $error){
			echo $error;
		}
		echo "</ul>";
	}
	else{
		if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
		}
		$owner_id=$_SESSION['user_id'];
		$title = $mysqli->real_escape_string($_POST['title']);
		$article = $mysqli->real_escape_string($_POST['article']);
		$article_link= $mysqli->real_escape_string($_POST['article_link']);
		$command="update articles set content='" . $article . "', title='" . $title . "', article_link='" . $article_link . "' where article_id = " . $article_id;
		echo $command;
		$stmt = $mysqli->prepare($command);
		if(!$stmt) {
			printf("Query Prep Failed: :(, $mysqli->error");
			exit;
		}
		
		$stmt->execute();
		$stmt->close();
				
		header("Location: read_article.php?id=".$article_id);
	}
}

?>
</form>
</body>
</html>
