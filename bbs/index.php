<?php
mb_http_output("UTF-8");
mb_internal_encoding("UTF-8");

require_once 'controller.php';
require_once 'view_etc.php';

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
	
	//検索
	case 'find':
		$findcontroller = new FindController();
		$findcontroller->findAction();
		break;
	
	//ログインチェック
	case 'login':
		$logincontroller = new LoginController();
		$logincontroller->loginAction();
		break;
	
	//ログアウト
	case 'logout':
		$logoutcontroller = new LogoutController();
		$logoutcontroller->logoutAction();
		break;
	
	//追加
	case 'add':
		$addcontroller = new AddController();
		if(isset($_POST['mktopic'])) {
			$addcontroller->addtopicAction();
		}
		else if(isset($_POST['mkcomment'])) {
			$addcontroller->addcommentAction();
		}
		break;
	
	//更新
	case 'update':
		$updatecontroller = new UpdateController();
		if(isset($_POST['check_update'])) {
			$updatecontroller->checkAction();
		}
		else if(isset($_POST['update'])) {
			$updatecontroller->updateAction();
		}
		break;
	
	//削除
	case 'delete':
		$deletecontroller = new DeleteController();
		if(isset($_POST['check_delete_topic'])) {
			$deletecontroller->check_topic_deleteAction();
		}
		else if(isset($_POST['delete_topic'])) {
			$deletecontroller->topic_deleteAction();
		}
		else if(isset($_POST['check_delete_comment'])) {
			$deletecontroller->check_comment_deleteAction();
		}
		else if(isset($_POST['delete_comment'])) {
			$deletecontroller->comment_deleteAction();
		}
		break;
	
	//ユーザー登録
	case 'signup':
		if(isset($_POST['signup'])) {
			$signupcontroller = new SignupController();
			$signupcontroller->signupAction();
		}
		else {
			$view = new View();
			$page_title = 'ユーザー登録';
			$message = '新規ユーザーを追加します。';
			$submit = 'signup';
			require_once 'header.php';
			$view->FormSignupView($message, $submit);
			require_once 'footer.php';
		}
		break;
	
	//ユーザー削除
	case 'user_del':
		if(isset($_POST['delete_user'])) {
			$userdelcontroller = new UserDelController();
			$userdelcontroller->userdelAction();
		}
		else {
			$view = new View();
			$view->UserDelete();
		}
		break;
	
	//ユーザー情報変更
	case 'user_update':
		if(isset($_POST['user_update'])) {
			$userupdatecontroller = new UserUpdateController();
			$userupdatecontroller->userupdateAction();
		}
		else {
			$view = new View();
			$adminmodel = new AdminModel();
			$user_info = $adminmodel->check_user($_COOKIE['id']);
			$message = 'ユーザー情報を変更します。';
			$submit = 'user_update';
			$page_title = 'ユーザー情報編集';
			require_once 'header.php';
			$view->FormSignupView($message, $submit, $user_info);
			require_once 'footer.php';
		}
		break;
	
	//管理者用
	case 'admin':
		$admincontroller = new AdminController();
		//if(isset($_GET['logout'])) {
		//	$admincontroller->adminlogoutAction();
		//}
		if($_COOKIE['admin']) {
			$admincontroller->adminAction();
		}
		//else if(isset($_POST['admin_login'])) {
		//	$admincontroller->adminloginAction();
		//}
		else {
			$adminview = new AdminView();
			echo '管理者権限のあるユーザーでログインして下さい。<br>';
			page_title("ログイン", 'FormLoginView');
		}
		break;
	
	//画像の原寸表示
	case 'image':
		if(isset($_GET['name'])) {
			$view = new View();
			$view->ImageView($_GET['name']);
		}
		break;
	
	default :
		header("HTTP/1.0 404 Not Found");
		exit();
		break;
}

$connect = mysqli_close($connect) or die('<br>データベースとの接続を閉じられませんでした。<br>');
?>
