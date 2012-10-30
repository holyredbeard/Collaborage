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

						if ($list != null) {
							$list = $listHandler->SaveNewList($list);

							$output = $listHandler->ShowList($list['listId'], $listView, false, false, $user);
						}
					}
					else {
						$users = $userHandler->GetAllUsers();
						$output .= $listView->CreateListForm($users, $user['userId'], $loginHandler);
					}
				}
				else {
					$output .= $listView->ShowNotLoggedIn();
				}

				break;

			case 'showLists':

				if ($IsLoggedIn) {
					$assignedLists = $listHandler->GetAssignedLists($user['userId']);
					$usersLists = $listHandler->GetUsersLists($user['userId']);

					$output .= $listView->ShowAllLists($assignedLists, $usersLists, $IsLoggedIn);
				}

				break;

			case 'showList':

				$listId = $URLQueryView->GetListId();

				// TODO: Här måste det kollas VARJE enskild användare om de är klara, inte bara denna.
				// Vidare om någon inte är klar visas den lista man senast skapade, låst.
				// Om möjlighet ska finnas ska det här gå att låsa upp om man vill ändra

				$userIsFinished = $listHandler->HasFinishedSorting($user['userId'], $listId);

				if ($userIsFinished) {
					$allHasSorted = $listHandler->AllHasSorted($listId);
				}

				// If not finished, let's sort the list!
				if ($allHasSorted == true) {
					
					$listUsers = $listHandler->GetListUsersIds($listId);

					$listOrders = $listHandler->GetListOrders($listId, $listUsers);

					$orderedList = $listHandler->CalculateOrder($listOrders);

					$wasAdded = $listHandler->AddListElemOrderPlaces($orderedList);
				}

				//echo $allHasSorted . '< is allHasSorted';

				// check if the list sorting is done
				//$listIsDone = $listHandler->CheckListStatus($listId);

				// Show the list!
				$output .= $listHandler->ShowList($listId, $listView, $userIsFinished, $allHasSorted, $user);

				break;

			case 'saveNewListOrder':
				$listOrder = $URLQueryView->GetListOrder();
				$listId = $URLQueryView->GetListId();

				$listOrderSaved = $listHandler->SaveListOrder($user['userId'], $listOrder, $listId);

				// check if the list sorting is done
				$allHasSorted = $listHandler->CheckListStatus($listId);
				
				$output .= $listHandler->ShowList($listId, $listView, true, $allHasSorted, $user);

				break;
		}

		return $output;
	}
}