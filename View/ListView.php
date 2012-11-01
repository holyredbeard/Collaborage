<?php

namespace View;

session_start();

class ListView {

	const LIST_IS_SORTED = 'Everyone is done. The list is sorted!';
	const USER_DONE_SORTING = 'You\'re done sorting, waiting for the others.';

	private $m_newListName = 'listName';
	private $m_newListCreationDate = 'newListCreationDate';
	private $m_newListExpireDate = 'newListExpireDate';
	private $m_newListCheckName = 'newListCheckName';
	private $m_newListUser = 'm_newListUser[]';
	private $m_newListUserCheck = 'm_newListUser'; 
	private $m_newListObject = 'newListObject[]';
	private $m_newListObjectName = 'newListObject';

	private $m_newListObjectDesc = 'newListObjectDesc';
	private $m_listObjectName = 'listObjectName';

	private $m_sortable = 'listElements';
	private $m_listObject = 'notSorted';
	private $m_listStatus = 'You haven\'t sorted the list yet!';
	private $m_listIsSorted = 'listIsSorted';
	private $m_userDoneSorting = 'userDoneSorting';
	private $m_listElementSorted ='listElements3';

	private $m_newListSubmit = 'newListIsSubmit';
	private $m_newOrderListSubmit = 'newOrderListSubmit';

	public function ShowAllLists($assignedLists, $usersLists, $IsLoggedIn) {

		$listHTML = "<div id='listContainer2'>";
		$listHTML .= "<h2>Lists</h2>";

		if ($IsLoggedIn) {

			function GenerateListHTML($lists) {

				$j = 0;
				foreach ($lists as $key) {
					$listHTML .= "<a class='list' href='index.php?type=list&action=showList&listId=" . $lists[$j]['listId'] . "' />" . $lists[$j]['listName'] . "</a><br/>";

					$j += 1;
				}

				return $listHTML;
			}

			$listHTML .= "<h3 class='usersLists'>Your lists</h3>";

			if ($usersLists != null) {
				$listHTML .= GenerateListHTML($usersLists);
			}
			else {
				$listHTML .= "<p>You haven't created any lists yet...</p>";
			}

			$listHTML .= "<h3 class='assigned'>Lists you're assigned to</h3>";
			
			if ($assignedLists != null) {
				$listHTML .= GenerateListHTML($assignedLists);
			}
			else {
				$listHTML .= "<p>You're not assigned to any lists...</p>";
			}
		}

		$listHTML .= "</div>";

		return $listHTML;
	}

	public function ShowList($list, $userIsFinished, $allHasSorted, $theUser) {

		$theUserId = $theUser['userId'];
		$theUserName = $theUser['username'];
		
		$listId = $list['listId'];
		$listName = $list['listOptions']['listName'];
		$listCreator = $list['listOptions']['listCreator'];
		$saveButton = '';

		if ($listCreator == $theUserName) {
			$listCreator = 'You';
		}
		else {
		}

		$creationDate = $list['listOptions']['creationDate'];

		if ($list['listOptions']['expireDate'] != NULL) {
			$expireDate = $list['listOptions']['expireDate'];

			$showExpireDate = "<p><strong>Deadline:</strong> $expireDate</p>";
		}

		function CheckStatus($isFinished) {
			if ($isFinished) {
				return 'done sorting';
			}
			else {
				return 'not sorted yet';
			}
		}

		$userStatus = CheckStatus($userIsFinished);

		$showUsers .= "<ul><li class='userList'><strong><span class='userName'>You</span></strong><span class='userStatus'>$userStatus</span></li>";

		foreach ($list['listUsers'] as $user) {
				$userId = $user['userId'];
				$username = $user['username'];
				$isFinished = $user['isFinished'];

				if ($theUserId == $userId) {
					unset($user['userId']);
					unset($user['userName']);
				}
				else {
					$userStatus = CheckStatus($isFinished);
					$showUsers .= "<li class='userList'><strong><span class='userName'>$username</span></strong><span class='userStatus'>$userStatus</span></li>";
				}
			}

		if ($allHasSorted) {
			$this->m_listObject = $this->m_listIsSorted;
			$this->m_sortable = $this->m_listElementSorted;
			$this->m_listStatus = self::LIST_IS_SORTED;
		}
		else if ($userIsFinished) {
			$this->m_listObject = $this->m_userDoneSorting;
			$this->m_sortable = $this->m_listElementSorted;
			$this->m_listStatus = self::USER_DONE_SORTING;
		}
		else {
			shuffle($list['listElements']);
		}

		foreach ($list['listElements'] as $element) {

			$listElemId = $element['listElemId'];
			$listElemName = $element['listElemName'];
			$listElemDesc = $element['listElemDesc'];

			$showElements .= "<ul>";
			$showElements .= "<li id='$listElemId' class='$this->m_listObject'><strong>$listElemName</strong><br>
									$listElemDesc</li>";
			$showElements .= "</ul>";
		}


		if ($userIsFinished == false) {
			$saveButton = "<a class='button' url='index.php?type=list&action=saveNewListOrder&listId=$listId' id='newOrder'>Save</a><br/>";

		}
			// Create list
		$listHTML = "<div id='listContainer'>
							<h2>$listName</h2>
							<div id='$this->m_sortable'>
								$showElements
							</div>
							<div id='listUsers'>
								<h3 class='collaborators'>Collaborators</h3>
								$showUsers
							</div>
							<div id='listInfo'>
								<h3 class='listInfo'>List info</h3>
								<strong>List status:</strong> $this->m_listStatus<br/>
								<strong>List creator:</strong> $listCreator<br/>
								<strong>Creation date:</strong> $creationDate<br/>
								$showExpireDate
							</div>
							$saveButton
						 </div>";

		return $listHTML;
	}

