<?php
session_start();

require_once 'dbconnect.php';


class Model {
	
	//レコード追加
	public function addcomment($board_id, $contents, $del_key){
		global $connect;
		$pen_name = NULL;

		if(!isset($_COOKIE['name'])) {
			if(!empty($_POST['pen_name'])) {
				$pen_name = mysqli_real_escape_string($connect, trim($_POST['pen_name']));

				$arr = array('【', '】');
				$pen_name = str_replace($arr, "", $pen_name);
			}
		}
		//$contents = mysqli_real_escape_string($connect, $contents);

		if(isset($_COOKIE['id'])) {
			$user_id = $_COOKIE['id'];
		}
		else {
			$user_id = NULL;
		}
		
		$sql = "select max(comnum) from comment where board_id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		if(mysqli_num_rows($result) != 0) {
			$row = mysqli_fetch_array($result);
			$comnum = $row['max(comnum)'] + 1;
		}
		else {
			$comnum = 1;
		}

		$sql = "insert into comment(board_id, comnum, user_id, del_key, pen_name, contents) values('$board_id', '$comnum', '$user_id', '$del_key', '$pen_name', '$contents');";
		$result = mysqli_query($connect, $sql) or die('データを登録できませんでした。');
		$comment_id = $this->last_comment_id($board_id);
		$this->image_upload($comment_id);
		$this->last_updated($board_id);
	}
	
	

	//画像アップロード
	public function image_upload($comment_id) {
		global $connect;

		if($_FILES['image']['error'] == 0) {
			$image_name = $comment_id . $_FILES['image']['name'];
			$image_tmp = $_FILES['image']['tmp_name'];
			$image_type = $_FILES['image']['type'];
			$image_size = $_FILES['image']['size'];
			if(is_uploaded_file($_FILES['image']['tmp_name'])) {
				//画像が規定の容量以内でファイル形式がjpeg,gif,pngか
				if(($image_size > 0 && $image_size < 5000000) && ($image_type == 'image/jpeg' || $image_type == 'image/pjpeg' || $image_type == 'image/gif')) {
					move_uploaded_file($image_tmp, 'image/' . $image_name);
				}
				else {
					//ファイルが大きすぎるか形式がjpg,gif以外です。
				}
			}
		}
		else {
			$image_size = 0;
			$image_name = NULL;
		}
		$sql = "update comment set image='$image_name' where id='$comment_id';";
		$result = mysqli_query($connect, $sql) or die('error');

	}
	
	
	

	//更新・削除時のコメント確認
	public function check_comment($comment_id) {
		global $connect;
		$sql = "select id, user_id, pen_name, contents, image, created_at from comment where id='$comment_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		
		$row = mysqli_fetch_assoc($result);
		$row['contents'] = nl2br($row['contents']);
		$row['img'] = $this->image_exist($row['id']);
		return $row;
	}

	//レコード更新
	public function update_comment($comment_id, $newcomm, $img_del) {
		global $connect;
		$sql = "update comment set contents='$newcomm' where id='$comment_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		if($_FILES['image']['error'] == 0) {
			$this->image_upload($comment_id);
		}
		if($img_del) {
			$sql = "update comment set image='' where id='$comment_id';";
			$result = mysqli_query($connect, $sql) or die('error');
		}
		$board_id = $this->cid_to_bid($comment_id);
		$this->last_updated($board_id);
	}
	
	
	//レコード削除
	public function del_comment($id){
		global $connect;
		$board_del = FALSE;
		$board_id = $this->cid_to_bid($id);
		if($this->board_delcheck($board_id)) {
			$sql = "delete from comment where id='$id';";
			$result = mysqli_query($connect, $sql) or die('データを削除できませんでした。');
		}
		else {
			$this->del_topic($board_id);
			$board_del = TRUE;
		}
		return $board_del;
	}
	

	//トピック追加
	public function addtopic($title, $contents, $del_key) {
		global $connect;
		
		//トピック作成
		$sql = "insert into board(title, del_key) values('$title', '$del_key');";
		$result = mysqli_query($connect, $sql) or die('トピックを作成できませんでした。');

		$sql = "select id from board where title='$title';";
		$result = mysqli_query($connect, $sql) or die('error');
		$board_id = mysqli_fetch_array($result);

		//コメント追加
		$this->addcomment($board_id['id'], $contents, $del_key);

		return $board_id['id'];
	}
	
	//トピック検索
	public function find_topic($str) {
		global $connect;
		$str = mysqli_real_escape_string($connect, $str);
		echo '"' . $str . '" で検索しました。<br>';
		$sql = "select id, title, last_updated from board where title like '%$str%';";
		$result = mysqli_query($connect, $sql) or die('error');
		
		while($row = mysqli_fetch_array($result)) {
			$boards[] = $row;
		}
		
		return $boards;
	}



	//トピック削除
	public function del_topic($board_id) {
		global $connect;

		$sql = "delete board, comment from board, comment where board.id='$board_id' and comment.board_id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');


	}

	//全てのboard取得
	public function get_boards() {
		global $connect;
		$sql = "select id, title, last_updated from board order by last_updated desc;";
		$result = mysqli_query($connect, $sql) or die('error');
		
		while($row = mysqli_fetch_array($result)) {
			$board_id = $row['id'];
			$sql = "select count(board_id) from comment where board_id='$board_id';";
			$result2 = mysqli_query($connect, $sql) or die('error');
			$count = mysqli_fetch_row($result2); //数値添字の配列で返される
			$row['count'] = $count[0];
			$boards[] = $row;
		}
		return $boards;
	}

	//board内のコメント数取得
	public function comment_count($board_id) {
		global $connect;
		$sql = "select count(board_id) from comment where board_id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		$ccount = mysqli_fetch_array($result);

		return $ccount['count(board_id)'];
	}

	//最終投稿id取得
	public function last_comment_id($board_id) {
		global $connect;
		$sql = "select id from comment where board_id='$board_id' order by created_at desc limit 1;";
		$result = mysqli_query($connect, $sql) or die('error');
		$lastid = mysqli_fetch_array($result);

		return $lastid['id'];
	}

	//最終投稿日時取得
	public function last_comment_time($board_id) {
		global $connect;
		$sql = "select created_at from comment where board_id='$board_id' order by created_at desc limit 1;";
		$result = mysqli_query($connect, $sql) or die('error');
		$lastct = mysqli_fetch_array($result);

		return $lastct['created_at'];
	}

	//board内のコメント関連データを全て取得
	public function get_comments($board_id) {
		global $connect;
		$sql = "select id, user_id, pen_name, contents, image, created_at from comment where board_id='$board_id' order by id;";
		$result = mysqli_query($connect, $sql) or die('error');
		
		while($row = mysqli_fetch_array($result)) {
			$comment['id'] = $row['id'];
			$comment['user_id'] = $row['user_id'];
			$comment['pen_name'] = $row['pen_name'];
			$comment['contents'] = $row['contents'];
			$comment['image'] = $row['image'];
			$comment['created_at'] = $row['created_at'];
			if(!empty($row['user_id'])) {
				$comment['user_name'] = '【' . $this->user_id_to_name($row['user_id']) . '】';
			}
			else if(!empty($row['pen_name'])) {
				$comment['user_name'] = $row['pen_name'];
			}
			else {
				$comment['user_name'] = '名無し';
			}
			
			$comment['contents'] = nl2br($row['contents']);
			
			$comment['img'] = $this->image_exist($row['id']);
			
			$comments[] = $comment;
		}
		return $comments;
	}
	
	//コメントのデータ取得　ページ分割版
	public function get_comments_page($board_id, $start, $limit) {
		global $connect;
		$sql = "select id, user_id, pen_name, contents, image, created_at 
						from comment where board_id='$board_id' order by id limit $start, $limit;";
		$result = mysqli_query($connect, $sql) or die('error');
		
		while($row = mysqli_fetch_array($result)) {
			$comment['id'] = $row['id'];
			$comment['user_id'] = $row['user_id'];
			$comment['pen_name'] = $this->ngword_translate($row['pen_name']);
			$comment['contents'] = $row['contents'];
			$comment['image'] = $row['image'];
			$comment['created_at'] = $row['created_at'];
			if(!empty($row['user_id'])) {
				$comment['user_name'] = '【' . $this->ngword_translate($this->user_id_to_name($row['user_id'])) . '】';
			}
			else if(!empty($row['pen_name'])) {
				$comment['user_name'] = $row['pen_name'];
			}
			else {
				$comment['user_name'] = '名無し';
			}
			
			$comment['contents'] = nl2br($this->ngword_translate($row['contents']));
			
			$comment['img'] = $this->image_exist($row['id']);
			
			$comments[] = $comment;
		}
		return $comments;
	}

	//title取得
	public function get_title($board_id) {
		global $connect;
		$sql = "select title from board where id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		$title = mysqli_fetch_assoc($result);

		return $this->ngword_translate($title['title']);
	}

	//user_idをnameに変換
	public function user_id_to_name($user_id) {
		global $connect;
		$sql = "select name from users where id='$user_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		$name = mysqli_fetch_array($result);

		return $name['name'];
	}

	//comment.idをcomment.board_idへ
	public function cid_to_bid($cid) {
		global $connect;
		$sql = "select board_id from comment where id='$cid';";
		$result = mysqli_query($connect, $sql) or die('error');
		$bid = mysqli_fetch_array($result);

		return $bid['board_id'];
	}

	//board内にまだ書き込みがあるか（削除時）
	public function board_delcheck($board_id) {
		global $connect;
		$bool = FALSE;
		$sql = "select id from comment where board_id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		if(mysqli_num_rows($result) > 1) {
			$bool = TRUE;
		}

		return $bool;
	}

	//画像があるか
	public function image_exist($comment_id){
		global $connect;
		$bool = FALSE;
		$sql = "select image from comment where id='$comment_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		$row = mysqli_fetch_array($result);
		if($row['image'] != NULL) {
			$bool = TRUE;
		}
		return $bool;
	}

	//新規ユーザー登録
	public function user_signup($login_id, $password, $name) {
		global $connect;

		//既に同じ名前があるか
		$sql = "select name from users where name='$name'";
		$result = mysqli_query($connect, $sql) or die('error');
		if(mysqli_num_rows($result) == 0) {
			$password = sha1($password);
			$sql = "insert into users(login_id, password, name) values('$login_id', '$password', '$name');";
			$result = mysqli_query($connect, $sql) or die('error');
		}
		else {
			$_SESSION['err_signup'] = 22;
			return FALSE;
		}
		return TRUE;
	}
	
	//ユーザー削除
	public function user_delete($user_id) {
		global $connect;
		
		$sql = "delete from users where id='$user_id';";
		$result = mysqli_query($connect, $sql) or die('error');
	}

	//del_keyが正しいか
	public function check_del_key($comment_id, $del_key) {
		global $connect;
		$bool = FALSE;
		$sql = "select id from comment where id='$comment_id' and del_key='$del_key';";
		$result = mysqli_query($connect, $sql) or die('error');
		if(mysqli_num_rows($result) == 1) {
			$bool = TRUE;
		}
		return $bool;
	}
	public function check_topic_del_key($board_id, $del_key) {
		global $connect;
		$bool = FALSE;
		$sql = "select id from board where id='$board_id' and del_key='$del_key';";
		$result = mysqli_query($connect, $sql) or die('error');
		if(mysqli_num_rows($result) == 1) {
			$bool = TRUE;
		}
		return $bool;
	}
	
	//ログイン処理
	public function check_login($login_id, $password) {
		global $connect;
		$password = sha1($password);
		$sql = "select id, name from users where login_id='$login_id' and password='$password';";
		$result = mysqli_query($connect, $sql) or die('error');
		if(mysqli_num_rows($result) == 1){
			$row = mysqli_fetch_array($result);
			setcookie('id', $row['id'], 0);
			setcookie('name', $row['name'], 0);
		}
		else {
			$_SESSION['err_login'] = 11;
		}
	}
	
	//boardの最終更新日時を書き換え
	public function last_updated($board_id) {
		global $connect;
		
		$sql = "update board set last_updated=now() where id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');
	}
	
	//管理者ログイン IDパスワード照合
	public function admin_login_check($login_id, $password) {
		global $connect;
		$sql = "select id, name from admins where login_id='$login_id' and password='$password';";
		$result = mysqli_query($connect, $sql) or die('error');
		if(mysqli_num_rows($result) == 1){
			$row = mysqli_fetch_array($result);
			setcookie('admin_id', $row['id'], 0);
			setcookie('admin_name', $row['name'], 0);
		}
		else {
			$_SESSION['err_admin'] = 102;
		}
		return $login;
	}
	
	//ボードIDが存在するか
	public function board_id_exist($board_id) {
		global $connect;
		
		$sql = "select id from board where id='$board_id';";
		$result = mysqli_query($connect, $sql);
		if(mysqli_num_rows($result) != 0) {
			return TRUE;
		}
		return FALSE;
	}
	
	
	//NGワードを＊＊＊に変換
	public function ngword_translate($str) {
		
		$str = str_replace(ngword, '＊＊＊', $str);
		
		return $str;
	}
}

class AdminModel extends Model {
	public function check_board($board_id) {
		global $connect;
		$sql = "select id, title, del_key from board where id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');
		$row = mysqli_fetch_array($result);
		return $row;
	}
	
	public function update_board($board_id, $title, $del_key) {
		global $connect;
		$sql = "update board set title='$title', del_key='$del_key' where id='$board_id';";
		$result = mysqli_query($connect, $sql) or die('error');
	}
	
	public function get_users() {
		global $connect;
		
		$sql = "select * from users";
		$result = mysqli_query($connect, $sql);
		while($row = mysqli_fetch_assoc($result)) {
			$users[] = $row;
		}
		if(empty($users)) { $users = array(); }
		return $users;
	}
	
	public function check_user($user_id) {
		global $connect;
		$sql = "select * from users where id='$user_id';";
		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		
		return $row;
	}
}
?>
