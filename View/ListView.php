<?php

namespace View;

session_start();

class ListView {

	private $m_newListName = 'newListName';
	private $m_newListCreationDate = 'newListCreationDate';
	private $m_newListExpireDate = 'newListExpireDate';
	private $m_newListCheckName = 'newListCheckName';
	private $m_newListUser = 'm_newListUser[]'; 
	private $m_newListUserCheck = 'm_newListUser'; 
	private $m_newListObject = 'newListObject[]';
	private $m_newListObjectDesc = 'm_newListObjectDesc';
	private $m_listObjectName = 'listObjectName';

	private $m_listObject = 'notSorted';
	private $m_listStatus = 'You haven\'t sorted the list yet!';

	private $m_newListSubmit = 'newListIsSubmit';
	private $m_newOrderListSubmit = 'newOrderListSubmit';

	public function ShowAllLists($assignedLists, $usersLists, $IsLoggedIn) {

		function GenerateListHTML($lists) {

			$j = 0;
			foreach ($lists as $key) {
				$listHTML .= "<a class='list' href='index.php?type=list&action=showList&listId=" . $lists[$j]['listId'] . "' />" . $lists[$j]['listName'] . "</a><br/>";

				$j += 1;
			}

			return $listHTML;
		}


		$listHTML = "<div id='listContainer'>";
		$listHTML .= "<h2>Lists</h2>";

		if ($IsLoggedIn) {
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

		echo $theUserName . '<<<<<<<<<<';
		echo $theUserId . '<<<<<<<<<<';
		
		$listId = $list['listId'];
		$listName = $list['listOptions']['listName'];
		$listCreator = $list['listOptions']['listCreator'];

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

		$userStatus = CheckStatus($isFinished);

		$showUsers .= "<ul><li class='userList'><strong><span class='userName'>$theUserName (you)</span></strong><span class='userStatus'>$userStatus</span></li>";

		foreach ($list['listUsers'] as $user) {
				$userId = $user['userId'];
				$username = $user['username'];
				$isFinished = $user['isFinished'];

				$userStatus = CheckStatus($isFinished);
				$showUsers .= "<li class='userList'><strong><span class='userName'>$username</span></strong><span class='userStatus'>$userStatus</span></li>";
			}

		$showUsers .= "</ul>";

		if (($userIsFinished) && ($allHasSorted == false)) {
			$this->m_listObject = 'doneSorted';
			$this->m_listStatus = 'You\'re done sorting, waiting for the others.';
		}
		else if ($allHasSorted) {
			$this->m_listObject = 'doneSorted';
			$this->m_listStatus = 'Everyone is done. The list is sorted!';
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
			// Create list
			$listHTML = "<div id='listContainer'>
							<h2>$listName</h2>
							<div id='listElements'>
								$showElements
							</div>
							<div id='listElements2'></div>
							<div id='listUsers'>
								<p><hr></p>
								<h3>Collaborators</h3>
								$showUsers
							</div>
							<div id='listInfo'>
								<h3 class='listInfo'>List info</h3>
								<strong>List status:</strong> $this->m_listStatus<br/>
								<strong>List creator:</strong> $listCreator<br/>
								<strong>Creation date:</strong> $creationDate<br/>
								$showExpireDate
							</div>
							<a class='buttonStyle' url='index.php?type=list&action=saveNewListOrder&listId=$listId' id='newOrder'>Save</a><br/>
						 </div>";
		}
		else {
			// Create list
			$listHTML = "<div id='listContainer'>
							<h2>$listName</h2>
							<div id='listSorted'>
								$showElements
							</div>
							<div id='listElements2'></div>
							<div id='listUsers'>
								<p><hr></p>
								<h3>Collaborators</h3>
								$showUsers
							</div>
							<div id='listInfo'>
								<h3 class='listInfo'>List info</h3>
								<strong>List status:</strong> $this->m_listStatus<br/>
								<strong>List creator:</strong> $listCreator<br/>
								<strong>Creation date:</strong> $creationDate<br/>
								$showExpireDate
							</div>
							<a href='' url='index.php?type=list&action=saveNewListOrder&listId=$listId' id='newOrder'>Save</a><br/>
						 </div>";
		}


		return $listHTML;
	}

	public function CreateListForm($users, $theUser) {

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
									<label for='$this->m_newListName'><input type='text' class='default tooltip' name='$this->m_newListName' title='Give your list an awesome name.' defaultValue='List name'/></label><br/>
									<label for='$this->m_newListExpireDate'><input type='text' class='default tooltip' id='datepicker' name='m_newListExpireDate' title='Choose a deadline for sorting the list' defaultValue='Expire date'/></label>
									<h3>Add list objects</h3>
									<label for='$this->m_listObjectName'><input type='text' class='default tooltip' id='$this->m_listObjectName' name='$this->m_newListObject' title='Type the name of the list object' defaultValue='List object name'/></label>
									<div id='newListObjectsDiv'>
										<label for='$this->m_newListObjectDesc'>
											<input type='text' class='default tooltip' id='$this->m_newListObjectDesc' name='$this->m_newListObjectDesc' title='Describe the list object so that other understand what it is' defaultValue='List object description'/>
										</label>
									</div>
									<button id='addListObjectSubmit'>+Add</button>
									<div id='listOfListObjects'>
									</div>
								</fieldset>
								<fieldset id='addUsers'>
									<h3>Add users</h3>
									$generateUsers
								</fieldset>
								<input type='submit' id='submit' name='$this->m_newListSubmit' value='Skapa lista'/>
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
			echo 'yes';
		}
		else {
			return false;
		}
	}

