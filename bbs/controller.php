<?php
session_start();

require_once 'model.php';
require_once 'view_etc.php';

//トップ画面表示
class TopController {

	public function indexAction() {
		$model = new Model();
		$boards = $model->get_boards();
		$view = new View();
		if($boards == NULL){
			$boards = array();
		}
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
			$_SESSION['err'] = 41;
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
			$_SESSION['err'] = 31;
		}
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
		exit();
	}
}

//更新
class UpdateController {
	public $model;

	public function checkAction() {
		$model = new Model();
		$comment_id = $_POST['comment_id'];
		$row = $model->check_comment($comment_id); //array(id, contents, image)
		
		require_once 'view_check.php';
	}
	
	public function updateAction() {
		global $connect;
		$model = new Model();
		
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
			$_SESSION['err'] = 99;
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
	public $model;
	
	public function check_topic_deleteAction() {
		$model = new Model();
		
		$board_id = $_POST['board_id'];
		$title = $model->get_title($board_id);
		require_once 'view_check.php';
	}
	
	public function topic_deleteAction() {
		$model = new Model();
		
		$board_id = $_POST['board_id'];
		$del_key = $_POST['del_key'];
		if($model->check_topic_del_key($board_id, $del_key)) {
			$model->del_topic($board_id);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
			exit();
		}
		else {
			$title = $model->get_title($board_id);
			$_SESSION['err'] = 99;
			$_SESSION['delete_topic_again'] =TRUE;
			require_once 'view_check.php';
			unset($_SESSION['delete_topic_again']);
		}
	}
	
	public function check_comment_deleteAction() {
		$model = new Model();
		
		$comment_id = $_POST['comment_id'];
		$row = $model->check_comment($comment_id);
		require_once 'view_check.php';
	}
	
	public function comment_deleteAction() {
		$model = new Model();
		
		$comment_id = $_POST['comment_id'];
		$del_key = $_POST['del_key'];
		$board_id = $model->cid_to_bid($comment_id);
		if($model->check_del_key($comment_id, $del_key)) {
			if($model->board_delcheck($board_id)) {
				$model->del_comment($comment_id);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
			}
			else {
				$model->del_topic($board_id);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
			}
		}
		else {
			$row = $model->check_comment($comment_id);
			$_SESSION['err'] = 99;
			$_SESSION['delete_comment_again'] = TRUE;
			require_once 'view_check.php';
			unset($_SESSION['delete_comment_again']);
		}
	}
}

//検索
class FindController {
	public $model;
	public $view;

	public function findAction() {
		global $connect;
		$view = new View();
		
		if(!empty($_GET['search'])) {
			$model = new Model();
			$str =  mysqli_real_escape_string($connect, trim($_GET['str']));
			$boards = $model->find_topic($str);
		}
		else {
			$boards = array();
		}
		require_once 'view_find.php';
	}
}


//ログイン
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
			$_SESSION['err'] = 12;
		}
		
	}
}

//ログアウト
class LogoutController {
	public $model;
	
	public function logoutAction() {
		setcookie("id", $_COOKIE['id'], time()-1);
		setcookie("name", $_COOKIE['name'], time()-1);
		
		if(isset($_SERVER['HTTP_REFERER'])) {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		}
		else {
			header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/keijiban_top.php');
		}
		exit();
	}
}

//ユーザー登録
class SignupController {
	public $model;
	
	public function signupAction() {
		global $connect;
		$model = new Model();
		
		$login_id = mysqli_real_escape_string($connect, trim($_POST['login_id']));
		$password1 = mysqli_real_escape_string($connect, trim($_POST['password1']));
		$password2 = mysqli_real_escape_string($connect, trim($_POST['password2']));
		$name = mysqli_real_escape_string($connect, trim($_POST['name']));

		if(!empty($login_id) && !empty($password1) && !empty($password2) && !empty($name)) {
			if($password1 == $password2) {

				if($model->user_signup($login_id, $password1, $name)) {
					$model->check_login($login_id, $password1);
					header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
				}
			}
			else {
				$_SESSION['err'] = 21;
			}
		}
		else {
			$_SESSION['err'] = 0;
		}
		require_once 'view_signup.php';
	}
}

//エラー表示 フォームが複数ある場合に対処すること
function error(){
	if(isset($_SESSION['err'])) {
		switch ($_SESSION['err']) {
			case 0:
				$err = "記入漏れがあります。";
				break;
			//ログイン時のエラー
			case 11:
				$err = "IDまたはパスワードが間違っています。";
				break;
			case 12:
				$err = "既にログインしています。";
				break;
			
			//サインアップ時のエラー
			case 21:
				$err = "パスワードは両方とも同じものを入力して下さい。";
				break;
			case 22:
				$err = "同じ名前が既にあります。";
				break;
			
			//コメント作成時のエラー
			case 31:
				$err = "コメントを入力してください。";
				break;
			
			//トピック作成時のエラー
			case 41:
				$err = "タイトルとコメントを入力して下さい。";
				break;
			
			//検索時のエラー
			case 51:
				$err = "見つかりませんでした。";
				break;
			
			//del_keyの不一致
			case 99:
				$err = "削除キーが違います。";
				break;
			
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
