<?php

namespace Model;

class RegisterHandler {

	private $m_db = null;

	public function __construct(Database $db) {
		$this->m_db = $db;
	}
	
	/**
	 * Körs för att registrera en användare
	 * @param String $username, användarnamn
	 * @param String $password, lösenord
	 * @return boolean
	 */
	public function DoRegister($username, $password){

		$query = "INSERT INTO user (username, password) VALUES (?, ?)";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("ss", $username, $password);
		
		$ret = $this->m_db->RunInsertQuery($stmt);

		$stmt->Close();

		return $ret;
	}


	/**
	 * Körs för att kontrollera att användarnamnet inte finns lagrat sedan tididare
	 * @param String $regUsername, användarnamn
	 * @param Array $users
	 * @return boolean
	 */
	public function CheckUnique($regUsername, Array $users){

		foreach ($users as $username) {
			if ($regUsername === $username){
				return false;
			}
		}

		return true;
	}

	/**
	 * Kedje-tester för applikationen
	 * @param Database $db
	 * @return boolean
	 */
	public static function Test(Database $db) {

		$registerHandler = new RegisterHandler($db);
		
		/**
		 * Test 1: Kolla så att det går att registrera sig.
		 */

		if($registerHandler->DoRegister('testuser06', 'testpass') == FALSE){
			echo "Test 1: DoRegister(), misslyckades (kunde inte registrera).";
			return false;
		}

		$query = "DELETE FROM users WHERE username ='testuser06'"; 
		$stmt = $db->Prepare($query);
		$db->DeleteUsers($stmt);
		

		/**
		 * Test 2: Kolla så att dubletter av användare hittas.
		 */
		
		$users = array('testuser01', 'testuser02', 'testuser03');

		if ($registerHandler->CheckUnique('testuser02', $users)){
			echo "Test 2: DoLogin(), misslyckades (det går att logga in med fel lösenord).";
			return false;
		}

		return true;
	}
}