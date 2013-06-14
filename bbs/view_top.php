
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>掲示板</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	
	<?php $view->MenuView(); ?>
	
	<?php $view->FormLoginView(); ?>
	
	<?php $view->FormTopicView(); ?>
	
	<?php $view->BoardsView($boards); ?>
</body>
</html>