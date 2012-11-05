<?php

namespace Controller;

require_once ('View/ListView.php');
require_once ('Model/ListHandler.php');
require_once('Common/PageView.php');

class ListController {
	
	public function DoControl(\Model\loginHandler $loginHandler,
							  \Model\Database $db,
							  \View\URLQueryView $URLQueryView,
							  $IsLoggedIn,
							  \Common\PageView $pageView,
							  $validation) {
		
		$listView = new \View\ListView();
		$listHandler = new \Model\ListHandler($db);
		$userHandler = new \Model\UserHandler($db);
		$loginHandler = new \Model\loginHandler($db);
		$validation = new \Model\ValidationHandler();

		// Hämtar array med användaren (id och användarnamn)
		$user = $loginHandler->GetStoredUser();

		// Hämtar information från URL:en om vad användaren valt att göra
		$action = $URLQueryView->GetAction();

		// Switch-sats som beroende på vad användaren valt att göra (vilket hämtas från URL:en) styr vad som ska göras/visas
		switch ($action) {

			// Om användaren valt att skapa en ny lista
			case 'newList':

				// Om användaren är inloggad körs nedan...
				if ($IsLoggedIn) {
					// Om användaren klickat på submit-knappen (och därmed valt att skapa lista) körs nedan...
					if ($listView->WantToCreateList()) {

						// Hämta datan från listan
						$listName = $listView->GetListName();
						$listObjects = $listView->GetListObjects();
						$listObjectDescs = $listView->GetListObjectDescs();
						$userCheckBoxes = $listView->GetListUsers();

						// Validerar datan...
						$checkValidation = $validation->DoValidateList($listName, $listObjects, $userCheckBoxes);

						// Om datan var okej körs nedan...
						if ($checkValidation) {

							// Sparar listan, vilket returnerar listIdt
							$listId = $listHandler->SaveNewList($user['userId'], $listName, $listObjects, $listObjectDescs, $userCheckBoxes);

							// Visar listan
							$output = $listHandler->ShowList($listId, $listView, false, false, $user, null);
						}
						//...vad inte valideringen okej hämtas valideringsfelen som visas för användaren
						else {
							$errors = $validation->GetValidationError();

							$users = $userHandler->GetAllUsers();
							$output .= $listView->CreateListForm($users, $user['userId'], $loginHandler, $errors);
						}
					}
					//...annars körs nedan som visar formuläret för att skapa en ny lista
					else {
						// Hämtar och sätter sidans titel
						$pageView->setTitle(\Common\PageView::TITLE_CREATE_NEW_LIST);

						$users = $userHandler->GetAllUsers();
						$output .= $listView->CreateListForm($users, $user['userId'], $loginHandler, null);
					}
				}
				//...annars visas information för användaren att man måste logga in
				else {
					// Hämtar och sätter sidans titel
					$pageView->setTitle(\Common\PageView::NOT_LOGGED_IN);

					$output .= $listView->ShowNotLoggedIn();
				}
				break;

			// Om användaren valt att visa lista med listor
			case 'showLists':

				// Om användaren är inloggad körs nedan...
				if ($IsLoggedIn) {
					// Array med listor som användaren är knuten till hämtas
					$assignedLists = $listHandler->GetAssignedLists($user['userId']);

					// Array med listor som användaren skapat hämtas
					$usersLists = $listHandler->GetUsersLists($user['userId']);

					// Array med listor som är sorterade hämtas
					$sortedLists = $listHandler->GetSortedLists();

					// Hämtar och sätter sidans titel
					$pageView->setTitle(\Common\PageView::VIEW_LIST);

					// Visar lista med de listor som är tillgängliga för användaren
					$output .= $listView->ShowAllLists($assignedLists, $usersLists, $IsLoggedIn, $sortedLists);
				}
				//...annars visas information för användaren att man måste logga in
				else {
					// Hämtar och sätter sidans titel
					$pageView->setTitle(\Common\PageView::NOT_LOGGED_IN);

					$output .= $listView->ShowNotLoggedIn();
				}
				break;

			// Om användaren valt att visa en lista			
			case 'showList':

				// Hämtar listId till den lista som ska visas från URL:en
				$listId = $URLQueryView->GetListId();

				// Kontrollerar om användaren har sorterat klart listan eller ej
				$userIsFinished = $listHandler->HasFinishedSorting($user['userId'], $listId);

				// Kontrollerar om listan är färdigsorterad
				$listIsSorted = $listHandler->CheckListStatus($listId);

				// Visar listan för användaren
				if ($listIsSorted == true) {
					// Hämtar och sätter sidans titel
					$pageView->setTitle(\Common\PageView::SHOW_ORDERED_LIST);
					$output .= $listHandler->ShowOrderedList($listHandler, $listView, $listId, $user['userId']);
				}
				else {
					// Hämtar och sätter sidans titel
					$pageView->setTitle(\Common\PageView::SHOW_LIST);
					$output .= $listHandler->ShowList($listId, $listView, $userIsFinished, false, $user);
				}
				break;

			// Om användaren valt att spara en nysorterad lista
			case 'saveNewListOrder':

				// Hämta listordningen från URL:en
				$listOrder = $URLQueryView->GetListOrder();

				// Hämtar listid för den lista som sorterats
				$listId = $URLQueryView->GetListId();

				// Kontrollerar om användaren har sorterat klart listan eller ej
				$userIsFinished = $listHandler->HasFinishedSorting($user['userId'], $listId);

				// Om användaren inte har sorterat tidigare sparas den nya sorteringen
				if ($userIsFinished == false) {
					// Hämtar och sätter sidans titel
					$pageView->setTitle(\Common\PageView::LIST_SAVED);
					$listOrderSaved = $listHandler->SaveListOrder($user['userId'], $listOrder, $listId);
				}

				// check if the list sorting is done
				$allHasSorted = $listHandler->AllHasSorted($listId);

				// If not finished, let's sort the list!
				if ($allHasSorted == true) {
					$pageView->setTitle(\Common\PageView::SHOW_ORDERED_LIST);				
					$output .= $listHandler->ShowOrderedList($listHandler, $listView, $listId, $user['userId']);
				}
				else {
					//$listId, $listView, $userIsFinished, $allHasSorted, $theUser
					$output .= $listHandler->ShowList($listId, $listView, true, false, $user);
				}
				break;
		}
		return $output;
	}
}