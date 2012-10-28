<?php

namespace Model;

session_start();

class LoginHandler {

	// Privat variabel för hantering av session.
	private $m_checkLoginState = 'login_session';
	private $m_sessionCheck = "isLoggedIn";
	private $m_storedUser;

	private $m_db = null;

	public function __construct(Database $db) {
		$this->m_db = $db;
	}

	/**
	 * Kontrollera om användaren är inloggad
	 * @return boolean
	 */
	public function IsLoggedIn() {
		if($_SESSION[$this->m_checkLoginState] == $this->m_sessionCheck) {
			return true;
		}
		else {
			return false;
		}
	}

	public function GetStoredUser() {
		if(isset($_SESSION[$this->m_storedUser])) {
			return $_SESSION[$this->m_storedUser];
		}
		else {
			return null;
		}
		//return isset($_SESSION[$this->m_storedUser]) ? $_SESSION[$this->m_storedUser] : false;
	}

	/**
	 * Logga in användaren
	 * 
	 * @param String $username, användarnamnet
	 * @param String $password, lösenordet
	 * @return boolean
	 */
	public function DoLogin($username, $password){

		$query = "SELECT * FROM user WHERE username=? AND password=?"; 
		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("ss", $username, $password);
		
		$user = $this->m_db->CheckUser($stmt);
		
		$stmt->close();

		if ($user != null){
			$_SESSION[$this->m_checkLoginState] = $this->m_sessionCheck;
			$_SESSION[$this->m_storedUser] = $user;

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Logga ut användaren
	 * 
	 * @param Object $loginView instans av LoginView()
	 */
	public function DoLogout(\View\LoginView $loginView){
		if (isset($_SESSION[$this->m_checkLoginState])){
			unset($_SESSION[$this->m_checkLoginState]);

			// Kör funktionen DeleteCookie för att ta bort kakorna.
			$loginView->DeleteCookie();
		}
	}

	/**
	 * Kedje-tester för applikationen
	 * @param Database $db
	 * @return boolean
	 */
	public static function Test(Database $db) {

			$loginHandler = new LoginHandler($db);
			$loginView = new \View\LoginView();

			$loginHandler->DoLogout($loginView);	// loggar ut användaren som förberedelse för testerna.
				
			/**
			 * Test 1: Testa så att man inte är inloggad.
			 */
			 
			if($loginHandler->IsLoggedIn()){
				echo "LoginHandler - Test 1: DoLogut(), misslyckades (är inloggad).";
				return false;
			}
			

			/**
			 * Test 2: Testa så att det inte går att logga in med fel lösenord.
			 */
			
			if ($loginHandler->DoLogin("testuser01", "wrongPass")){
				echo "LoginHandler - Test 2: DoLogin(), misslyckades (det går att logga in med fel lösenord).";
				return false;
			}
			

			/**
			 * Test 3: Testa så att det går att logga in.
			 */
			
			if ($loginHandler->DoLogin("testuser01", "zLziXN9DAEAkT4A4TGGPRQdVqPVznsugBxquZCvz2ME=") == FALSE){
				echo "LoginHandler - Test 3: DoLogin(), misslyckades (det går inte att logga in med korrekta uppgifter).";
				return false;
			}


			/**
			 * Test 4: Testa så att man är inloggad.
			 */
			
			if ($loginHandler->IsLoggedIn() == FALSE){
				echo "LoginHandler - Test 4: IsLoggedIn(), misslyckades (var ej inloggad).";
				return false;
			}

			$loginHandler->DoLogout($loginView);	// loggar ut användaren igen


			/**
			 * Test 5: Testa så att man inte är inloggad.
			 */
			
			if ($loginHandler->IsLoggedIn()){
				echo "LoginHandler - Test 5: IsLoggedIn(), misslyckades (är fortfarande inloggad).";
				return false;
			}

			/**
			 * Test 6: Testa så att man inte kan logga in med fel användarnamn.
			 */
			
			if ($loginHandler->DoLogin("testuser01", "654321")){
				echo "LoginHandler - Test 6: DoLogin(), misslyckades (det gick att logga in med fel användarnamn).";
				return false;
			}

			return true;
		}
	}

?>