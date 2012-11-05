<?php

namespace View;

/**
 * A view that only generates output 
 * This view is/can be used from several controllers
 */
class UserView {

	// Variablar för formulär
	private $_checkBox = 'check[]';
	private $_check = 'check';
	private $_submitRemove = 'submitRemove';

	// Meddelanden
    const USER_REMOVED = "<p class='success'>User(s) successfully removed!</p>";
    const FAILED_TO_REMOVE_USER = "<p class='fail'>Failed to remove user(s)!</p>";


    /**
     * Funktion som genererar och returnerar formuläret för att visa och ta bort användare
     * @param Array $userArray
     * @return String
     */
	public function ShowUsers(Array $userArray) {

		$userIdArray = $userArray[0];
		$userNameArray = $userArray[1];
		$usersToInclude = implode(",", $userNameArray);

		$nrOfUsers = count($userIdArray);

		for ($i = 0; $i < $nrOfUsers; $i++) {
			$users .= "<label for='$userIdArray[$i]'>
						$userNameArray[$i]
						<input type='checkbox' user='$userNameArray[$i]' id='$this->_checkBox' name='$this->_checkBox' value='$userIdArray[$i]' /><br/>
						</label>";
		}
		
		$userList = "<div id='listContainer'>
						<h2>Admin area</h2>
						<div id='userList'>
							<form id='form3' method='post' action=''>
								<fieldset>
									<p><h3>Users</h3></p>
									<p>$users</p>
									<input type='submit' id='$this->_submitRemove' name='$this->_submitRemove' Value='Delete' />
								</fieldset>
							</form>
						</div>
					</div>";

		return $userList;
	}

	/**
    * Kontrollera om användaren klickat på submit-knappen
    * 
    * @return boolean
    */
	public function TriedToRemoveUser() {
		if (isset($_POST[$this->_submitRemove])) {
			return true;        
        }
        else {
        	return false;
        }

	}

	/**
    * Hämta och returnerar de användare användaren klickat i som ska tas bort
    * 
    * @return boolean
    */
	public function GetUsersToRemove() {
		if (isset($_POST[$this->_check])) {
            return $_POST[$this->_check];
        }
		else {
			return null;
		};
	}
}