<?php
	session_start();
	mysql_connect('localhost','root','') or die(mysql_error());
	mysql_select_db('mydb') or die(mysql_error());

	if(isset($_POST['newwelcome'])) {
		mysql_query("UPDATE users SET welcome = '$_POST[newwelcome]' WHERE id = '$_SESSION[id]'");
	}

	if(isset($_POST['search'])) {
		$result = mysql_query("SELECT * FROM users WHERE name like '%$_POST[search]%'");
		while($search = mysql_fetch_array($result)) {
			printf('<a href="%s"><div class="sub">
					<center><img class="subs-photo" src="%s">
					<p class="subs-name">%s</p></center></div></a>',
					'index.php?id='.$search['id'], $search['login'].'/0.jpg' ,$search['name']);
		}
		return;
	}

	if(isset($_POST['delete'])) {
		mysql_query("DELETE FROM comments WHERE id = '$_POST[delete]'");
	}

	if(isset($_POST['newcomment'])) {
		mysql_query("INSERT INTO comments 
			VALUES (null, '$_POST[newcomment]', '$_SESSION[id]', '$_SESSION[currentid]', NOW())");
		return;
	}

	header('Location: index.php');
?>