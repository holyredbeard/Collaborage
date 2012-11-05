<?php

namespace Model;

require_once ('View/ValidationView.php');

class ValidationHandler {

    private $errors = array();  // array som lagrar felmeddelanden

    // Konstanter för min-längder för användarnamn och lösenord
    const kMinPasswordLength = 6;
    const kMinUsernameLength = 5;

    // Konstanter för min-länger för listor
    const MinNrOfListUsers = 2;
    const MinNrOfListObjects = 2;

    /**
     * Funktion som tar emot användarnamn och lösenord och kontrollerar
     * dem mot samtliga valideraingsfunktioner.
     * 
     * @param String $regUsername
     * @param String $regPassword
     * @param String $regPassword2
     * @return boolean 
     */
    public function DoValidateRegistering($regUsername, $regPassword, $regPassword2) {
        $usernameCheck = $this->ValidateUsername($regUsername);

        $passwordCheck = $this->ValidatePassword($regPassword, $regPassword2);

        if (!$usernameCheck || !$passwordCheck) {
            return false;
        }
        else {
            return true;
        }
    }

    public function DoValidateList($listName, $listObjects, $userCheckBoxes) {
        $listNameCheck = $this->ValidateListName($listName);
        $listObjectsCheck = $this->ValidateListObjects($listObjects);
        $userCheckBoxesCheck = $this->ValidateListUsers($userCheckBoxes);

        echo "<p>$listNameCheck, $listObjectsCheck, $userCheckBoxesCheck</p>";

        if (!$listNameCheck || !$listObjectsCheck || !$userCheckBoxesCheck) {
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Funktion för att hämta och returnera felmeddelanden
     * @return Array
     */
    public function GetValidationError() {
        return $this->errors;
    }
    

    /**
     * Funktion för att validera användarnamnet
     * @param String $username, användarnamn
     * @return boolean
     */
    public function ValidateUsername($username) {
		if (!preg_match('/^[a-z0-9_-]*$/i', $username)) {
            $this->errors[] = \View\ValidationView::USERNAME_WRONG_FORMAT;
			return false;
		}
        else if (strlen($username) < self::kMinUsernameLength){
            $this->errors[] = \View\ValidationView::USERNAME_TOO_SHORT;
            return false;
        }
        return true;
    }


    /**
     * Funktion för att validera lösenordet
     * @param String $password, lösenord1
     * @param String $password2, lösenord2
     * @return boolean
     */
	public function ValidatePassword($password, $password2) {

        // Kontrollerar att båda lösenordsfälten innehåller samma lösenord
        if ($password != $password2) {
            $this->errors[] = \View\ValidationView::PASSWORD_DID_NOT_MATCH;
            return false;
        }
        // Kontrollerar att lösenordet endast innehåller tillåtna tecken
		else if (!preg_match('/^[a-z0-9_-]*$/i', $password)) {
            $this->errors[] = \View\ValidationView::PASSWORD_WRONG_FORMAT;
			return false;
        }
        // Kontrollerar att lösenordet har min antal tillåtna tecken
        else if (strlen($password) < self::kMinPasswordLength) {
            $this->errors[] = \View\ValidationView::PASSWORD_TOO_SHORT;
            return false;
        }
        return true;
    }

    // List valdiation
    // 

    const NEED_LIST_NAME = "<span class='errorMessage'>You need to give the list a name.</span>";
    const NEED_MORE_LISTOBJECTS = "<span class='errorMessage'>You need to add at least three list objects.</span>";
    const NEED_MORE_USERS = "<span class='errorMessage'>You need to assign at least two users.</span>";
    
    
    public function ValidateListName($listName) {
            if (($listName == '') || ($listName == null)) {
                $this->errors[] = \View\ValidationView::NEED_LIST_NAME;
                return false;
            }
            else {
                return true;
            }
    }

    public function ValidateListObjects($listObjects) {
        if (count($listObjects) < self::MinNrOfListObjects) {
                $this->errors[] = \View\ValidationView::NEED_MORE_LISTOBJECTS;
                return false;
            }
            else {
                return true;
        }
    }

    public function ValidateListUsers($listUsers) {
        if (count($listUsers) < self::MinNrOfListUsers) {
            $this->errors[] = \View\ValidationView::NEED_MORE_USERS;
            return false;
        }
        else {
            return true;
        }
    }


    /**
     * Kedje-tester för applikationen
     * @return boolean
     */
    public static function Test() {

        $validation = new ValidationHandler();

        /**
         * Test 1: Testa så att man inte kan registrera med ett användarnamn med otillåtna tecken (t ex åäö).
         */
        
        if ($validation->ValidateUsername('user')) {
            echo "Test 1: ValidateUsername(), misslyckades (det ska inte gå att registrera med ett användarnamn med otillåtna tecken).";
            return false;
        }


        /**
         * Test 2: Testa så att man inte kan gå vidare med registreringen med ett användarnamn som är kortare än 5 tecken.
         */
        
        if ($validation->ValidateUsername('user')) {
            echo "Test 2: ValidateUsername(), misslyckades (det ska inte gå att registrera med ett användarnamn kortare än 5 tecken).";
            return false;
        }


        /**
         * Test 3: Testa så att man inte kan gå vidare med registreringen om man anger två olika lösenord.
         */
        
        if ($validation->ValidatePassword('testtest', 'testtest2')) {
            echo "Test 4: ValidatePassword(), misslyckades (lösenorden matchade inte, men detta upptäcktes ej).";
            return false;
        }


        /**
         * Test 4: Testa så att man inte kan gå vidare med registreringen med ett lösenord med otillåtna tecken (t ex åäö).
         */
        
        if ($validation->ValidatePassword('abcåäö', 'abcåäö')) {
            echo "Test 4: ValidatePassword(), misslyckades (det ska inte gå att registrera med ett lösenord med otillåtna tecken).";
            return false;
        }


        /**
         * Test 5: Testa så att man inte kan gå vidare med registreringen med ett lösenord med otillåtna tecken (t ex åäö).
         */
        
        if ($validation->ValidatePassword('12345', '12345')) {
            echo "Test 5: ValidatePassword(), misslyckades (det ska inte gå att registrera med ett lösenord kortare än 6 tecken).";
            return false;
        }
        return true;
}
}