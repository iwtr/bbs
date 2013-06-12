<?php

require_once 'model.php';
require_once 'keijiban_error.php';

//トップ画面表示
class TopController {
	public $model;
	
	public function indexAction() {
		$model = new Model();
		$boards = $model->get_boards();
		
		require_once 'view_top.php';
	}
}

//内容表示
class BoardController {
	function contentsAction() {
		
		require_once 'view_board.php';
	}
}

//投稿
class AddController {
	
}

//削除
class DeleteController {
	
}

//更新
class UpdateController {
	
}

//検索


//ログイン

//ログアウト

?>
