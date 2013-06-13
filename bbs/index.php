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
		if(isset($_GET['board_id'])) {
			$boardcontroller = new BoardController();
			$boardcontroller->contentsAction();
			break;
		}
		else {
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		break;
	
	//ログインチェック
	case 'login':
		$logincontroller = new LoginController();
		$logincontroller->loginAction();
		break;
	
	case 'add':
		$addcontroller = new AddController();
		if(isset($_POST['mktopic'])) {
			$addcontroller->addtopicAction();
		}
		else if(isset($_POST['mkcomment'])) {
			$addcontroller->addcommentAction();
		}
		break;
	
	case 'update':
		$updatecontroller = new UpdateController();
		if(isset($_POST['check_update'])) {
			$updatecontroller->checkAction();
		}
		else if(isset($_POST['update'])) {
			$updatecontroller->updateAction();
		}
		break;
	
	case 'delete':
		$deletecontroller = new DeleteController();
		if(isset($_POST['check_delete_comment'])) {
			
		}
		else if(isset($_POST['delete_comment'])) {
			
		}
		else if(isset($_POST['check_delete_topic'])) {
			
		}
		else if(isset($_POST['delete_topic'])) {
			
		}
		break;
	
	case 'delete_topic':
		break;
	
	default :
		header("HTTP/1.0 404 Not Found");
		exit();
		break;
}

$connect = mysqli_close($connect) or die('<br>データベースとの接続を閉じられませんでした。<br>');
?>
