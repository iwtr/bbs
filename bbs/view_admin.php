<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css.php">
	<title>管理ページ</title>
</head>
<body>
	管理画面<br>
	<?php echo '管理者：'. $_COOKIE['name']; ?>としてログイン中。<br>
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
			<li><a href="index?request=admin&settings&ngword">禁止ワードの設定</a></li>
			<li><a href="index?request=admin&settings&authority">権限の操作</a></li>
			<li><a href="index?request=admin&settings&color">色変更</a></li>
			<li><a href="index?request=admin&settings&news">トップ画面での new 表示時間</a></li>
			<li><a href="index?request=admin&settings&test">test</a></li>
			<li style="list-style-type: none; height: 15px;"></li>
			<li><a href="index?request=logout">ログアウト</a></li>
		</ul>
	</div>
</body>
</html>