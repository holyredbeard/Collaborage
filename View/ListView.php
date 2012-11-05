<?php

namespace View;

session_start();

class ListView {

	// Meddelanden för list-status.
	const LIST_IS_SORTED = 'Everyone is done. The list is sorted!';
	const USER_DONE_SORTING = 'You\'re done sorting, waiting for the others.';

	// Privata variablar
	private $m_newListName = 'listName';
	private $m_newListCheckName = 'newListCheckName';
	private $m_newListUser = 'm_newListUser[]';
	private $m_newListUserCheck = 'm_newListUser';
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

	/**
    * Visar samtliga listor
    * @param Array: $assignedLists, Array: $usersLists, Bool: $isLoggedIn, Array: $sortedLists
    * @return String, $listHtml 
    */
	public function ShowAllLists($aLists, $uLists, $IsLoggedIn, $sLists) {

		// Hämtar arrayer med listor från listobjektet
		$assignedLists = $aLists->GetLists();
		$usersLists = $uLists->GetLists();
		$sortedLists = $sLists->GetLists();

		$listHTML = "<div id='listContainer2'>";
		$listHTML .= "<h2>Lists</h2>";

		// Om användaren är inloggad körs nedan
		if ($IsLoggedIn) {

			// Funktion som loopar igenom den medskickade listarreyen för att generera länkar för listorna
			function GenerateListHTML($lists) {
				$j = 0;

				foreach ($lists as $key) {
					$listHTML .= "<a class='list' href='index.php?type=list&action=showList&listId=" . $lists[$j]['listId'] . "' />" . $lists[$j]['listName'] . "</a><br/>";

					$j += 1;
				}

				return $listHTML;
			}
			$listHTML .= "<h3 class='usersLists'>Your lists</h3>";

			// Om användaren äger listor genereras länkar för dessa,
			// och i annat fall visas meddelande för användaren att så ej är fallet.
			if ($usersLists != null) {
				$listHTML .= GenerateListHTML($usersLists);
			}
			else {
				$listHTML .= "<p>You haven't created any lists yet...</p>";
			}

			$listHTML .= "<h3 class='assigned'>Lists you're assigned to</h3>";
			
			// Om det finns listor som användaren är knuten till genereras länkar för dessa,
			// och i annat fall visas meddelande för användaren att så ej är fallet.
			if ($assignedLists != null) {
				$listHTML .= GenerateListHTML($assignedLists);
			}
			else {
				$listHTML .= "<p>You're not assigned to any lists...</p>";
			}

			$listHTML .= "<h3 class='assigned'>Sorted lists</h3>";

			// Om det existerar sorterade listor genereras länkar för dessa,
			// och i annat fall visas meddelande för användaren att så ej är fallet.
			if ($sortedLists != null) {
				$listHTML .= GenerateListHTML($sortedLists);
			}
			else {
				$listHTML .= "<p>There are no sorted lists at the moment...</p>";
			}
		}

		$listHTML .= "</div>";

		return $listHTML;
	}

	/**
    * Visar en lista
    * @param Array: $list, Bool: $userIsFinished, Bool: $allHasSorted, Array: $theUserName
    * @return String, $listHtml 
    */
	public function ShowList($list, $userIsFinished, $allHasSorted, $theUser) {

		// Sätter variablar från de medskickade argumenten
		$theUserName = $theUser['username'];
		$listId = $list['listId'];
		$listName = $list['listOptions']['listName'];
		$listCreator = $list['listOptions']['listCreator'];
		$creationDate = $list['listOptions']['creationDate'];

		$saveButton = '';

		// Om användaren skapat listan visas 'You' istället för användarnamnet
		if ($listCreator == $theUserName) {
			$listCreator = 'You';
		}

		// Funktion som returnerar olika strängar beroende på om $isFinished är true eller false
		function CheckStatus($isFinished) {
			if ($isFinished) {
				return 'done sorting';
			}
			else {
				return 'not sorted yet';
			}
		}

		// Hämtar status genom att använda funktionen för detta
		$userStatus = CheckStatus($userIsFinished);

		// Skapar en html-lista med samtliga användare som är knutna till listan
		$showUsers .= "<ul><li class='userList'><strong><span class='userName'>You</span></strong><span class='userStatus'>$userStatus</span></li>";

		// Loopar igenom användare i listarrayen 
		foreach ($list['listUsers'] as $user) {
				$userId = $user['userId'];
				$username = $user['username'];
				$isFinished = $user['isFinished'];

				// Tar bort användarens id från listarrayen när denna dyker upp för att undvika dublett.
				// Annars hämtas status beroende på användarens status, som läggs till ett li-objekt
				if ($theUser['userId'] == $userId) {
					unset($user['userId']);
					unset($user['userName']);
				}
				else {
					$userStatus = CheckStatus($isFinished);
					$showUsers .= "<li class='userList'><strong><span class='userName'>$username</span></strong><span class='userStatus'>$userStatus</span></li>";
				}
			}

		// Beroende på om samtliga användare har sorterat eller om användaren har sorterat eller ej
		// sätts variablar som senare används för att visa listan på korrekt sätt (med klasser).
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
			// Har användaren inte sorterat listan blandas listobjekten
			shuffle($list['listElements']);
		}

