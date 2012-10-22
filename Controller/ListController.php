<?php

namespace Controller;

require_once ('View/ListView.php');
require_once ('Model/ListHandler.php');

class ListController {

	
	public function DoControl($loginHandler, $db, $URLQueryView) {
		$listView = new \View\ListView();
		$listHandler = new \Model\ListHandler($db);
		$userHandler = new \Model\UserHandler($db);
		$loginHandler = new \Model\loginHandler($db);

		if ($listView->WantToCreateList()) {
			$list = $listView->GetNewList($loginHandler);

			$list = $listHandler->SaveNewList($list);

			$output = $listHandler->ShowList($list['listId'], $listView);
		}
		else {

			$action = $URLQueryView->GetAction();

			switch ($action) {
				case 'newList':
					$users = $userHandler->GetAllUsers();
					$output .= $listView->CreateListForm($users, $loginHandler);

					break;

				case 'showLists':
					echo 'hej';
					$lists .= $listHandler->GetAllLists($listView);
					var_dump($lists);
					//$output.= $listView->ShowAllLists($lists);

					break;

				case 'viewList':
					$output = $listHandler->ShowList(101, $listView);

					break;
			}
		}

		return $output;
	}	

	public function ChangeListOrder($listId) {
		$listOptions = $listHandler->GetListOptions($listId);		// Array

		$output = $listView->ShowList;
	}
}



		//if ($listView->WantToViewList()) {
			//$listId = $mainView->GetListToView();
			//
			

			//$output = $listView->ShowList($listId);
		//}
		//
		//