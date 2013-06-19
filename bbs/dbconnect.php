<?php
define('dbhost', 'localhost');
define('dbuser', 'root');
define('dbpass', '12345');
define('dbname', 'TRAINING01');

$connect = mysqli_connect(dbhost, dbuser, dbpass, dbname) or die('<br>MySQLに接続できませんでした。<br>');
$result = mysqli_select_db($connect, 'TRAINING01') or die('エラー（mysqli_select_db）');
mysqli_set_charset($connect, "utf8");

?>
