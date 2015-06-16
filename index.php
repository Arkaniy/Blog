<?php
	session_start();
	if(!isset($_SESSION['id'])) {
		header('Location: login.php');
		die();
	}
	$id = $_SESSION['id'];
	if(isset($_GET['id'])) {
		$id = $_GET['id'];
	}
	mysql_connect('localhost','root','') or die(mysql_error());
	mysql_select_db('mydb') or die(mysql_error());
	$result = mysql_query("SELECT * FROM users WHERE id = '$id'");
	if (!($user = mysql_fetch_array($result))) {
		header('Location: error.php');
	}

	if(isset($_POST['new-article'])){
		$uploadfile = $user['login'].'/'.$_FILES['picture']['name'];
		move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile);
		$pic_name = $_FILES['picture']['name'];
		mysql_query("INSERT INTO articles 
			VALUES (null, '$_POST[title]', '$_POST[body]', '$_SESSION[id]', '$pic_name', NOW())");
	}

	if(isset($_POST['sub'])) {
		if($_POST['sub'] == 1) {
			mysql_query("INSERT INTO subs 
				VALUES (null, '$_SESSION[id]', '$id')");
		}
		else {
			mysql_query("DELETE FROM subs WHERE person = '$_SESSION[id]' AND sub = '$id'");
		}
	}

	if(isset($_POST['new-photo']) && isset($_FILES['photo'])) {
		$uploadfile = $user['login'].'/0.jpg';
		move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile);
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/main.css">
	<script type="text/javascript" src="js/jquery-2.1.1.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
	<title>My blog</title>
</head>
<body>
	<div class="wrapper">
		<div class="header">
			<div class="text-header"><h1>Welcome to our blog</h1></div>
			<div class="controls-header">
				<?php
					if($_SESSION['id'] != $id) {
						print('<a href="index.php"><div class="button">Home</div></a>');
						$result = mysql_query("SELECT * FROM subs WHERE person = '$_SESSION[id]'");
						while($subs = mysql_fetch_array($result)) {
							if($subs['sub'] == $id) {
								$alreadysub = true;
								break;
							}
						}
						if(!$alreadysub) {
							print('<a href="#"><div class="button" id="subscribe">Subscribe</div></a>');
						}
						else {
							print('<a href="#"><div class="button" id="unsubscribe">Unsubscribe</div></a>');
						}
					}
					print('<a href="login.php"><div class="button">Log out</div></a>');
				?>
				
			</div>
			<div class="clear"></div>
		</div>

		<div class="content">
			<div class="left">
				<?php
					// main photo, name //
					printf('<center>
							<img class="main-photo" src="%s">
							</center>
							<p class="name">%s</p>',
							$user['login'].'/0.jpg' ,$user['name']);
					if(!isset($_GET['id']) || $_GET['id'] == $_SESSION['id']) {
						print('<form class="new-post" method="post" action="" enctype="multipart/form-data">
										<input type="file" name="photo">
										<input type="submit" name="new-photo" class="button" value="Change photo">
					 				</form>');
					}
					print('<hr>');
					// subscribers //
					$result = mysql_query("SELECT * FROM subs WHERE person = '$id'");
					if (isset($result)) {
						$query = "SELECT * FROM users WHERE id = ";
						while($subs = mysql_fetch_array($result)) {
							$query .= $subs['sub'] . ' OR id = ';
						}
						$query .= '-1';
						$result = mysql_query($query);
						while($subs = mysql_fetch_array($result)) {
							printf('<a href="%s"><div class="sub">
								<center><img class="subs-photo" src="%s">
								<p class="subs-name">%s</p></center></div></a>',
								'index.php?id='.$subs['id'], $subs['login'].'/0.jpg', $subs['name']);
						}
					}
				?>
				<div class="search">
					<input type="text" name="search-name" placeholder="search...">
					<button id="search">Search</button>
				</div>
				<div class="find">
					
				</div>
			</div>
			<div class="right">
				<?php
					if($_SESSION['id'] == $id) {
						printf('<input type="text" class="new-welcome">
								<p class="welcome">%s</p>', $user['welcome']);
					}
					else {
						printf('<p>%s</p>', $user['welcome']);	
					}
				?>
				<hr>
				<?php
					 if($_SESSION['id'] == $id) {
					 	print('
					 		<div class="slide-down">
					 			<p>New post</p>
					 		</div>

							<div class="slide">
					 			<center>
					 				<form class="new-post" method="post" action="" enctype="multipart/form-data">
										<input type="text" name="title" placeholder="Title">
										<textarea name="body"></textarea>
										<input type="file" name="picture">
										<input type="submit" name="new-article" class="button" value="Send">
					 				</form>
					 			</center>
					 		</div>
					 		<hr>
					 	');
					}
				?>
				<div class="articles">
					<?php
						$result = mysql_query("SELECT * FROM articles 
							WHERE author = '$user[id]' ORDER BY id DESC");
						$max_count = 3;
						$posts = mysql_num_rows($result);
						$pages = intval(($posts - 1) / $max_count) + 1;

						if(isset($_GET['page'])) {
							$page = (int)$_GET['page']-1;
							if($page < 0) {
								$page = 0;
							}
							else if($page > $pages - 1) {
								$page = $pages - 1;
							}
						}
						else {
							$page=0;
						}

						
						mysql_data_seek($result, $page*$max_count);
						while ($max_count-- > 0 && $articles = mysql_fetch_array($result)) {
							if(strlen($articles['body']) < 400) {
							 	$body = $articles['body'];
							}
							else {
							 	$body = substr($articles['body'],0,400).'...';
							}
							printf('<div class="article">
										<h4><a href="post.php?id=%d">%s</a></h4>
										<img src="%s">
										<p>%s</p>
										<p class="date">%s</p>
										<p class="link"><a href="post.php?id=%d">Read more</a></p>
									</div>
									<div class="clear"></div>',
							$articles['id'], $articles['title'], $user['login'].'/'.$articles['picture'], $body, date("j F, Y", strtotime($articles['date'])), $articles['id']);
						}
						echo '<div class="link">';
						if($pages>1) {
							for($i=$page-6; $i<$page+5; $i++) {
								if($i > 0 && $i<=$pages) {
									echo '<a class="button" href=index.php?page='.$i.'>'.$i.'</a>';
								}
							}
						}
						echo '</div>';
					?>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="footer">
			<p>Made by Oleksii</p>
		</div>
	</div>
</body>
</html>