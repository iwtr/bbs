<?php
session_start();

header("Content-type: text/html; charset=utf-8");

require_once 'model.php';

//トップ画面表示
class TopController {

	public function indexAction() {
		$model = new Model();
		$boards = array();
		
		$boards = $model->get_boards();
		$view = new View();
		require_once 'view_top.php';
	}
}

//内容表示
class BoardController {
	
	public function contentsAction() {
		$model = new Model();
		$view = new View();
		$board_id = $_GET['board_id'];
		
		$title = $model->get_title($board_id);
		$comments = $model->show_comments($board_id);
		
		
		require_once 'view_board.php';
	}
}

//追加
class AddController {
	
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
				$board_id = $model->addtopic(htmlspecialchars($title), htmlspecialchars($contents), $del_key);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
				exit();
			}
			else {
				$_SESSION['err_topic'] = 98;
			}
		}
		else {
			$_SESSION['err_topic'] = 41;
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
				$model->addcomment($board_id, htmlspecialchars($contents), $del_key);
			}
			else {
				$_SESSION['err_comment'] = 98;
			}
		}
		else {
			$_SESSION['err_comment'] = 31;
		}
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
		exit();
	}
}

//更新
class UpdateController {

	public function checkAction() {
		$model = new Model();
		$view = new View();
		
		$comment_id = $_POST['comment_id'];
		$row = $model->check_comment($comment_id);
		$page_title = "更新";
		require_once 'header.php';
		$view->UpdateDeleteCheckView($row);
		$view->FormUpdateView($row);
		require_once 'footer.php';
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
			$_SESSION['err_delete'] = 99;
			$row = $model->check_comment($_POST['comment_id']);
			$page_title = "更新";
			require_once 'header.php';
			$view->UpdateDeleteCheckView($row);
			$view->FormUpdateView($row);
			require_once 'footer.php';
		}
		exit();
	}
}

//削除
class DeleteController {
	
	public function check_topic_deleteAction() {
		$model = new Model();
		$view = new View();
		$board_id = $_POST['board_id'];
		$title = $model->get_title($board_id);
		$page_title = "トピック削除";
		require_once 'header.php';
		$view->FormTopicDeleteView($title, $board_id);
		require_once 'footer.php';
	}
	
	public function topic_deleteAction() {
		$model = new Model();
		$view = new View();
		
		$board_id = $_POST['board_id'];
		$del_key = $_POST['del_key'];
		if($model->check_topic_del_key($board_id, $del_key)) {
			$model->del_topic($board_id);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
			exit();
		}
		else {
			$title = $model->get_title($board_id);
			$_SESSION['err_delete'] = 99;
			$page_title = "トピック削除";
			require_once 'header.php';
			$view->FormTopicDeleteView($title, $board_id);
			require_once 'footer.php';
		}
	}
	
	public function check_comment_deleteAction() {
		$model = new Model();
		$view = new View();
		
		$comment_id = $_POST['comment_id'];
		$row = $model->check_comment($comment_id);
		$page_title = "コメント削除";
		require_once 'header.php';
		$view->UpdateDeleteCheckView($row);
		$view->FormCommentDeleteView($row);
		require_once 'footer.php';
	}
	
	public function comment_deleteAction() {
		$model = new Model();
		$view = new View();
		
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
			$_SESSION['err_delete'] = 99;
			$page_title = "コメント削除";
			require_once 'header.php';
			$view->UpdateDeleteCheckView($row);
			$view->FormCommentDeleteView($row);
			require_once 'footer.php';
		}
	}
}

//検索
class FindController {

	public function findAction() {
		global $connect;
		$view = new View();
		$boards = array();
		
		if(!empty($_GET['search'])) {
			$model = new Model();
			$str =  mysqli_real_escape_string($connect, trim($_GET['str']));
			$boards = $model->find_topic($str);
			if(empty($boards)){
				$_SESSION['err_find'] = 51;
				$boards = array();
			}
		}
		$page_title = "タイトル検索";
		require_once 'header.php';
		$view->FormFindView();
		$view->BoardsView($boards);
		require_once 'footer.php';
	}
}


//ログイン
class LoginController {
	
	public function loginAction() {
		global $connect;
		$login_id = mysqli_real_escape_string($connect, trim($_POST['login_id']));
		$password = mysqli_real_escape_string($connect, trim($_POST['password']));
		
		if(!empty($login_id) && !empty($password)){
			$model = new Model();
			$model->check_login($login_id, $password);
			if(isset($_POST['board_id'])) {
				$board_id = $_POST['board_id'];
				header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=board&board_id=' . $board_id);
			}
			else {
				header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
			}
			exit();
		}
		else {
			$_SESSION['err_login'] = 13;
		}
		header('Location: ' . $_SERVER['HTTP_REFERER']);
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
				$_SESSION['err_signup'] = 21;
			}
		}
		else {
			$_SESSION['err_signup'] = 23;
		}
		require_once 'view_signup.php';
	}
}

