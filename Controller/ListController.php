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

		$user = $loginHandler->GetStoredUser();

		if ($listView->WantToSaveNewOrderedList()){
			echo 'yes2';
		} else {
			echo 'nej';
		}

		$action = $URLQueryView->GetAction();

		switch ($action) {
			case 'newList':
				if ($listView->WantToCreateList()) {
					$list = $listView->GetNewList($loginHandler, $user);

					$list = $listHandler->SaveNewList($list);

					$output = $listHandler->ShowList($list['listId'], $listView);
				}
				else {
					$users = $userHandler->GetAllUsers();
					$output .= $listView->CreateListForm($users, $loginHandler);
				}

				break;

			case 'showLists':
				//$lists = $listHandler->GetAllLists($listView);
				//
				$publicLists = $listHandler->GetAllPublicLists();

				$assignedLists = $listHandler->GetAssignedLists($user['userId']);

				$output .= $listView->ShowAllLists($publicLists, $assignedLists);

				break;

			case 'showList':

				$listId = $URLQueryView->GetListId();

				$output .= $listHandler->ShowList($listId, $listView);

				break;

			case 'viewList':
				$output .= $listHandler->ShowList(101, $listView);

				break;

			case 'saveNewListOrder':
				$listOrder = $URLQueryView->GetListOrder();

				$return = $listHandler->SaveListOrder();

				echo $return;

				break;
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