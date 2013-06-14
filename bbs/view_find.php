<?php
session_start();
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>検索</title>
</head>
<body>
	<?php error();
	$view->FormFindView();
	$view->BoardsView($boards);
	?>
	<!--
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
					<td><?php echo $model->comment_count($row['id']); //コメント数取得 ?></td>
					<td><?php echo $row['last_updated']; //最終投稿日時取得 ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	-->
</body>
</html>