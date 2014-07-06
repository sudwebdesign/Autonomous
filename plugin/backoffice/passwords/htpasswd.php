<?php
use control\crypto\toolbox;
$hash = '';
if(isset($_POST['login'])&&isset($_POST['password']))
	$hash = $_POST['login'].':'.toolbox::crypt_apr1_md5($_POST['password']);
?>
<!DOCTYPE html>
<head></head>
<body>
	<h1>Htaccess HTTP-Auth Password-Generator</h1>
	<form method="POST" action="./htpasswd.php">
		<label for="login">Login</label><input type="text" name="login"></input><br>
		<label for="password">Password</label><input type="text" name="password"></input><br>
		<label for="hash">Hash</label><input type="text" name="hash" value="<?php echo $hash; ?>" readonly></input><br>
		<input type="submit" value="Gen" /><br>
	</form>
</body>
</html>