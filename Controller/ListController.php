<?php

namespace Controller;

require_once ('View/ListView.php');
require_once ('Model/ListHandler.php');

class ListController {

	
	public function DoControl($db) {
		$listView = new \View\ListView();
		$listHandler = new \Model\ListHandler($db);

		//if ($listView->WantToViewList()) {
			//$listId = $mainView->GetListToView();
			//
			$listId = 1;

			//$output = $listView->ShowList($listId);
		//}
		//
		
		$listOptions = $listHandler->GetListOptions($listId);		// : Array
		$listElements = $listHandler->GetListElements($listId);		// : Array
		$listUsers = $listHandler->GetListUsers($listId);			// : Var


		$list = array('listId' => $listId,
					  'listOptions' => $listOptions,
					  'listElements' => $listElements,
					  'listUsers' => $listUsers);

		$output .= $listView->ShowList($list);

		return $output;
	}	

	public function ChangeListOrder($listId) {
		$listOptions = $listHandler->GetListOptions($listId);		// Array

		$output = $listView->ShowList;
	}
}