<?php 

use core\classes\{Controller, Session};


class MainController extends Controller {

	public function actionIndex() {
		//var_dump('Main Controller, action Index');

		require_once 'view/main/index.php';
		return true;
	}

}

?>