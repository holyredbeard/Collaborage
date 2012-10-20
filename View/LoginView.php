<?php

namespace View;

/**
 * A view that only generates output 
 * This view is/can be used from several controllers
 */
class LoginView {

    // Variablar för formulär.
	private $_loginUserName = 'username';
	private $_loginPassword = 'password';
    private $_rememberMe = 'rememberMe';
    private $_loginButton = 'loginButton';
    private $_logoutButton = 'logoutButton';

    // Variablar för cookies.
    private $_cookieUsername = 'cookieUsername';
    private $_cookiePassword = 'cookiePassword';

    // Variablar för meddelanden.
    const LOGGED_IN = "<p class='success'>You are logged in!</p>";
    const LOGGED_OUT = "<p class='succses'>You are logged out!</p>";
    const WRONG_USERNAME_OR_PASSWORD = "<p class='fail'>Wrong username and/or password!</p>";

    /**
     * Generera och returnera inloggnings-formulär
     * 
     * @return String,  HTML
     */
	public function DoLoginBox() {
  		return "<div id='loginForm'>
					<form id='form1' method='post' action=''>
						<fieldset>
							<label for='$this->_loginUserName'><br /><input type='text' value='your login name' class='loginLoginField' id='$this->_loginUserName' name='$this->_loginUserName' size='20' /></label><br/>
							<label for='$this->_loginPassword' ><br /><input type='password' class='loginPassField id='$this->_loginPassword' name='$this->_loginPassword' size='20' /></label><br/>
							<input type='submit' class='loginLoginButton' id='$this->_loginButton' name='$this->_loginButton' value='Login' />
							<label for='$this->_rememberMe' ><input type='checkbox' name='$this->_rememberMe' value='loggedIn' />Remember me:</label>
						</fieldset>
					</form>
				</div>";
	}

    /**
     * Generera och returnera utloggnings-knapp
     * 
     * @return String,  HTML
     */
	public function DoLogoutBox() {
		return "<div id='logoutButton'>
                    <form method='post' action=''>
    					<input type='submit' id='$this->_logoutButton' name='$this->_logoutButton' value='Logout' />
    				</form>
                </div>";
  	}

    /**
     * Kontrollerar om användaren klickat i "Remember me"-checkboxen
     * 
     * @return boolean
     */
    public function RememberMe() {
        if (isset($_POST[$this->_rememberMe])) {
            return true;
        }
        return false;
    }

    /**
     * Returnera användarnamn från cookie eller formulär
     * 
     * @return String,  HTML
     */
	public function GetUserName() {
		if (isset($_COOKIE[$this->_cookieUsername])) {
			return $_COOKIE[$this->_cookieUsername];
		}
        else if (isset($_POST[$this->_loginUserName])){
            return $_POST[$this->_loginUserName];
        }
		else {
			return null;
		};
    }
    
    /**
     * Returnera lösenord från cookie eller formulär
     * 
     * @return String,  HTML
     */
    public function GetPassword() {
    	if (isset($_COOKIE[$this->_cookiePassword])) {
    		return $_COOKIE[$this->_cookiePassword];
    	}
        else if (isset($_POST[$this->_loginPassword])){
            return $_POST[$this->_loginPassword];
        }
    	else {
    		return null;
    	};
    }

    /**
     * Returnera lösenord från cookie eller formulär
     * 
     * @return boolean
     */
    public function TriedToLogin() {
    	if (isset($_POST[$this->_loginButton])) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    /**
     * Kontrollera om användaren valt att logga ut
     * 
     * @return boolean
     */
    public function TriedToLogout() {
    	if (isset($_POST[$this->_logoutButton])) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    /**
     * Kontrollera om kakor finns
     * 
     * @return boolean
     */
    public function CookieSet(){
        if (isset($_COOKIE[$this->_cookieUsername]) && isset($_COOKIE[$this->_cookiePassword])){
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Skapar kakor innehållandes användarnamn och lösenord
     * 
     * @param String, $loginUsername, användarnamnet
     * @param String, $loginPassword, lösenordet
     */
    public function CreateCookie($loginUsername, $loginPassword) {
        $cookieExpires = time() + 3600 * 24 * 7; // 7 days

        setcookie($this->_cookieUsername, $loginUsername, $cookieExpires);
        setcookie($this->_cookiePassword, $loginPassword, $cookieExpires);
    }

    /**
     * Tar bort cookies
     */
    public function DeleteCookie() {
        setcookie($this->_cookieUsername, '', time() - 60);
        setcookie($this->_cookiePassword, '', time() - 60);
        unset($_COOKIE[$this->_cookieUsername]);
        unset($_COOKIE[$this->_cookiePassword]);
    }
}