<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>ユーザー登録</title>
</head>
<body>
	ユーザー情報を入力して下さい<br>
	<?php error(); ?>
	<form method="POST" action="index?request=signup">
		<label for="input1">ID：</label><input id="input1" type="text" name="login_id"><br>
		<label for="input2">パスワード：</label><input id="input2" type="text" name="password1"><br>
		<label for="input3">パスワード（確認）：</label><input id="input3" type="text" name="password2"><br>
		<label for="input4">名前：</label><input id="input4" type="text" name="name"><br>
		<input type="submit" name="signup" value="登録"><br>
	</form>
</body>
</html>