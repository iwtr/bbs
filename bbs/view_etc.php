<?php

class View{
	
	public function MenuView() {
		?>
		<table style="border: solid 1px;">
			<tbody>
				<tr>
					<td><a href="index?request=find">トピック検索</a></td>
					<td><a href="index?request=signup">ユーザー登録</a></td>
					<td><a href="index?request=logout">ログアウト</a></td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	
	public function FormLoginView() {
		if(!isset($_COOKIE['name'])) {
		?>
		<div id="b" style="display: table;">
			<?php error(); ?>
			<form method="POST" action="index?request=login">
				<label>ID:</label><input type="text" name="login_id"><br>
				<label>パスワード:</label><input type="password" name="password"><br>
				<?php if(isset($_GET['id'])) { ?>

				<?php } ?>
				<input type="submit" name="submit" value="ログイン"><br>
			</form>
		</div>
		<?php
		}
		else {
			?>
			<span>ユーザー名：<?php echo $_COOKIE['name']; ?> でログイン中</span>
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
						<?php error(); ?>
						<form enctype="multipart/form-data" method="post" action="index?request=add">
							<label for="text1">タイトル：</label><input id="text1" type="text" name="title" placeholder=""><br>
							<?php if(!isset($_COOKIE['name'])) { ?>
								<label for="text2">名前：</label><input id="text2" type="text" name="pen_name"><br>
							<?php } ?>
							<label for="text3">内容：</label><textarea id="text3" name="contents" cols="50" rows="5"></textarea><br>
							<label>画像：</label><input type="file" name="image"><br>
							<label>削除キー：</label><input type="password" name="del_key"><span style="font-size: 10px; color: red;">※設定しなかった場合は0000になります。</span><br>
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
			<?php error(); ?>
			<form enctype="multipart/form-data" method="POST" action="index?request=add">
				<?php if(!isset($_COOKIE['name'])) { ?>
					<label for="text1">名前：</label><input id="text1" type="text" name="pen_name"><br>
				<?php } ?>
				<label for="text2">内容：</label><textarea id="text2" name="contents" cols="50" rows="5"></textarea><br>
				<label>画像：</label><input type="file" name="image"><br>
				<label>削除キー：</label><input type="password" name="del_key"><span style="font-size: 10px; color: red;">※設定しなかった場合は0000になります。</span><br>
				<input type="hidden" name="board_id" value="<?php echo $_GET['board_id']; ?>">
				
				<input type="submit" name="mkcomment" value="投稿">
			</form>
		</div>
		<?php
	}
	
}

?>