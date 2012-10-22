<?php

namespace Controller;

require_once('View/HeaderView.php');

class HeaderController {
	
	public function DoControl() {

		$headerView = new \View\HeaderView();

		$menu = $headerView->GetMenu();

		$header = "<div id='logo'>Collaborage</div> $menu";

		return $header;
	}
}