<?php

namespace Controller;

require_once('View/HeaderView.php');

class HeaderController {
	
	public function DoControl() {

		$headerView = new \View\HeaderView();

		$header = $headerView->GetMenu();



		return $header;
	}
}