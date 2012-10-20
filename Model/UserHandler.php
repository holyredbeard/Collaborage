<?php

namespace Model;

require_once "Model/RegisterHandler.php";

class UserHandler {

	private $m_db = null;

	public function __construct(Database $db) {
		$this->m_db = $db;
	}


	/**
	 * Funktion som körs för att hämta samtliga användare i databasen
	 * @return Array
	 */
	public function GetAllUsers() {
$listId = 1;
		// TODO: Implement database query!
		// Arrayen innehåller:
		// listId, userId, listName, creationDate, expireDate, isPublic
		// hämtar ut användarnamnet till listskaparen i en annan funktion.
		// 
		$query = 'SELECT * FROM Users';
		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listOptions = $this->m_db->GetUsers($stmt);

		/*$listOptions = array('listId' => 2,
							 'userId' => 3,
							 'listName' => 'FirstList',
							 'creationDate' => '2012-08-05',
							 'expireDate' =>
							 '2012-10-20',
							 'isPublic' => true);*/

		return $listOptions;
	}


	/**
	 * Funktion som körs för att ta bort en eller flera användare
	 * @param Array $userIds
	 * @return boolean
	 */
	public function RemoveUsers(Array $userIds) {

		// Kontrollerar att arrayen inte är tom
		if ($userIds != null) {

			$c = Array();	// array som lagrar '?'' till queryn
			$s = '';

			// Loopar antalet gånger som det finns ids och lägger till '?' till arrayen samt 's' till bind_param
			foreach ($userIds AS $u) {
				$c[] = "?";
				$s.= 's';
			}

			$inPart = "(" . implode(",", $c) . ")";				// skapar sträng av '?' till queryn
			$query = "DELETE FROM Users WHERE id IN $inPart";

			$stmt = $this->m_db->Prepare($query);

			array_unshift($userIds, $s);		// lägger s:en först i arrayen $userIds

			// Sätter samman bind_param-frasen med arrayen
			call_user_func_array(array($stmt, 'bind_param'), $this->makeValuesReferenced($userIds));

			$ret = $this->m_db->DeleteUsers($stmt);

			$stmt->Close();

			return $ret;
		}
		else {
			return false;
		}

	}

	/**
	 * Stödfunktion till RemoveUsers() för att göra array till referens
	 * @param  Array  $arr
	 * @return Array, som referens
	 */
	public function makeValuesReferenced(Array $arr) {
    		$refs = array();

    		foreach($arr as $key => $value) {
				$refs[$key] = &$arr[$key];
    		}

    		return $refs;
	}


	/**
	 * Kedje-tester för applikationen
	 * @param Database $db
	 * @return boolean
	 */
	public static function Test(Database $db) {

		$userHandler = new \Model\UserHandler($db);
		$registerHandler = new \Model\RegisterHandler($db);

		/**
		 * Test 1: Testa så att det går att hämta array med användare.
		 */
		$testArray = $userHandler->GetAllUsers();

		$checkArray = is_array($testArray);

		if ($checkArray == FALSE) {
			echo "Test 1: GetUsers(), misslyckades (det gick inte att hämta arrayen med användare).";
			return false;
		}


		/**
		 * Test 2: Testa så att det går att ta bort flera användare.
		 */

		$registerHandler->DoRegister('testuser26', 'testpass');
		$registerHandler->DoRegister('testuser25', 'testpass');

		$testArray = $userHandler->GetAllUsers();	// hämta alla användare igen
		$userIds = $testArray[0];					// tilldela variabeln $userIds arrayen med ids
		asort($userIds);							// sortera arrayen

		// Läggs till de två användarna som skapades ovan i arrayen $idsToRemove
		$idsToRemove = array();
		$idsToRemove[] = $userIds[count($userIds) - 1];
		$idsToRemove[] = $userIds[count($userIds) - 2];

		if ($userHandler->RemoveUsers($idsToRemove) == FALSE){
			echo "Test 2: RemoveUser(), misslyckades (det gick inte att ta bort användarna).";
			return false;
		}

		return true;
	}
}