<?php

namespace Controller;

require_once('View/HeaderView.php');

class HeaderController {
	
	public function DoControl($isLoggedIn) {

		$headerView = new \View\HeaderView();

		$header = $headerView->GetMenu($isLoggedIn);

		return $header;
	}
}