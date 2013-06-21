<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>管理ページ</title>
</head>
<body>
	管理画面<br>
	<?php echo $_COOKIE['admin_name']; ?>としてログイン中。<br>
	<div id="menu">
		<ul>
			<li>追加・編集・削除
				<ul>
					<li><a href ="index?request=admin&topic">トピック</a></li>
					<li><a href ="index?request=admin&comment">コメント</a></li>
					<li><a href ="index?request=admin&user">ユーザー</a></li>
				</ul>
			</li>
			<li><a href="index?request=admin&settings&page">ページ最大表示数変更</a></li>
			<!--<li><a href="index?request=admin&settings&ngword">禁止ワードの設定</a></li>-->
			<li style="list-style-type: none; height: 15px;"></li>
			<li><a href="index?request=admin&logout">ログアウト</a></li>
		</ul>
	</div>
</body>
</html>