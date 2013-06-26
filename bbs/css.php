<?php

header('Content-Type: text/css; charset=utf-8');
require_once 'bbssettings.php';

?>
body {
	background-color: <?php echo background; ?>;
}

form {
	margin: 0px;
}

#board_outerbox {
	background-color: <?php echo board_outerbox_bgcolor; ?>;
}

#board_innerbox {
	background-color: <?php echo board_innerbox_bgcolor; ?>;
}

#forms {
	background-color: <?php echo forms_bgcolor; ?>;
	display: inline-block;
	margin: 5px;
	padding: 5px;
}

#boards {
	background-color: <?php echo boards_bgcolor; ?>;
	display: inline-block;
	border-color: #aaa;
	border-collapse: collapse;
}