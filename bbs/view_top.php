<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>掲示板</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	
	<!-- メニュー -->
	<table style="border: solid 1px;">
		<tbody>
			<tr>
				<td><a href="keijiban_kensaku.php">トピック検索</a></td>
				<td><a href="keijiban_signup.php">ユーザー登録</a></td>
				<td><a href="keijiban_logout.php">ログアウト</a></td>
			</tr>
		</tbody>
	</table>
	
	<div id="b" style="display: table;">
		<?php
		// error();
		require_once 'keijiban_login.html';
		?>
	</div>
	
	<?php
	if(isset($_COOKIE['name'])) {
		echo 'ユーザー名：' . $_COOKIE['name'] . 'でログイン中。';
	}
	else {
		echo 'ログインしていません。';
	}
	?>
	<br>
	
	<!-- トピック一覧 -->
	<table border="1" style=" background-color: #ddd; border-color: #aaa; border-collapse: collapse; margin-top: 5px;">
		<thead>
			<tr>
				<td>タイトル</td>
				<td>コメント数</td>
				<td>最終投稿日時</td>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($boards as $row) {
				?>
				<tr>
					<td><?php echo '<a href="index?request=board&id='. $row['id'] .'">' . $row['title'] . '</a>'; ?></td>
					<td><?php echo $model->comment_count($row['id']); //コメント数取得 ?></td>
					<td><?php echo $model->last_comment_time($row['id']); //最終投稿日時取得 ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	
	
	
	
	<table style="background-color: #ddd; margin-top: 5px;">
		<tbody>
			<tr>
				<td>
					新しく掲示板を作る<br>
					<?php //error(); ?>
					<form enctype="multipart/form-data" method="post" action="keijiban_tsuika.php">
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
</body>
</html>