//ユーザー削除
class UserDelController {
	
	public function userdelAction() {
		$model = new Model();
		$user_id = $_COOKIE['id'];
		
		$model->user_delete($user_id);
		setcookie("id", $_COOKIE['id'], time()-1);
		setcookie("name", $_COOKIE['name'], time()-1);
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
	}
}

//管理者用操作
class AdminController {
	public function adminAction() {
		$adminview = new AdminView();
		$adminmodel = new AdminModel();
		
		if(isset($_GET['topic'])) {
			if(isset($_POST['delete'])){
				echo 'delete';
			}
			else if(isset($_POST['update'])) {
				echo 'update';
			}
			else if(isset($_POST['add'])) {
				echo 'add';
			}
			else {
				$boards = $adminmodel->get_boards();
				$page_title = "トピック一覧";
				require_once 'header.php';
				$adminview->AdminBoardssView($boards);
				require_once 'footer.php';
			}
		}
		else if(isset($_GET['comment'])) {
			
		}
		else if(isset($_GET['user'])) {
			
		}
		else {
			require_once 'view_admin.php';
		}
	}
	
	public function adminloginAction() {
		global $connect;
		$model = new Model();
		$login_id = mysqli_real_escape_string($connect, trim($_POST['login_id']));
		$password = mysqli_real_escape_string($connect, trim($_POST['password']));
		
		if(!empty($login_id) && !empty($password)) {
			$model->admin_login_check($login_id, $password);
		}
		else {
			$_SESSION['err_admin'] = 101;
		}
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index?request=admin');
		exit();
	}
	
	public function adminlogoutAction() {
			setcookie("admin_id", $_COOKIE['admin_id'], time()-1);
			setcookie("admin_name", $_COOKIE['admin_name'], time()-1);
			
			header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
	}
}



//エラー表示
function error_login(){
	if(isset($_SESSION['err_login'])) {
		switch ($_SESSION['err_login']) {
			//ログイン時のエラー
			case 11:
				$err = "IDまたはパスワードが間違っています。";
				break;
			case 12:
				$err = "既にログインしています。";
				break;
			case 13:
				$err = "IDとパスワードを入力して下さい。";
				break;
			
			//その他
			default :
				$err = "";
				break;
		}
		unset($_SESSION['err_login']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}
function error_signup(){
	if(isset($_SESSION['err_signup'])) {
		switch ($_SESSION['err_signup']) {
			//サインアップ時のエラー
			case 21:
				$err = "パスワードは両方とも同じものを入力して下さい。";
				break;
			case 22:
				$err = "同じ名前が既にあります。";
				break;
			case 23:
				$err = "全て記入してください。";
				break;
	
			//その他
			default :
				$err = "";
				break;
		}
		unset($_SESSION['err_signup']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}
function error_comment(){
	if(isset($_SESSION['err_comment'])) {
		switch ($_SESSION['err_comment']) {
			//コメント作成時のエラー
			case 31:
				$err = "コメントを入力してください。";
				break;
			case 98:
				$err = "削除キーが英数字以外です。";
				break;
			
			//その他
			default :
				$err = "";
				break;
		}
		unset($_SESSION['err_comment']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}
function error_topic(){
	if(isset($_SESSION['err_topic'])) {
		switch ($_SESSION['err_topic']) {
			//トピック作成時のエラー
			case 41:
				$err = "タイトルとコメントを入力して下さい。";
				break;
			case 98:
				$err = "削除キーが英数字以外です。";
				break;
			
			//その他
			default :
				$err = "";
				break;
		}
		unset($_SESSION['err_topic']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}
function error_find(){
	if(isset($_SESSION['err_find'])) {
		switch ($_SESSION['err_find']) {
			//検索時のエラー
			case 51:
				$err = "見つかりませんでした。";
				break;
			
			//その他
			default :
				$err = "";
				break;
		}
		unset($_SESSION['err_find']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}
function error_delete() {
	if(isset($_SESSION['err_delete'])) {
		switch ($_SESSION['err_delete']) {
			//削除時のエラー
			case 99:
				$err = "削除キーが違います。";
				break;
			
			//その他
			default :
				$err = "";
				break;
		}
		unset($_SESSION['err_delete']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}
function error_admin() {
	if(isset($_SESSION['err_admin'])) {
		switch ($_SESSION['err_admin']) {
			case 101:
				$err = "全て入力してください。";
				break;
			case 102:
				$err = "ID又はパスワードが間違っています。";
		}
		unset($_SESSION['err_admin']);
		echo '<span style="color: red;">' . $err . '</span>';
	}
}


function page_title() {
	$view = new View();
	$num = func_num_args();
	$page_title = func_get_arg(0);
	for($i=1; $i<$num; $i++) {
		$array[] = func_get_arg($i);
	}
	
	require_once 'header.php';
	foreach($array as $funcname) {
		$view->$funcname();
	}
	require_once 'footer.php';
}


?>
