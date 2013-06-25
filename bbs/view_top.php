<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css.php">
	<title>掲示板</title>
</head>
<body>
	
	<?php $view->MenuView(); ?>
	<?php $view->FormLoginView(); ?>
	
	<div style="margin: 0px auto;">
		<span style="text-align: center;">
			<?php $view->FormTopicView(); ?>
			<?php $view->BoardsView($boards); ?>
		</span>
	</div>
	<br>
	<div style="text-align: right;">
		<span style="width: 100%;">
			<a href="index?request=admin">管理者用</a>
		</span>
	</div>
	
</body>
</html>