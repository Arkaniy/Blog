<?php
	session_start();

	if(!isset($_SESSION['id'])) {
		header('Location: login.php');
	}
	$_SESSION['currentid'] = $_GET['id'];
	mysql_connect('localhost','root','') or die(mysql_error());
	mysql_select_db('mydb') or die(mysql_error());
	$result = mysql_query("SELECT * FROM articles WHERE id = '$_GET[id]'") or die(mysql_error());
	if (!($article = mysql_fetch_array($result))) {
		header('Location: error.php');
	}

	if(isset($_POST['delete-post'])) {
		mysql_query("DELETE FROM articles WHERE id = '$_GET[id]'");
		$result = mysql_query("DELETE FROM comments WHERE toward = '$_GET[id]'");
		header('Location: index.php');
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/post.css">
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
					printf('<a href="index.php?id=%d"><div class="button">Back</div></a>',
						$article['author']);
				?>
				<a href="index.php"><div class="button">Home</div></a>
				<a href="login.php"><div class="button">Log out</div></a>
			</div>
			<div class="clear"></div>
		</div>

		<div class="content">
			<div class="left">
				<?php
					$result = mysql_query("SELECT * FROM users WHERE id = '$article[author]'");
					$user = mysql_fetch_array($result);
					if($article['picture'] != '') {
						printf('<img class="picture" src="%s">', $user['login'].'/'.$article['picture']);
					}
					printf('<h2>%s</h2>
							<p>%s</p>
							<div class="clear"></div>
							<p class="name">%s</p>
							<p class="date-article name">%s</p>',
						$article['title'], $article['body'], $user['name'], date("j F, Y", strtotime($article['date'])));
					if($article['author'] == $_SESSION['id']) {
						print('<div class="controls">
								<form method="post">
									<button name="delete-post" class="button">Delete post</button>
								</form>
								</div>
						');
					}
				?>
			</div>
			<div class="right">
				<center>
					<textarea></textarea>
					<button class="button new-comment">Send</button>
				</center>	
				<?php
					$result = mysql_query("SELECT * FROM comments 
						WHERE toward = '$_GET[id]' ORDER BY id DESC") or die(mysql_error());
					$query = "SELECT * FROM users WHERE id = ";
					while($comments = mysql_fetch_array($result)) {
						$query .= $comments['from'] . ' OR id = ';
					}
					$res = mysql_query($query .= '-1');

					while($row = mysql_fetch_array($res)) {
						$from[$row['id']] = $row['name'];
					}

					$max_count = 5;
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
						while ($max_count-- > 0 && $comments = mysql_fetch_array($result)) {
						printf('
							<div class=comment id="%d">
								<p>%s</p>
							', $comments['id'], $comments['body']);
						if($_SESSION['id'] == $article['author'] || $_SESSION['id'] == $comments['from']) {
							print('<p class="delete-comment">Delete</p>');
						}
						printf('
								<p class="date">%s</p>
								<p class="from"><a href="index.php?id=%d">%s</a></p>
								<div class="clear"></div>
							</div>
						', date("j F, Y, G:i:s", strtotime($comments['date'])), $comments['from'], $from[$comments['from']]);
					}
					echo '<div class="link">';
						if($pages>1) {
							for($i=$page-6; $i<$page+5; $i++) {
								if($i > 0 && $i<=$pages) {
									echo '<a class="button" href=post.php?id='.$_GET['id'].'&page='.$i.'>'.$i.'</a>';
								}
							}
						}
						echo '</div>';
				?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="footer">
			<p>Made by Oleksii</p>
		</div>
	</div>
</body>
</html>