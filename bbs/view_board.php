<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>掲示板_表示</title>
</head>
<body>
	<div style="text-align: center;">
		<input type="button" onClick="location.href='index.php';" value="掲示板一覧に戻る">
	</div>
	
	<div style="text-align: right; margin: 0px auto; width: 900px;">
		<?php $view->FormLoginView(); ?>
	</div>
	
	<div style="background-color: #eee; margin: 0px auto; padding: 10px; width: 900px;">
		<div style="text-align: center; color: red; font-size: 20px;">
			<?php echo $title; ?>
			
			<div style="display: inline-block; float: right;">
				<form method="POST" action="index?request=delete">
					<input type="hidden" name="board_id" value="<?php echo $board_id; ?>">
					<input type="submit" name="check_delete_topic" value="削除">
				</form>
			</div>
			
		</div>
		<table style="background-color: #ccc; margin: 0px auto; width: 100%;">
			<tbody>
				<?php
				$i = 1;
				foreach ($comments as $row) {
					?>
					<tr>
						<td colspan="2">
							<div style="float: left; width: 40px; margin-right: 10px;">
								<?php echo $i; ?>
							</div>
							<div style="float: left;">
								名前：<?php echo $row['user_name']; ?>
							</div>
							<div style="float: right;">
								投稿日時：<?php echo $row['created_at']; ?>
							</div>
						</td>
					</tr>
					<tr>
						<td style="word-break: break-all; padding: 10px 20px 0px; max-width: 600px;">
							<?php echo $row['contents']; ?>
						</td>
						<td style="max-height: 150px; width: 200px;">
							<?php if($row['img']) { echo '<a href="index?request=image&name='.$row['image'].'"><img style="max-height: 150px; max-width:200px;" src="image/' . $row['image'] . '"></a>'; } ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: right;">
							<form style="display: inline;" method="POST" action="index?request=update">
								<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
								<input type="submit" name="check_update" value="更新">
							</form>
							<form style="display: inline;" method="POST" action="index?request=delete">
								<input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
								<input type="submit" name="check_delete_comment" value="削除">
							</form>
						</td>
					</tr>
					<tr>
							<td colspan="2"><hr style="width: 70%;"></td>
					</tr>
					<?php $i++; } ?>
			</tbody>
		</table>
		<?php $view->FormContentsView(); ?>
	</div>
	<input type="button" onClick="location.href='index.php';" value="掲示板一覧に戻る">
</body>
</html>