	public function CreateListForm($users, $theUser, $loginHandler, $theErrors) {

		if($theErrors != null) {
			foreach ($theErrors as $error) {
				$showErrors .= $error . '<br/>';
			}
		}

		$i = 0;
		foreach ($users[0] as $key) {
			if ($users[0][$i] == $theUser) {
				unset($users[0][$i]);
				unset($users[1][$i]);
			}
			else {
				$userId = $users[0][$i];
				$userName = $users[1][$i];
				$generateUsers .= "$userName <input type='checkbox' name='$this->m_newListUser' value='$userId' /></br>";
			}
			$i += 1;
		}

		$newListHTML = "<div id='newListContainer'>	
							<form id='newListForm' method='post'>
								<fieldset>
									<h3 class='listTitle'>Create new list</h3>
									<label for='$this->m_newListName'><input type='text' id='$this->m_newListName' val='' class='default tooltip' name='$this->m_newListName' title='Give your list an awesome name.' defaultValue='List name'/></label><br/>
									<h3>Add list objects</h3>
									<label for='$this->m_listObjectName'><input type='text' class='default tooltip' id='$this->m_listObjectName' title='Type the name of the list object' defaultValue='List object name'/></label>
									<div id='newListObjectsDiv'>
										<label for='$this->m_newListObjectDesc'>
											<input type='text' class='default tooltip' id='$this->m_newListObjectDesc' title='Describe the list object so that other understand what it is' defaultValue='List object description'/>
										</label>
									</div>
									<button id='addListObjectSubmit'>+Add</button>
									<div id='listOfListObjects'>
									</div>
								</fieldset>
								<div id='errorMessages'>
									$showErrors
								</div>
								<fieldset id='addUsers'>
									<h3>Add users</h3>
									$generateUsers
								</fieldset>
								<input type='submit' id='submit' name='$this->m_newListSubmit' value='Create list'/>
							</form>
						</div>";

		return $newListHTML;
	}

	public function WantToCreateList() {
		if (isset($_POST[$this->m_newListSubmit])) {
			return true;
		}
		else {
			return false;
		}
	}

	public function WantToSaveNewOrderedList() {
		if (isset($_POST[$this->m_newOrderListSubmit])) {
			return true;
		}
		else {
			return false;
		}
	}

	public function GetListName() {
		if (isset($_POST[$this->m_newListName])) {
			return $_POST[$this->m_newListName];
		}
	}

	public function GetListObjects() {
		if (isset($_POST[$this->m_newListObjectName])) {
			return $_POST[$this->m_newListObjectName];
		}
	}

	public function GetListObjectDescs() {
		if (isset($_POST[$this->m_newListObjectDesc])) {
			return $_POST[$this->m_newListObjectDesc];
		}
	}

	public function GetListUsers() {
		if (isset($_POST[$this->m_newListUserCheck])) {
			return $_POST[$this->m_newListUserCheck];
		}
	}

	/*public function NewListSubmitted() {
		if (isset($_POST['submitButton'])) {
			return true;
		}
		return false;
	}*/

	public function ShowNotLoggedIn() {

		$output = "<div id='notLoggedIn'>
					You need to log in to be able to create a new list!
					</div>";	

		return $output;
	}
}