<?php

namespace Controller;

require_once ('View/ListView.php');
require_once ('Model/ListHandler.php');
require_once('Common/PageView.php');

class ListController {
	
	public function DoControl($loginHandler, $db, $URLQueryView, $IsLoggedIn, $pageView, $validation) {
		$listView = new \View\ListView();
		$listHandler = new \Model\ListHandler($db);
		$userHandler = new \Model\UserHandler($db);
		$loginHandler = new \Model\loginHandler($db);
		$validation = new \Model\ValidationHandler();

		$user = $loginHandler->GetStoredUser();
		$action = $URLQueryView->GetAction();

		switch ($action) {
			case 'newList':
				if ($IsLoggedIn) {
					if ($listView->WantToCreateList()) {
						$listName = $listView->GetListName();
						$listObjects = $listView->GetListObjects();
						$listObjectDescs = $listView->GetListObjectDescs();
						$userCheckBoxes = $listView->GetListUsers();
						$checkValidation = $validation->DoValidateList($listName, $listObjects, /*$listObjectDescs, */$userCheckBoxes);

						if ($checkValidation) {
							$list = $listHandler->GenerateListArray($user, $listName, $listObjects, $listObjectDescs, $userCheckBoxes);

							if ($list != null) {
								$list = $listHandler->SaveNewList($list);
								
								//$listId, $listView, $userIsFinished, $allHasSorted, $theUser
								$output = $listHandler->ShowList($list['listId'], $listView, false, false, $user, null);
							}
						}
						else {
							$errors = $validation->GetValidationError();

							$users = $userHandler->GetAllUsers();
							$output .= $listView->CreateListForm($users, $user['userId'], $loginHandler, $errors);
						}
					}
					else {
						$pageView->setTitle(\Common\PageView::TITLE_CREATE_NEW_LIST);

						$users = $userHandler->GetAllUsers();
						$output .= $listView->CreateListForm($users, $user['userId'], $loginHandler, null);
					}
				}
				else {
					$pageView->setTitle(\Common\PageView::NOT_LOGGED_IN);

					$output .= $listView->ShowNotLoggedIn();
				}

				break;

			case 'showLists':

				if ($IsLoggedIn) {
					$assignedLists = $listHandler->GetAssignedLists($user['userId']);
					$usersLists = $listHandler->GetUsersLists($user['userId']);

					$pageView->setTitle(\Common\PageView::VIEW_LIST);

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
					$pageView->setTitle(\Common\PageView::SHOW_ORDERED_LIST);
					$output .= $this->ShowOrderedList($listHandler, $listView, $listId);
				}
				else {
					$pageView->setTitle(\Common\PageView::SHOW_LIST);
					$output .= $listHandler->ShowList($listId, $listView, $userIsFinished, false, $user);
				}

				break;

			case 'saveNewListOrder':
				$listOrder = $URLQueryView->GetListOrder();
				$listId = $URLQueryView->GetListId();

				$userIsFinished = $listHandler->HasFinishedSorting($user['userId'], $listId);

				if ($userIsFinished == false) {
					$listOrderSaved = $listHandler->SaveListOrder($user['userId'], $listOrder, $listId);
					
					$pageView->setTitle(\Common\PageView::LIST_SAVED);

				}

				// check if the list sorting is done
				$allHasSorted = $listHandler->AllHasSorted($listId);

				// If not finished, let's sort the list!
				if ($allHasSorted == true) {
					$pageView->setTitle(\Common\PageView::SHOW_ORDERED_LIST);				
					$output .= $this->ShowOrderedList($listHandler, $listView, $listId);
				}

				//$listId, $listView, $userIsFinished, $allHasSorted, $theUser
				$output .= $listHandler->ShowList($listId, $listView, true, false, $user);

				break;
		}

		return $output;
	}

	public function ShowOrderedList($listHandler, $listView, $listId) {				
			$listUsers = $listHandler->GetListUsersIds($listId);
			$listOrders = $listHandler->GetListOrders($listId, $listUsers);

			$orderedList = $listHandler->CalculateOrder($listOrders);

			// TODO: Fixa så att de blir lika om samma poäng!
			$wasAdded = $listHandler->AddListElemOrderPlaces($orderedList);

			//$listId, $listView, $userIsFinished, $allHasSorted, $theUser
			$output .= $listHandler->ShowList($listId, $listView, true, true, $user);

			return $output;
		}
}