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
		$result = mysqli_query($connect, $sql);
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
		$result = mysqli_query($connect, $sql);

	}

	//更新・削除時のコメント確認
	public function check_comment($id) {
		global $connect;
		$sql = "select id, contents, image from comment where id='$id';";
		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_array($result);
		return $row;
	}

	//レコード更新
	public function update_comment($comment_id, $newcomm, $img_del) {
		global $connect;
		$sql = "update comment set contents='$newcomm' where id='$comment_id';";
		$result = mysqli_query($connect, $sql);
		if($_FILES['image']['error'] == 0) {
			$this->image_upload($comment_id);
		}
		if($img_del) {
			$sql = "update comment set image='' where id='$comment_id';";
			$result = mysqli_query($connect, $sql);
		}
	}

	//レコード削除
	public function del_comment($id){
		global $connect;
		$sql = "delete from comment where id='$id';";
		$result = mysqli_query($connect, $sql) or die('データを削除できませんでした。');
	}


	//トピック追加
	public function addtopic($title, $contents, $del_key) {
		global $connect;
		
		//トピック作成
		$sql = "insert into board(title, del_key) values('$title', '$del_key');";
		$result = mysqli_query($connect, $sql) or die('トピックを作成できませんでした。');

		$sql = "select id from board where title='$title';";
		$result = mysqli_query($connect, $sql);
		$board_id = mysqli_fetch_array($result);

		//コメント追加
		$this->addcomment($board_id['id'], $contents, $del_key);

		return $board_id['id'];
	}

	//トピック検索
	public function find($str) {
		global $connect;
		$str = mysqli_real_escape_string($connect, $str);
		echo 'str=' . $str;
		$sql = "select title from board where title like '%$str%';";
		$result = mysqli_query($connect, $sql);
		//$row = mysqli_fetch_array($result);

		return $result;
	}



	//トピック削除
	public function del_topic($board_id) {
		global $connect;

		$sql = "delete board, comment from board, comment where board.id='$board_id' and comment.board_id='$board_id';";
		$result = mysqli_query($connect, $sql);


	}

	//全てのboard取得 *
	public function get_boards() {
		global $connect;
		$sql = "select id, title from board;";
		$result = mysqli_query($connect, $sql);
		
		while($row = mysqli_fetch_array($result)) {
			$boards[] = $row;
		}
		return $boards;
	}

	//board内のコメント数取得
	public function comment_count($board_id) {
		global $connect;
		$sql = "select count(board_id) from comment where board_id='$board_id';";
		$result = mysqli_query($connect, $sql);
		$ccount = mysqli_fetch_array($result);

		return $ccount['count(board_id)'];
	}

	//最終投稿id取得
	public function last_comment_id($board_id) {
		global $connect;
		$sql = "select id from comment where board_id='$board_id' order by created_at desc limit 1;";
		$result = mysqli_query($connect, $sql);
		$lastid = mysqli_fetch_array($result);

		return $lastid['id'];
	}

	//最終投稿日時取得
	public function last_comment_time($board_id) {
		global $connect;
		$sql = "select created_at from comment where board_id='$board_id' order by created_at desc limit 1;";
		$result = mysqli_query($connect, $sql);
		$lastct = mysqli_fetch_array($result);

		return $lastct['created_at'];
	}

	//board内のコメント関連データ取得
	public function show_comments($board_id) {
		global $connect;
		$sql = "select id, comnum, user_id, pen_name, contents, image, created_at from comment where board_id='$board_id' order by id;";
		$result = mysqli_query($connect, $sql);
		while($row = mysqli_fetch_array($result)) {
			$comments[] = $row;
		}

		return $comments;
	}

	//title取得
	public function get_title($board_id) {
		global $connect;
		$sql = "select title from board where id='$board_id';";
		$result = mysqli_query($connect, $sql);
		$title = mysqli_fetch_array($result);

		return $title['title'];
	}

	//user_idをnameに変換
	public function user_id_to_name($user_id) {
		global $connect;
		$sql = "select name from users where id='$user_id';";
		$result = mysqli_query($connect, $sql);
		$name = mysqli_fetch_array($result);

		return $name['name'];
	}

	//comment.idをcomment.board_idへ
	public function cid_to_bid($cid) {
		global $connect;
		$sql = "select board_id from comment where id='$cid';";
		$result = mysqli_query($connect, $sql);
		$bid = mysqli_fetch_array($result);

		return $bid['board_id'];
	}

	//board内にまだ書き込みがあるか（削除時）
	public function board_delcheck($board_id) {
		global $connect;
		$bool = FALSE;
		$sql = "select id from comment where board_id='$board_id';";
		$result = mysqli_query($connect, $sql);
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
		$result = mysqli_query($connect, $sql);
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
		$result = mysqli_query($connect, $sql);
		if(mysqli_num_rows($result) == 0) {
			$sql = "insert into users(login_id, password, name) values('$login_id', '$password', '$name');";
			$result = mysqli_query($connect, $sql);
		}
		else {
			$_SESSION['err'] = 'signup_err_2';
			return FALSE;
		}
		return TRUE;
	}

	//del_keyが正しいか
	public function check_del_key($comment_id, $del_key) {
		global $connect;
		$bool = FALSE;
		$sql = "select id from comment where id='$comment_id' and del_key='$del_key';";
		$result = mysqli_query($connect, $sql);
		if(mysqli_num_rows($result) == 1) {
			$bool = TRUE;
		}
		return $bool;
	}
	
	//ログイン処理
	public function check_login($login_id, $password) {
		global $connect;
		if(!empty($login_id) && !empty($password)){
			$sql = "select id, name from users where login_id='$login_id' and password='$password';";
			$result = mysqli_query($connect, $sql);
			if(mysqli_num_rows($result) == 1){
				$row = mysqli_fetch_array($result);
				setcookie('id', $row['id'], 0);
				setcookie('name', $row['name'], 0);
			}
			else {
				$_SESSION['err'] = 'login_err_1';
			}
		}
		else {
			$_SESSION['err'] = 0;
		}
	}
	
	
	
	
}
?>
