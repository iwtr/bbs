<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>掲示板</title>
</head>
<body>
	
	<?php $view->MenuView(); ?>
	
	<?php $view->FormLoginView(); ?>
	
	<?php $view->FormTopicView(); ?>
	
	<?php $view->BoardsView($boards); ?>
	
	<a href="index?request=admin">管理者用</a>
	
</body>
</html>