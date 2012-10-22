<?php

namespace View;

session_start();

class ListView {

	private $m_newListName = 'newListName';
	private $m_newListCreationDate = 'newListCreationDate';
	private $m_newListExpireDate = 'newListExpireDate';
	private $m_newListIsPublic = 'newListIsPublic';
	private $m_newListCheckName = 'newListCheckName';
	private $m_newListUser = 'm_newListUser[]'; 
	private $m_newListUserCheck = 'm_newListUser'; 
	private $m_newListObject1 = 'm_newListObject1';
	private $m_newListObject2 = 'm_newListObject2';
	private $m_newListObject3 = 'm_newListObject3';
	private $m_newListObject4 = 'm_newListObject4';
	private $m_newListObject5 = 'm_newListObject5';

	private $m_newListSubmit = 'newListIsSubmit';

	public function ShowList($list) {
		
		$listId = $list['listId'];
		$listName = $list['listOptions']['listName'];
		$listCreator = $list['listOptions']['listCreator'];

		$creationDate = $list['listOptions']['creationDate'];

		if ($list['listOptions']['isPublic'] == true){
			$isPublic = "<p><strong>Public list: </strong>Yes</p>";
		}
		else {
			$isPublic = "<p><strong>Public list: </strong>No</p>";
		}

		if ($list['listOptions']['expireDate'] != NULL) {
			$expireDate = $list['listOptions']['expireDate'];

			$showExpireDate = "<p><strong>Expire date:</strong> $expireDate</p>";
		}

		function CreateListElements($list) {

			// Shuffles the list elements
			shuffle($list['listElements']);

			foreach ($list['listElements'] as $element) {

				$listElemId = $element['listElemId'];
				$listElemName = $element['listElemName'];
				$listElemDesc = $element['listElemDesc'];
				//$orderPlace = $element['listElemOrderPlace'];

				$showElements .= "<div id='listElement'>
									<strong>$listElemName (Id: $listElemId)</strong><br>
									$listElemDesc
								</div>";
			}

			return $showElements;
		}

		function CreateListUsers($list) {
			foreach ($list['listUsers'] as $user) {
				$userId = $user['userId'];
				$username = $user['username'];
				$isFinished = $user['isFinished'];

				if ($isFinished == 1) {
					$isFinished = 'Yes';
				} else {
					$isFinished = 'No';
				}

				$showUsers .= "<li><a href='index.php?showUser=$userId'>$username</a> | <strong>Done:</strong> $isFinished</strong></li>";
			}

			$showUsers .= "</ul>";

			return $showUsers;
		}

		$showUsers = CreateListUsers($list);
		$showElements = CreateListElements($list);

		// Create list
		$listHTML = "<div id='listContainer'>
						<h2>$listName</h2>
						<div id='listElements'>
							$showElements
						</div>
						<div id='listUsers'>
							<p><hr></p>
							<h3>Collaborators</h3>
							$showUsers
						</div>
						<div id='listInfo'>
							<h3>List info</h3>
							<strong>List creator:</strong> $listCreator<br/>
							<strong>Creation date:</strong> $creationDate<br/>
							$showExpireDate
							$isPublic
						</div> 
					 </div>";

		return $listHTML;
	}

	public function CreateListForm($users) {

		$i = 0;
		foreach ($users[0] as $key) {
			$userName = $users[1][$i];
			$userId = $users[1][$i];
			$generateUsers .= "$userName <input type='checkbox' name='$this->m_newListUser' value='$userId' /></br>";
			$i += 1;
		}

		$newListHTML = "<div id='newListContainer'>	
							<form id='newListForm' method='post'>
								<fieldset>
									<label for='$this->m_newListName'><input type='text' name='$this->m_newListName' value='List name'/></label>
									<label for='$this->m_newListExpireDate'><input type='text' name='m_newListExpireDate' value='Expire date'/></label>
									<label for='$this->m_newListIsPublic'>Public<input type='checkbox' checked='checked' id='$this->m_newListIsPublic' name='$this->m_newListIsPublic' Value='True' /></label>
									<h3>Add list objects</h3>
									<label for='$this->m_newListObject1'><input type='text' name='$this->m_newListObject1' value=''/></label>
									<label for='$this->m_newListObject2'><input type='text' name='$this->m_newListObject2' value=''/></label>
									<label for='$this->m_newListObject3'><input type='text' name='$this->m_newListObject3' value=''/></label>
									<label for='$this->m_newListObject4'><input type='text' name='$this->m_newListObject4' value=''/></label>
									<label for='$this->m_newListObject5'><input type='text' name='$this->m_newListObject5' value=''/></label>
								</fieldset>
								<fieldset id='addUsers'>
									<h3>Add users</h3>
									$generateUsers
								</fieldset>
								<input type='submit' name='$this->m_newListSubmit' value='Skapa lista'/>
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

	// TODO: Eventuellt en GET-funktion för varje fält... det är nog att föredra.
	public function GetNewList($loginHandler) {

		$user = $loginHandler->GetStoredUser();
		$userId = $user['userId'];

		$listName = 'listName';
		$expireDate = '2012-12-31';
		$checkedUsers = array();

		$date = getdate();
		$creationDate = $date['year'] . $date['mon'] . $date['mday'];

		if (isset($_POST[$this->m_newListName])) {
			$listName = $_POST[$this->m_newListName];
		}

		if (isset($_POST[$this->m_newListIsPublic])) {
			$isPublic = true;
		}
		else {
			$isPublic = false;
		}
		
		// TODO: Ändra kod för detta!!!!!!
		if (isset($_POST[$this->m_newListObject1])) {
			$listObjects[] = $_POST[$this->m_newListObject1];
		}
		if (isset($_POST[$this->m_newListObject2])) {
			$listObjects[] = $_POST[$this->m_newListObject2];
		}
		if (isset($_POST[$this->m_newListObject3])) {
			$listObjects[] = $_POST[$this->m_newListObject3];
		}
		if (isset($_POST[$this->m_newListObject4])) {
			$listObjects[] = $_POST[$this->m_newListObject4];
		}
		if (isset($_POST[$this->m_newListObject5])) {
			$listObjects[] = $_POST[$this->m_newListObject5];
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
						 'isPublic' => $isPublic,
						 'userId' => $userId);

		return $newList;
	}

	public function CreateUserList() {

		$userList = "<div id='newListContainer'>	
									<form id='newListUserForm' method='post'>
										<fieldset>
											<label for='$this->m_newListCheckName'>User 1<input type='checkbox' id='$this->m_newListIsPublic' name='$this->m_newListIsPublic' value='loggedIn' /></label>
											<label for='$this->m_newListCheckName'>User 2<input type='checkbox' id='$this->m_newListIsPublic' name='$this->m_newListIsPublic' value='loggedIn' /></label>
											<label for='$this->m_newListCheckName'>User 3<input type='checkbox' id='$this->m_newListIsPublic' name='$this->m_newListIsPublic' value='loggedIn' /></label>
											<label for='$this->m_newListCheckName'>User 4<input type='checkbox' id='$this->m_newListIsPublic' name='$this->m_newListIsPublic' value='loggedIn' /></label>
											<label for='$this->m_newListCheckName'>User 5<input type='checkbox' id='$this->m_newListIsPublic' name='$this->m_newListIsPublic' value='loggedIn' /></label>
											<label for='$this->m_newListCheckName'>User 6<input type='checkbox' id='$this->m_newListIsPublic' name='$this->m_newListIsPublic' value='loggedIn' /></label>
										</fieldset>
									</form>
								</div>";
	}
}