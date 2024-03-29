<?php

namespace View;

class RegisterView {

	// Variablar för formulär
	private $_registerUserName = 'username';
	private $_registerPassword = 'password';
	private $_registerPassword2 = 'password2';
    private $_registerButton = 'registerButton';
    private $_registerCancel = 'registerCancel';
    private $_register = 'register';
    private $cssClassFailMessage = 'failMessage';
    private $cssClassFail = 'fail';
    private $cssClassSuccess = 'success';

    /*// Konstanter
    private $minUsernameLength = 5;
    private $minPasswordLength = 6;*/

    // Meddelanden
    const SUCCESSFULLY_REGISTERED = "<p class='success'>Registration successful!.</p><p>Log in with the username and password you registered with.</p>";
    const UNSUCCESSFULLY_REGISTERED = "<p class='fail'>Registration failed!</p>";

    // Felmeddlenaden
    const PASSWORD_DID_NOT_MATCH = "The passwords didn't match!";
    const USERNAME_WRONG_FORMAT = "Username contains illegal characters.";
    const USERNAME_TOO_SHORT = "Username is too short (minimum 5 characters).";
    const PASSWORD_WRONG_FORMAT = "Password is in wrong format.";
    const PASSWORD_TOO_SHORT = "Password is too short (minimum 6 characters).";
    const ALL_FIELDS_NOT_FILLED = "You must to fill in all fields.";
    const USERNAME_EXISTS = "Username already exists.";

    /**
     * Genererar och returnerar registrerings-formuläret
     *
     * @return String, HTML
     */
	public function DoRegisterBox() {
		return "<div id='registerForm'>
				<form id='form2' method='post' action=''>
					<fieldset>
						<label for='$this->_registerUserName'>Username:<br /><input type='text' class='tooltip' title='Choose a username with a minimum of 5 characters' id='$this->_registerUserName' name='$this->_registerUserName' size='20' /></label><br/>
						<label for='$this->_registerPassword' >Password:<br /><input type='password' class='tooltip' title='Choose a password with a minimum of 6 characters' id='$this->_registerPassword' name='$this->_registerPassword' size='20' /></label><br/>
						<label for='$this->_registerPassword2' >Repeat password:<br /><input type='password' class='tooltip' title='Repeat the password' id='$this->_registerPassword2' name='$this->_registerPassword2' size='20' /></label><br/>
						<input type='submit' id='$this->_registerButton' name='$this->_registerButton' value='Register' />
						<input type='submit' class='cancelButton' name='$this->_registerCancel' value='Cancel' /></label>
					</fieldset>
				</form>
			</div>";
	}

	/**
     * Genererar och returnerar registrerings-knappen
     *
     * @return String, HTML
     */
	public function DoRegisterButton() {
		return "<div id='registerButton'>
                    <span id='registerSpan'>Haven't got an account?</span>
                    <form method='post' action='index.php'>
					   <input class='regButton' type='submit' id='$this->_register' name='$this->_register' value='Sign up now!' />
			        </form>
                </div>";
	}

	/**
    * Kontrollerar om användaren klickat på registrerings-knappen
    * 
    * @return boolean
    */
    public function WantToRegister() {
    	if (isset($_POST[$this->_register])) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

	/**
    * Returnerar användarnamnet från formuläret
    * 
    * @return boolean
    */
   
	public function GetUsername() {
		if (isset($_POST[$this->_registerUserName])){
            return $_POST[$this->_registerUserName];
        }
		else {
			return null;
		};
	}

	/**
    * Returnerar lösenordet från forumläret
    * 
    * @return boolean
    */
   	public function GetPassword() {
		if (isset($_POST[$this->_registerPassword])){
            return $_POST[$this->_registerPassword];
        }
		else {
			return null;
		};
    }

	/**
    * Returnerar upprepat lösenordet från formuläret
    * 
    * @return boolean
    */
   
   	public function GetPassword2() {
   		if (isset($_POST[$this->_registerPassword2])){
            return $_POST[$this->_registerPassword2];
        }
		else {
			return null;
		};
   	}

    /**
    * Kontrollerar om användaren valt att skicka in registrerings-formuläret
    * 
    * @return boolean
    */
    public function TredToRegister() {
    	if (isset($_POST[$this->_registerButton])) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    /**
    * Sätter samman html-kod med felmeddelanden i registreringsformuläret.
    *
    * @param String: $errorMessages, String: $validationErrors
    * @return String
    */
    public function GetErrorMessages($errorMessages, $validationErrors){
        $errorMessages = array_merge((array)$errorMessages, (array)$validationErrors);

        foreach ($errorMessages as $error ){
            $showErrors.= "<p class='failMessage'>* $error</p>";
        }

        return $showErrors;
    }
}