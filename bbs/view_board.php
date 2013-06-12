<?php
session_start();

$board_id = $_GET['id'];

?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>掲示板_表示</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<input type="button" onClick="location.href='keijiban_top.php';" value="掲示板一覧に戻る">
	<div style="text-align: right; margin: 0px auto; width: 900px;">
		<?php if(isset($_COOKIE['name'])) { ?><a href="keijiban_logout.php">ログアウト</a>
		<?php } else { ?><a href="keijiban_login.html">ログイン</a> <?php } ?>
	</div>
	<div style="background-color: #eee; margin: 0px auto; padding: 10px; width: 900px;">
		<div style="text-align: center; color: red; font-size: 20px;">
			<?php echo get_title($board_id); ?>
			<div style="display: inline-block; float: right;">
				<form method="POST" action="keijiban_sakujo.php">
					<input type="hidden" name="board_id" value="<?php echo $board_id; ?>">
					<input type="submit" name="check_deltopic" value="削除">
				</form>
			</div>
		</div>
		<table style="background-color: #ccc; margin: 0px auto; width: 100%;">
			<tbody>
				<?php
				$result = show_comments($board_id); //board内のコメント関連データ取得
				$i = 1;
				while($row = mysqli_fetch_array($result)){
					?>
					<tr>
						<td colspan="2">
							<div style="float: left; width: 40px; margin-right: 10px;">
								<?php echo $i; ?>
							</div>
							<div style="float: left;">
								名前：
									<?php
									if(!empty($row['user_id'])) {
										echo '【' . user_id_to_name($row['user_id']) . '】';
									}
									else if(!empty($row['pen_name'])) {
										echo $row['pen_name'];
									}
									else {
										echo '名無し';
									}
									?>
							</div>
							<div style="float: right;">
								投稿日時：<?php echo $row['created_at']; ?>
							</div>
						</td>
					</tr>
					<tr>
						<td style="word-break: break-all; padding: 10px 20px 0px; max-width: 600px;">
							<?php
							$str = nl2br($row['contents']);
							echo $str;
							?>
							
						</td>
						<td style="max-height: 150px; width: 200px;">
							<?php if(image_exist($row['id'])) { echo '<img style="max-height: 150px; max-width:200px;" src="image/' . $row['image'] . '">'; } ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: right;">
							<form style="display: inline;" method="POST" action="keijiban_tsuika.php">
								<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
								<input type="submit" name="check_update" value="更新">
							</form>
							<form style="display: inline;" method="POST" action="keijiban_sakujo.php">
								<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
								<input type="submit" name="check_delete" value="削除">
							</form>
						</td>
					</tr>
					<tr>
							<td colspan="2"><hr style="width: 70%;"></td>
					</tr>
					<?php $i++; } ?>
			</tbody>
		</table>
		
		<div>
			<?php error(); ?>
			<form enctype="multipart/form-data" method="POST" action="keijiban_tsuika.php"> <!-- コメント追加時の処理へ -->
				<?php if(!isset($_COOKIE['name'])) { ?>
					<label for="text1">名前：</label><input id="text1" type="text" name="pen_name"><br>
				<?php } ?>
				<label for="text2">内容：</label><textarea id="text2" name="contents" cols="50" rows="5"></textarea><br>
				<label>画像：</label><input type="file" name="image"><br>
				<label>削除キー：</label><input type="password" name="del_key"><span style="font-size: 10px; color: red;">※設定しなかった場合は0000になります。</span><br>
				<input type="hidden" name="board_id" value="<?php echo $board_id; ?>">
				
				<input type="submit" name="mkcomment" value="投稿">
			</form>
		</div>
	</div>
	<input type="button" onClick="location.href='keijiban_top.php';" value="掲示板一覧に戻る">
	
	<?php $connect = mysqli_close($connect) or die('データベースとの接続を閉じられませんでした。'); ?>
</body>
</html>
