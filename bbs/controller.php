<?php
session_start();

require_once 'model.php';
require_once 'view_etc.php';

//トップ画面表示
class TopController {
	public $model;
	public $view;
	
	public function indexAction() {
		$model = new Model();
		$view = new View();
		$boards = $model->get_boards();
		require_once 'view_top.php';
	}
}

//内容表示
class BoardController {
	public $model;
	public $view;
	
	public function contentsAction() {
		$board_id = $_GET['board_id'];
		$model = new Model();
		$view = new View();
		$comments = $model->show_comments($board_id);
		require_once 'view_board.php';
	}
}

//追加
class AddController {
	public $model;
	
	public function addtopicAction() {
		global $connect;
		
		if(!empty($_POST['title']) && !empty($_POST['contents'])) {
			$del_key = mysqli_real_escape_string($connect, trim($_POST['del_key']));
			if($del_key == NULL) {
				$del_key ="0000";
			}
			if(ctype_alnum($del_key)) {
				$title = mysqli_real_escape_string($connect, trim($_POST['title']));
				$contents = mysqli_real_escape_string($connect, trim($_POST['contents']));
				
				$model = new Model();
				$board_id = $model->addtopic($title, $contents, $del_key);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
				exit();
			}
			else {
				//del_keyが英数字以外
			}
		}
		else {
			$_SESSION['err'] = 'topic_err_1';
		}
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
		exit();
	}
	
	public function addcommentAction() {
		global $connect;
		$board_id = $_POST['board_id'];
		if(!empty($_POST['contents'])) {
			$del_key = mysqli_real_escape_string($connect, trim($_POST['del_key']));
			if($del_key == NULL) {
				$del_key ="0000";
			}
			if(ctype_alnum($del_key)) {
				$contents = mysqli_real_escape_string($connect, trim($_POST['contents']));
				
				$model = new Model();
				$model->addcomment($board_id, $contents, $del_key);
			}
			else {
				//削除キーが英数字以外
			}
		}
		else {
			$_SESSION['err'] = 'comment_err_1';
		}
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
		exit();
	}
}

//更新
class UpdateController {
	public $model;
	public $view;

	public function checkAction() {
		$model = new Model();
		$view = new View();
		
		$row = $model->check_comment($_POST['comment_id']); //array(id, contents, image)
		
		require_once 'view_check.php';
	}
	
	public function updateAction() {
		global $connect;
		$model = new Model();
		$view = new View();
		
		$del_key = $_POST['del_key'];
		$comment_id = $_POST['comment_id'];
		
		if($_POST['img_del'] == 'on') {
			$img_del = TRUE;
		}
		
		$board_id = $model->cid_to_bid($comment_id);
		
		//削除キーが合ってるかどうか
		if($model->check_del_key($comment_id, $del_key)) {
			$newcomment = mysqli_real_escape_string($connect, trim($_POST['newcomm']));
			$model->update_comment($comment_id, $newcomment, $img_del);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
		}
		else {
			//削除キーが間違ってる
			$_SESSION['err'] = 'del_key_err_1';
			$_SESSION['update_again'] =TRUE;
			$row = $model->check_comment($_POST['comment_id']);
			require_once 'view_check.php';
			unset($_SESSION['update_again']);
		}
		exit();
	}
}

//削除
class DeleteController {
	
}

//検索

//
class LoginController {
	public $model;
	
	public function loginAction() {
		global $connect;
		$login_id = mysqli_real_escape_string($connect, trim($_POST['login_id']));
		$password = mysqli_real_escape_string($connect, trim($_POST['password']));
		
		if(!isset($_COOKIE['name'])) {
			$model = new Model();
			$model->check_login($login_id, $password);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
			exit();
		}
		else {
			$_SESSION['err'] = 'login_err_2';
		}
		
	}
}

//ログアウト



//エラー表示
function error(){
	if(isset($_SESSION['err'])) {
		switch ($_SESSION['err']) {
			case 0:
				$err = "記入漏れがあります。";
				break;
			//ログイン時のエラー
			case 'login_err_1':
				$err = "IDまたはパスワードが間違っています。";
				break;
			case 'login_err_2':
				$err = "既にログインしています。";
				break;
			
			//サインアップ時のエラー
			case 'signup_err_1':
				$err = "パスワードは両方とも同じものを入力して下さい。";
				break;
			case 'signup_err_2':
				$err = "同じ名前が既にあります。";
				break;
			
			//コメント入力時のエラー
			case 'comment_err_1':
				$err = "コメントを入力してください。";
				break;
			//トピック入力時のエラー
			case 'topic_err_1':
				$err = "タイトルとコメントを入力して下さい。";
				break;
			
			//del_keyの不一致
			case 'del_key_err_1':
				$err = "削除キーが違います。";
			
			//その他
			default :
				$err = "";
				break;
		}
		unset($_SESSION['err']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}
?>
