<?php
mb_http_output("UTF-8");
mb_internal_encoding("UTF-8");

require_once 'controller.php';

if(isset($_GET['request'])) {
	$request = $_GET['request'];
}
else {
	$request = 'top';
}

switch ($request) {
	//トップ
	case 'top':
		$topcontroller = new TopController();
		$topcontroller->indexAction();
		break;
	
	//内容表示
	case 'board':
		if(isset($_GET['id'])) {
			$boardcontroller = new BoardController();
			$boardcontroller->contentsAction();
			break;
		}
		else {
			//IDが入ってない
		}
		break;
	
	//
		
		
	default :
		header("HTTP/1.0 404 Not Found");
		exit();
		break;
}

$connect = mysqli_close($connect) or die('<br>データベースとの接続を閉じられませんでした。<br>');
?>
