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
		?>
		<table style="background-color: #ddd; margin-top: 5px;">
			<tbody>
				<tr>
					<td>
						新しく掲示板を作る<br>
						<?php error_topic(); ?>
						<form enctype="multipart/form-data" method="post" action="index?request=add">
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
		?>
		<div>
			<?php error_comment(); ?>
			<form enctype="multipart/form-data" method="POST" action="index?request=add">
				<?php if(!isset($_COOKIE['name'])) { ?>
					<label for="text1">名前：</label><input id="text1" type="text" name="pen_name"><br>
				<?php } ?>
				<label for="text2">内容：</label><textarea id="text2" name="contents" cols="50" rows="5"></textarea><br>
				<label>画像：</label><input type="file" name="image"><br>
				<label>削除キー：</label><input type="password" name="del_key" maxlength="4"><span style="font-size: 10px; color: red;">※設定しなかった場合は0000になります。</span><br>
				<input type="hidden" name="board_id" value="<?php echo $_GET['board_id']; ?>">
				
				<input type="submit" name="mkcomment" value="投稿">
			</form>
		</div>
		<?php
	}
	
	public function FormFindView() {
		?>
		<?php error_find(); ?>
		<form method="GET" action="index.php">
			<input type="hidden" name="request" value="find">
			<label>検索文字列：</label><input type="text" name="str">
			<input type="submit" name="search" value="検索">
		</form>
		<?php
	}
	
	public function FormSignupView() {
		?>
		ユーザー情報を入力して下さい<br>
		<?php error_signup(); ?>
		<form method="POST" action="index?request=signup">
			<label for="input1">ID：</label><input id="input1" type="text" name="login_id"><br>
			<label for="input2">パスワード：</label><input id="input2" type="text" name="password1"><br>
			<label for="input3">パスワード（確認）：</label><input id="input3" type="text" name="password2"><br>
			<label for="input4">名前：</label><input id="input4" type="text" name="name"><br>
			<input type="submit" name="signup" value="登録"><br>
		</form>
		<?php
	}
	
	public function FormUpdateView($row) {
		?>
		上記の内容を更新します。<br>
		<?php error_delete(); ?>
		<form enctype="multipart/form-data" method="POST" action="index?request=update">
			<label for="item1">内容：</label><textarea id="item1" name="newcomm" cols="50" rows="5"><?php echo $row['contents']; ?></textarea><br>
			<label for="item2">画像：</label><input id="item2" type="file" name="image">
			<?php if($img) { ?>
			<label for="item3">画像を削除する</label><input id="item3" type="checkbox" name="img_del" value="on">
			<?php } ?>
			<br>
			<label for="item4">削除キー：</label><input id="item4" type="password" name="del_key" maxlength="4"><br>
			<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
			<input type="submit" name="update" value="更新する">
		</form>
		<?php
	}
	
	public function FormTopicDeleteView($title, $board_id) {
		?>
		トピック：<?php echo $title; ?> を削除します。<br>
		<?php error_delete(); ?>
		<form method="POST" action="index?request=delete">
			<label for="item1">削除キー：</label><input id="item1" type="password" name="del_key" maxlength="4"><br>
			<input type="hidden" name="board_id" value="<?php echo $board_id; ?>">
			<input type="submit" name="delete_topic" value="削除">
		</form>
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
	
}

class AdminView extends View {
	public function AdminBoardssView($boards) {
		?>
		<div style="display: table">
			<form method="POST" action="index?request=admin&topic">
				<div style="display: table-cell">
					<table border="1" style=" background-color: #ddd; border-color: #aaa; border-collapse: collapse; margin-top: 5px;">
						<thead><tr><td>削除</td><td>編集</td></tr></thead>
						<tbody>
							<?php foreach ($boards as $row) { ?>
							<tr>
								<td style="text-align: center;"><input type="checkbox" name="delete" value="<?php $row['id'] ?>"></td>
								<td style="text-align: center;">
									<!--
									<form name="form1" method="POST" action="index?request=admin&topic">
										<input type="hidden" name="update">
										<a href="#" onClick="document.form1.submit();">●</a>
									</form>
									-->
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<div style="display: table-cell">
					<?php $this->BoardsView($boards); ?>
				</div>
				<input type="submit" name="delete" value="チェックした項目を削除">
			</form>
			<?php $this->FormTopicView(); ?>
		</div>
		<?php
	}
	
}

?>
