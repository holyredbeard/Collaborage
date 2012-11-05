<?php

namespace Controller;

require_once('View/HeaderView.php');

class HeaderController {
	
	/**
     * Funktion som kör GetView i headerView-klassen för att visa menyn
     * 
     * @param Boolean $isLoggedIn, Boolean $isAdmin
     * @return boolean
     */
	public function DoControl($isLoggedIn, $isAdmin) {

		$headerView = new \View\HeaderView();

		$header = $headerView->GetMenu($isLoggedIn, $isAdmin);

		return $header;
	}
}