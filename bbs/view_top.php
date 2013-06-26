<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css.php">
	<title>掲示板</title>
</head>
<body>
	
	<?php $view->MenuView(); ?>
	<?php $view->FormLoginView(); ?>
	
	<div style="margin: 0px auto; text-align: center;">
		<?php $view->FormTopicView(); ?>
		<div style="display: inline-table;">
			<div style="display: table-cell;"><?php $view->BoardsView($boards); ?></div>
			<div style="display: table-cell;">
				<table id="boards" border="1">
					<thead><tr><td style="width: 40px;">&nbsp;</td></tr></thead>
					<tbody>
						<?php foreach ($boards as $row) { ?>
						<tr>
							<td><?php if(in_array($row['id'], $new_boards)) { echo 'new'; } else { echo '&nbsp;'; } ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<br>
	<div style="text-align: right;">
		<span style="width: 100%;">
			<a href="index?request=admin">管理者用</a>
		</span>
	</div>
	
</body>
</html>