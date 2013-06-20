<?php
session_start();

class View {
	
	public function MenuView() {
		?>
		<table style="border: solid 1px;">
			<tbody>
				<tr>
					<td><a href="index?request=find">トピック検索</a></td>
					<td><a href="index?request=signup">ユーザー登録</a></td>
					<?php if(isset($_COOKIE['name'])) { ?>
					<td><a href="index?request=user_del">ユーザー削除</a></td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
		<?php
	}
	
	public function FormLoginView() {
		if(!isset($_COOKIE['name'])) {
		?>
		<div style="display: inline-table; text-align: right;">
			<div style="text-align: left;">ログインフォーム<br></div>
			<?php error_login(); ?>
			<form method="POST" action="index?request=login">
				<label>ID:</label><input type="text" name="login_id"><br>
				<label>パスワード:</label><input type="password" name="password"><br>
				<?php if(isset($_GET['board_id'])) { ?>
				<input type="hidden" name="board_id" value="<?php echo $_GET['board_id']; ?>">
				<?php } ?>
				<input type="submit" name="submit" value="ログイン"><br>
			</form>
		</div>
		<?php
		}
		else {
			?>
			<span>ユーザー名：<?php echo $_COOKIE['name']; ?> でログイン中<br>
			<a href="index?request=logout">ログアウト</a></span>
			<?php
		}
	}
	
	public function FormTopicView() {
		if(func_get_args() != NULL) {
			$admin = func_get_arg(0);
			?> <input form="formtopic" type="hidden" name="admin"> <?php
		}
		?>
		<table style="background-color: #ddd; margin: 5px;">
			<tbody>
				<tr>
					<td>
						新しく掲示板を作る<br>
						<?php error_topic(); ?>
						<form id="formtopic" enctype="multipart/form-data" method="post" action="index?request=add">
							<label for="text1">タイトル：</label><input id="text1" type="text" name="title" placeholder=""><br>
							<?php if(!isset($_COOKIE['name'])) { ?>
								<label for="text2">名前：</label><input id="text2" type="text" name="pen_name"><br>
							<?php } ?>
							<label for="text3">内容：</label><textarea id="text3" name="contents" cols="50" rows="5"></textarea><br>
							<label>画像：</label><input type="file" name="image"><br>
							<label>削除キー：</label><input type="password" name="del_key" maxlength="4"><span style="font-size: 10px; color: red;">※設定しなかった場合は0000になります。</span><br>
							<input type="submit" name="mktopic" value="作成">
						</form>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	
	public function FormContentsView() {
		if(func_get_args() != NULL) {
			$admin = func_get_arg(0);
			?>
			<input form="formcontents" type="hidden" name="admin">
			board_id:<input form="formcontents" type="text" name="board_id"><br>
			<?php
		}
		?>
		<div style="display: inline-block; background-color: #eee; margin: 5px;">
			<?php error_comment(); ?>
			<form id="formcontents" enctype="multipart/form-data" method="POST" action="index?request=add">
				<?php if(!isset($_COOKIE['name'])) { ?>
					<label for="text1">名前：</label><input id="text1" type="text" name="pen_name"><br>
				<?php } ?>
				<label for="text2">内容：</label><textarea id="text2" name="contents" cols="50" rows="5"></textarea><br>
				<label>画像：</label><input type="file" name="image"><br>
				<label>削除キー：</label><input type="password" name="del_key" maxlength="4"><span style="font-size: 10px; color: red;">※設定しなかった場合は0000になります。</span><br>
				<?php if(!$admin) { ?>
					<input type="hidden" name="board_id" value="<?php echo $_GET['board_id']; ?>">
				<?php } ?>
				<input type="submit" name="mkcomment" value="投稿">
			</form>
		</div>
		<?php
	}
	
	public function FormFindView() {
		?>
		<div style="display: inline-block; background-color: #eee; margin: 5px;">
			<?php error_find(); ?>
			<form method="GET" action="index.php">
				<input type="hidden" name="request" value="find">
				<label>検索文字列：</label><input type="text" name="str">
				<input type="submit" name="search" value="検索">
			</form>
		</div>
		<?php
	}
	
	public function FormSignupView() {
		?>
		<div style="display: inline-block; background-color: #eee; margin: 5px;">
			新規ユーザーを追加します。<br>
			<?php error_signup(); ?>
			<form method="POST" action="index?request=signup">
				<label for="input1">ID：</label><input id="input1" type="text" name="login_id"><br>
				<label for="input2">パスワード：</label><input id="input2" type="text" name="password1"><br>
				<label for="input3">パスワード（確認）：</label><input id="input3" type="text" name="password2"><br>
				<label for="input4">名前：</label><input id="input4" type="text" name="name"><br>
				<input type="submit" name="signup" value="登録"><br>
			</form>
		</div>
		<?php
	}
	
	public function FormUpdateView($row, $admin) {
		?>
		<div style="display: inline-block; background-color: #eee; margin: 5px;">
			上記の内容を更新します。<br>
			<?php error_delete(); ?>
			<form enctype="multipart/form-data" method="POST" action="index?request=update">
				<label for="item1">内容：</label><textarea id="item1" name="newcomm" cols="50" rows="5"><?php echo $row['contents']; ?></textarea><br>
				<label for="item2">画像：</label><input id="item2" type="file" name="image">
				<?php if($img) { ?>
				<label for="item3">画像を削除する</label><input id="item3" type="checkbox" name="img_del" value="on">
				<?php } ?>
				<br>
				<?php if(!$admin) { ?>
					<label for="item4">削除キー：</label><input id="item4" type="password" name="del_key" maxlength="4"><br>
				<?php } else { ?><input type="hidden" name="admin"><?php } ?>
				<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
				<input type="submit" name="update" value="更新する">
			</form>
		</div>
		<?php
	}
	
	public function FormTopicDeleteView($title, $board_id) {
		?>
		<div style="display: inline-block; background-color: #eee; margin: 5px;">
			トピック：<?php echo $title; ?> を削除します。<br>
			<?php error_delete(); ?>
			<form method="POST" action="index?request=delete">
				<label for="item1">削除キー：</label><input id="item1" type="password" name="del_key" maxlength="4"><br>
				<input type="hidden" name="board_id" value="<?php echo $board_id; ?>">
				<input type="submit" name="delete_topic" value="削除">
			</form>
		</div>
		<?php
	}

	public function FormCommentDeleteView($row) {
		?>
		上記の内容を削除します。<br>
		<?php error_delete(); ?>
		<form method="POST" action="index?request=delete">
			<label for="item1">削除キー：</label><input id="item1" type="password" name="del_key" maxlength="4"><br>
			<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
			<input type="submit" name="delete_comment" value="削除する">
		</form>
		<?php
	}

	public function UserDelete() {
		?>
		現在ログイン中のユーザー <?php echo $_COOKIE['name']; ?> を削除します。<br>
		<form method="POST" action="index?request=user_del">
			<input type="submit" name="delete_user" value="削除する">
		</form>
		<?php
	}

	public function BoardsView($boards) {
		?>
		<table border="1" style=" background-color: #ddd; border-color: #aaa; border-collapse: collapse; margin-top: 5px;">
			<thead>
				<tr>
					<td>タイトル</td>
					<td>コメント数</td>
					<td>最終投稿日時</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($boards as $row) { ?>
					<tr>
						<td><?php echo '<a href="index?request=board&board_id='. $row['id'] .'">' . $row['title'] . '</a>'; ?></td>
						<td><?php echo $row['count']; ?></td>
						<td><?php echo $row['last_updated']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}
	
	public function UpdateDeleteCheckView($row) {
		?>
		<table border="1">
			<thead>
				<tr>
					<td>内容</td>
					<td>画像</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<?php echo $row['contents']; ?>
					</td>
					<td>
						<?php if($row['img']) {
						echo '<img style="max-height: 150px; max-width:200px;" src="image/' . $row['image'] . '">'; }
						else { echo '画像なし'; } ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	
	public function ImageView($name) {
		$page_title = $name;
		require_once 'header.php';
		echo '<img src="image/'.$name.'">';
		require_once 'footer.php';
	}
	
}

class AdminView extends View {
	public function FormAdminLogin() {
		?>
		管理者としてログインします。<br>
		<?php error_admin(); ?>
		<form method="POST" action="index?request=admin">
			<label for="item1">ID：</label><input id="item1" type="text" name="login_id"><br>
			<label for="item2">パスワード：</label><input id="item2" type="password" name="password"><br>
			<input type="submit" name="admin_login" value="ログイン">
		</form>
		<?php
	}
	
	//管理メニュー トピック一覧
	public function AdminBoardsView($boards) {
		?>
		<div style="display: table;">
			<div style="display: table-cell;">
				<table border="1" style=" background-color: #ddd; border-color: #aaa; border-collapse: collapse; margin-top: 5px;">
					<thead><tr><td>削除</td></tr></thead>
					<tbody>
						
						<?php foreach ($boards as $row) { ?>
						<tr>
							<td style="text-align: center;">
								<input form="formboarddelete" type="checkbox" name="delete_board_id[]" value="<?php echo $row['id']; ?>">
							</td>
						</tr>
						<?php } ?>
						
					</tbody>
				</table>
			</div>
			<div style="display: table-cell;">
				<table border="1" style=" background-color: #ddd; border-color: #aaa; border-collapse: collapse; margin-top: 5px;">
					<thead><tr><td>編集</td></tr></thead>
					<tbody>
						<?php foreach ($boards as $row) { ?>
						<tr>
							<td style="text-align: center;">
								<form method="POST" action="index?request=admin&topic">
									<input type="hidden" name="update_board_id" value="<?php echo $row['id']; ?>">
									<input style="margin: 0px;" type="submit" name="chkupdate" value="編集">
								</form>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div style="display: table-cell;">
				<?php $this->BoardsView($boards); ?>
			</div>
		</div>
		<div>
			<form id="formboarddelete" method="POST" action="index?request=admin&topic">
				<input type="submit" name="chkdelete" value="チェックした項目を削除">
			</form>
			
		</div>
		<?php
	}
	
	public function AdminBoardsDeleteCheckView($titles) {
		?>
		以下のトピックを削除します。<br>
		<?php foreach ($titles as $title) {	echo $title.'<br>'; } ?>
		<br>
		<form style="display: inline-block;" method="POST" action="index?request=admin&topic">
			<input type="submit" name="delete" value="確認">
		</form>
		<input type="button" onClick="location.href='<?php echo $_SERVER['HTTP_REFERER']; ?>';" value="戻る">
		<?php
	}
	
	public function AdminCommentsDeleteCheckView($comments) { //続き
		?>
		以下のコメントを削除します。<br>
		<table border="1">
			<thead>
				<tr>
					<td>user_id</td>
					<td>pen_name</td>
					<td>contents</td>
					<td>image</td>
					<td>created_at</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($comments as $comment) {	?>
					<tr>
						<?php $this->AdminCommentsColumnsView($comment); ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<br>
		<form style="display: inline-block;" method="POST" action="index?request=admin&comment">
			<input type="submit" name="delete" value="確認">
		</form>
		<input type="button" onClick="location.href='<?php echo $_SERVER['HTTP_REFERER']; ?>';" value="戻る">
		<?php
	}
	
	public function AdminBoardUpdateCheckView($row) {
		?>
		<div style="display: table;">
			<div style="display: table-header-group;">
				<div style="display: table-cell; border: solid 1px;">
					タイトル
				</div>
				<div style="display: table-cell; border: solid 1px;">
					削除キー
				</div>
			</div>
			<div style="display: table-row;">
				<div style="display: table-cell; border: solid 1px;">
					<?php echo $row['title']; ?>
				</div>
				<div style="display: table-cell; border: solid 1px;">
					<?php echo $row['del_key']; ?>
				</div>
			</div>
		</div>
		
		<div>
			上記の内容を更新します。<br>
			<?php error_admin(); ?>
			<form method="POST" action="index?request=admin&topic">
				<label for="item1">タイトル：</label><input id="item1" type="text" name="title"><br>
				<label for="item2">削除キー：</label><input id="item2" type="password" maxlength="4" name="del_key"><br>
				<input type="hidden" name="board_id" value="<?php echo $row['id']; ?>">
				<input type="submit" name="update" value="更新する">
			</form>
		</div>
		<?php
	}
	
	//管理メニュー コメント一覧
	public function AdminCommentsView($board_id, $title, $comments) {
		//title, array(id, user_id, pen_name, contents, image, created_at, user_name, img)
		?>
		<div style="display: table; background-color: #eee; margin-bottom: 10px; padding: 5px;">
			ID：<span style="color: red;"><?php echo $board_id; ?></span> タイトル：<?php echo $title; ?>
			<table border="1">
				<thead>
					<tr>
						<td>削除</td>
						<td>編集</td>
						<td>user_id</td>
						<td>pen_name</td>
						<td>contents</td>
						<td>image</td>
						<td>created_at</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($comments as $row) { ?>
					<tr>
						<td><input form="formcommentdelete" type="checkbox" name="delete_comment_id[]" value="<?php echo $row['id']; ?>"></td>
						<td>
							<form method="POST" action="index?request=admin&comment">
								<input type="hidden" name="update_comment_id" value="<?php echo $row['id']; ?>">
								<input style="margin: auto;" type="submit" name="chkupdate" value="編集">
							</form>
						</td>
						<?php $this->AdminCommentsColumnsView($row) ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<?php
	}
	public function AdminCommentsColumnsView($row) {
		?>
		<td style="width: 4em;"><?php echo $row['user_id']; ?></td>
		<td style="width: 12em;"><?php echo $row['pen_name']; ?></td>
		<td style="width: 25em;"><?php echo $row['contents']; ?></td>
		<td style="text-align: center;">
			<?php if($row['img']) { echo '<a href="index?request=image&name='.$row['image'].'">●</a>'; } ?>
		</td>
		<td><?php echo $row['created_at']; ?></td>
		<?php
	}
	public function AdminFormCommentsView() {
		?>
		<form id="formcommentdelete" method="POST" action="index?request=admin&comment">
			<input type="submit" name="chkdelete" value="チェックした項目を削除">
		</form>
		<?php
	}
	
	//管理メニュー ユーザー一覧
	public function AdminUsersView($users) {
		if(isset($_POST['chkdelete'])) { $display = "none"; $name = "delete"; ?>以下のユーザーを削除します。<?php }
		else { $display = ""; $name = "chkdelete"; }
		?>
		<div style="display: table; background-color: #eee; margin-bottom: 10px; padding: 5px;">
			<table border="1">
				<thead>
					<tr>
						<td style="display: <?php echo $display; ?>">削除</td>
						<td>id</td>
						<td>login_id</td>
						<td>password</td>
						<td>name</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($users as $row) { ?>
					<tr>
						<td style="display: <?php echo $display; ?>;">
							<input form="formuserdelete" type="checkbox" name="delete_user_id[]" value="<?php echo $row['id']; ?>">
						</td>
						<td><?php echo $row['id']; ?></td>
						<td><?php echo $row['login_id']; ?></td>
						<td><?php echo $row['password']; ?></td>
						<td><?php echo $row['name']; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<form id="formuserdelete" method="POST" action="index?request=admin&user">
				<input type="submit" name="<?php echo $name; ?>" value="チェックした項目を削除">
			</form>
		</div>
		<?php
	}
	
}

?>