		// Loopar igenom arrayen med listobjekten och skapar en html-lista med dem som sedan visas för användaren.
		foreach ($list['listElements'] as $element) {

			$elemImage = '';
			$listElemId = $element['listElemId'];
			$listElemName = $element['listElemName'];
			$listElemDesc = $element['listElemDesc'];
			$listElemenOrderPlace = $element['listElemOrderPlace'];

			// Om listan är sorterad visas bilder (stjärnor med guld, silver och brons) på de tre första listobjekten.
			if ($allHasSorted) {
				if ($listElemenOrderPlace == 1) {
				$elemImage = "<img class='listElemImg' src='http://cdn1.iconfinder.com/data/icons/august/PNG/Star%20Gold.png'/>";
				}
				else if ($listElemenOrderPlace == 2) {
					$elemImage = "<img class='listElemImg' src='http://cdn1.iconfinder.com/data/icons/august/PNG/Star%20Orange.png'/>";
				}
				else if ($listElemenOrderPlace == 3) {
					$elemImage = "<img class='listElemImg' src='http://cdn1.iconfinder.com/data/icons/august/PNG/Star%20Red.png'/>";
				}
			}

			$showElements .= "<ul>";
			$showElements .= "<li id='$listElemId' class='$this->m_listObject'><strong>$listElemName</strong><br/>
									$listElemDesc $elemImage</li>";
			$showElements .= "</ul>";
		}

		// Om användaren inte har sorterat klart listan visas knappen för att spara listan.
		if ($userIsFinished == false) {
			$saveButton = $saveButton = "<a class='button' url='index.php?type=list&action=saveNewListOrder&listId=$listId' id='newOrder'>Save</a><br/>";

		}
		
		// Skapar html för listan
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
							</div>
							$saveButton
						 </div>";

		return $listHTML;
	}

	/**
    * Visar forumlär för att skapa en lista
    * @param Array: $users, Array: $theUser, LoginHandler: $isLoggedIn, Array: $theErrors
    * @return String, html 
    */
	public function CreateListForm(Array $users, $theUser, \Model\LoginHandler $loginHandler, $theErrors) {

		// Kontrollerar om det finns valideringsfel och skapar i så fall html-text för felmeddelandena.
		if($theErrors != null) {
			foreach ($theErrors as $error) {
				$showErrors .= $error . '<br/>';
			}
		}

		// Itererar över arrayen med användare och skapar input-fält för var och en av dem.
		$i = 0;
		foreach ($users[0] as $key) {
			if ($users[0][$i] == $theUser) {
				unset($users[0][$i]);
				unset($users[1][$i]);
			}
			else {
				$userId = $users[0][$i];
				$userName = $users[1][$i];
				$generateUsers .= "$userName <input type='checkbox' name='$this->m_newListUser' value='$userId' /></br/>";
			}
			$i += 1;
		}

		// Skapar html-koden för listformuläret
		$newListHTML = "<div id='newListContainer'>	
							<form id='newListForm' method='post' action=''>
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

    /**
     * Returnera om användaren valt att skapa lista
     * 
     * @return boolean
     */
	public function WantToCreateList() {
		if (isset($_POST[$this->m_newListSubmit])) {
			return true;
		}
		else {
			return false;
		}
	}

    /**
     * Returnera om användaren valt spara sorterad lista
     * 
     * @return boolean
     */
	public function WantToSaveNewOrderedList() {
		if (isset($_POST[$this->m_newOrderListSubmit])) {
			return true;
		}
		else {
			return false;
		}
	}

    /**
     * Returnera listnamnet
     * 
     * @return String,  HTML
     */
	public function GetListName() {
		if (isset($_POST[$this->m_newListName])) {
			return $_POST[$this->m_newListName];
		}
	}

    /**
     * Returnera listobjekten
     * 
     * @return Array
     */
	public function GetListObjects() {
		if (isset($_POST[$this->m_newListObjectName])) {
			return $_POST[$this->m_newListObjectName];
		}
	}

    /**
     * Returnera listobjektens beskrivningar
     * 
     * @return Array
     */
	public function GetListObjectDescs() {
		if (isset($_POST[$this->m_newListObjectDesc])) {
			return $_POST[$this->m_newListObjectDesc];
		}
	}

    /**
     * Returnera om användaren valt att logga in
     * 
     * @return Array
     */
	public function GetListUsers() {
		if (isset($_POST[$this->m_newListUserCheck])) {
			return $_POST[$this->m_newListUserCheck];
		}
	}


    /**
     * Skapar och returnerar HTML i fall användaren in är inloggad
     * men vill skap en lista
     * 
     * @return String, HMTL
     */
	public function ShowNotLoggedIn() {

		$output = "<div id='notLoggedIn'>
					You need to log in to be able to create a new list!
					</div>";	

		return $output;
	}
}