	// TODO: Eventuellt en GET-funktion för varje fält... det är nog att föredra.
	public function GetNewList($loginHandler, $user) {

		$listOk = true;

		$userId = $user['userId'];

		$listName = 'listName';
		$expireDate = '2012-12-31';
		$checkedUsers = array();

		$date = getdate();
		$creationDate = $date['year'] . $date['mon'] . $date['mday'];

		if (isset($_POST[$this->m_newListName])) {
			$listName = $_POST[$this->m_newListName];
		}
		else {
			$listOk = false;
		}
		
		// TODO: Ändra kod för detta!!!!!!
		if (isset($_POST['newListObject'])) {
			$listObjectNames = $_POST['newListObject'];
		}

		if (isset($_POST['newListObjectDesc'])) {
			$listObjectDescs = $_POST['newListObjectDesc'];
		}

		$nrOf = count($listObjectNames);

		for($i = 0; $i < $nrOf-1; $i++) {
			$listObjectName = $listObjectNames[$i+1];

			$listObjectDesc = $listObjectDescs[$i];

			$listObjects[] = array('listObjectName' => $listObjectName,
								 'listObjectDesc' => $listObjectDesc);
		}

		if (isset($_POST[$this->m_newListUserCheck])) {

			$userCheckBoxes = $_POST[$this->m_newListUserCheck];

			foreach($userCheckBoxes as $user) {
	        	$checkedUsers[] = $user;
	    	}
		}

		$newList = array('listName' => $listName,
						 'creationDate' => $creationDate,
						 'expireDate' => $expireDate,
						 'listObjects' => $listObjects,
						 'checkedUsers' => $checkedUsers,
						 'userId' => $userId);

		return $newList;
	}

	public function NewListSubmitted() {
	// TODO: $this->m_submitNewForm
		if (isset($_POST['submitButton'])) {
			return true;
		}
		return false;
	}

	public function GetNewListOrder() {
	// TODO: $this->m_formJson
		if(isset($_POST['json'])) {
			$json = $_POST['json']; // $json is a string
			$person = json_decode($json); // $person is an array with a key 'name'
		}
	}

	public function ShowNotLoggedIn() {

		$output = "<div id='notLoggedIn'>
					You need to log in to be able to create a new list!
					</div>";	

		return $output;
	}
}