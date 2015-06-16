<?php
	session_start();
	unset($_SESSION['id']);
	session_destroy();

	mysql_connect('localhost', 'root', '') or die(mysql_error());
	mysql_select_db("mydb") or die(mysql_error());

	/////////////////Log IN/////////////////////////
	if(isset($_POST['enter'])) {
		$login = $_POST['login'];
		$password = $_POST['password'];

		$query = "SELECT * FROM users WHERE login = '$login'";
		$result = mysql_query($query) or die(mysql_error());
		if(($user = mysql_fetch_array($result)) && $user['password'] == md5($password)) {
			session_start();
			$_SESSION['id'] = $user['id'];
			header('Location: index.php');
		}
		else {
			$_POST['wrong'] = true;
		}
	}
	////////////////////////////////////////////////

	/////////////////Registration///////////////////
	if(isset($_POST['register'])) {
		$login = $_POST['login'];
		$password = $_POST['password'];
		$name = $_POST['name'];
		$result = mysql_query("SELECT * FROM users WHERE login = '$login'") or die(mysql_error());
		if (mysql_fetch_array($result) == false) {
			if ($password == $_POST['password2']) {
				// if (preg_match("/[0-9]/",$password) && preg_match("/[a-z]/",$password) && 
				// 	preg_match("/[A-Z]/",$password) && strlen($password) > 8) {
					mysql_query("INSERT INTO  users VALUES (null, '$login', md5('$password'), '$name', 'What you feel....')");
					mkdir($login);
					$uploadfile = $login.'/0.jpg';
					move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile);
					$_POST['ok'] = true;
				// }
				// else {
				// 	echo "make your pass stronger";
				// }
			}
			else {
				$_POST['passnotmatch'] = true;
			}
		}
		else {
			$_POST['logalreadyused'] = true;
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/login.css">
	<script type="text/javascript" src="js/jquery-2.1.1.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
	<title>My blog</title>
</head>
<body>
	<div class="wrapper">
		<div class="header">
			<div class="text-header"><h1>Welcome to our blog</h1></div>
			<div class="clear"></div>
		</div>
		
		<div class="content">
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae natus, at voluptate, doloribus amet ullam incidunt id, commodi rem voluptates velit. Molestias quod sed nihil facere eaque. Sed culpa debitis provident aut est sunt illum repudiandae soluta dolor neque error nemo facere accusantium enim repellendus magnam consectetur, ab ducimus? Ullam ipsum cum soluta dolorem cupiditate placeat, inventore, aliquam itaque officiis eveniet est, possimus obcaecati accusamus quod nesciunt dicta aut dignissimos quo doloribus fuga, magnam provident! Rerum aliquam optio nam sunt iste debitis repellat quasi officiis quis perspiciatis blanditiis autem nobis, dicta molestiae ab sed deleniti, fugit esse voluptatibus dolorum consequatur.</p>
			<center>
			<form class="login" method="post">
				<input type="text" name="login" placeholder="login" required><br>
				<input type="password" name="password" placeholder="password" required><br>
				<input type="submit" name="enter" value="Log in"><br/>
				<?php
					if(isset($_POST['wrong'])) {
						print('Wrong name or password');
					}
				?>
			</form>
			</center>
			
			<center>
			<form  class="registration" method="post" enctype="multipart/form-data">
				<input type="text" name="login" placeholder="login" required><br>
				<input type="text" name="name" placeholder="name" required><br>
				<input type="password" name="password" placeholder="password" required><br>
				<input type="password" name="password2" placeholder="confirm password" required><br>
				<input type="file" name="photo"><br/>
				<input type="submit" name="register" value="Register"><br/>
				<?php
					if(isset($_POST['logalreadyused'])) {
						print('Login already used');
					}
					if(isset($_POST['passnotmatch'])) {
						print('Password not match');
					}
					if(isset($_POST['ok'])) {
						print('You have been registered');
					}
				?>
			</form>
			</center>
			<div class="clear"></div>
		</div>
		<div class="footer">
			<p>Made by Oleksii</p>
		</div>
	</div>
</body>
</html>