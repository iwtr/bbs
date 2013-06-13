<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>確認</title>
</head>
<body>
	<?php if(isset($_POST['check_deltopic'])) { ?>
		トピック：<?php echo $title; ?> を削除します。
		<form method="POST" action="">
			<input type="hidden" name="boar_id" value="<?php echo $board_id; ?>">
			<input type="submit" name="delete_topic" value="削除">
		</form>
	<?php } else { ?>
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
					<?php
					$str = nl2br($row['contents']);
					echo $str;
					?>
				</td>
				<td>
					<?php
					if($model->image_exist($row['id'])) {
						echo '<img style="max-height: 150px; max-width:200px;" src="image/' . $row['image'] . '">';
					} else { ?>
						画像なし
					<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php } if(isset($_POST['check_update']) || isset($_SESSION['update_again'])) { ?>
		上記の内容を更新します。<br>
		<?php error(); ?>
		<form enctype="multipart/form-data" method="POST" action="index?request=update">
			内容：<textarea name="newcomm" cols="50" rows="5"><?php echo $row['contents']; ?></textarea><br>
			画像：<input type="file" name="image">画像を削除する<input type="checkbox" name="img_del" value="on"><br>
			削除キー：<input type="password" name="del_key"><br>
			<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
			<input type="submit" name="update" value="更新する">
		</form>
	<?php } if(isset($_POST['check_delete']) || isset($_SESSION['delete_again'])) { ?>
		上記の内容を削除します。<br>
		<?php error(); ?>
		<form method="POST" action="keijiban_sakujo.php">
			削除キー：<input type="password" name="del_key"><br>
			<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
			<input type="submit" name="delete" value="削除する">
		</form>
	<?php } ?>
	<input type="button" onClick="location.href='index.php';" value="掲示板一覧に戻る">
</body>
</html>