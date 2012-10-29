<?php

namespace Controller;

require_once ('View/ListView.php');
require_once ('Model/ListHandler.php');

class ListController {

	
	public function DoControl($loginHandler, $db, $URLQueryView, $IsLoggedIn) {
		$listView = new \View\ListView();
		$listHandler = new \Model\ListHandler($db);
		$userHandler = new \Model\UserHandler($db);
		$loginHandler = new \Model\loginHandler($db);

		$user = $loginHandler->GetStoredUser();

		$action = $URLQueryView->GetAction();

		switch ($action) {
			case 'newList':
				if ($IsLoggedIn) {
					if ($listView->WantToCreateList()) {
						$list = $listView->GetNewList($loginHandler, $user);

						$list = $listHandler->SaveNewList($list);

						$output = $listHandler->ShowList($list['listId'], $listView, false, false);
					}
					else {
						$users = $userHandler->GetAllUsers();
						$output .= $listView->CreateListForm($users, $loginHandler);
					}
				}
				else {
					$output .= $listView->ShowNotLoggedIn();
				}

				break;

			case 'showLists':
				//$lists = $listHandler->GetAllLists($listView);
				//
				$publicLists = $listHandler->GetAllPublicLists();

				if ($IsLoggedIn) {
					$assignedLists = $listHandler->GetAssignedLists($user['userId']);
				}
				else {
					$assignedLists = null;
				}

				$output .= $listView->ShowAllLists($publicLists, $assignedLists);

				break;

			case 'showList':

				$listId = $URLQueryView->GetListId();

				// TODO: Här måste det kollas VARJE enskild användare om de är klara, inte bara denna.
				// Vidare om någon inte är klar visas den lista man senast skapade, låst.
				// Om möjlighet ska finnas ska det här gå att låsa upp om man vill ändra

				$isFinished = $listHandler->HasFinishedSorting($user['userId'], $listId);

				// If not finished, let's sort the list!
				if ($isFinished != false) {

					$listUsers = $listHandler->GetListUsersIds($listId);

					$listOrders = $listHandler->GetListOrders($listId, $listUsers);

					$orderedList = $listHandler->CalculateOrder($listOrders);

					$wasAdded = $listHandler->AddListElemOrderPlaces($orderedList);
				}

				// check if the list sorting is done
				$listIsDone = $listHandler->CheckListStatus($listId);

				// Show the list!
				$output .= $listHandler->ShowList($listId, $listView, $userIsFinished, $listIsDone);

				break;

			case 'viewList':
				$output .= $listHandler->ShowList(101, $listView);

				break;

			case 'saveNewListOrder':
				$listOrder = $URLQueryView->GetListOrder();
				$listId = $URLQueryView->GetListId();

				$listOrderSaved = $listHandler->SaveListOrder($user['userId'], $listOrder, $listId);

				// check if the list sorting is done
				$listIsDone = $listHandler->CheckListStatus($listId);
				
				$output .= $listHandler->ShowList($listId, $listView, true, $listIsDone);

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