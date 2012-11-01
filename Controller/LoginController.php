<?php

namespace Controller;

class LoginController {

    /**
     * Genererar XHTML (forumlär, felmeddelanden etc...)
     * 
     * @param $loginHandler, instance of LoginHandler()
     * @param $loginView, instance of LoginView()
     * @return String, XHTML
     */
	public function DoControl(\Model\LoginHandler $loginHandler,
                              \View\LoginView $loginView,
                              \View\RegisterView $registerView,
                              \Model\EncryptionHandler $encryptionHandler,
                              \Common\PageView $pageView) {

		$controlInfo = '';

        // Kontrollerar om användaren loggat in
    	if ($loginHandler->IsLoggedIn()){      

            // Kontrollerar om användaren försökt logga ut
    		if ($loginView->TriedToLogout()){

                // Kör DoLogout() för att logga ut användaren
    			$loginHandler->DoLogout($loginView);
    			
                $controlInfo = \View\LoginView::LOGGED_OUT;
    		}

            // Har användaren inte försökt logga ut visas logout-knappen
    		else {
    			$loginView->DoLogoutBox();
    		}
    	}

    	else {
            // Kontrollerar om användaren försökt logga in eller cookies existerar hos klienten.
            if ($loginView->TriedToLogin() || $loginView->CookieSet()){

                // Hämtar användarnamn och lösenord
    			$loginUsername = $loginView->GetUserName();
    			$loginPassword = $loginView->GetPassword();

                // Kontrollerar om cookies finns och om så inte är fallet krypteras lösenordet
                if ($loginView->CookieSet() == FALSE) {
                    $loginPassword = $encryptionHandler->Encrypt($loginPassword);
                }
                else {
                    $loginPassword = $encryptionHandler->Decrypt($loginPassword);
                }

                // Loggar in användaren (hur inloggningen gick returneras)
                $loginTry = $loginHandler->DoLogin($loginUsername, $loginPassword);

				if ($loginTry){
                    if ($loginView->RememberMe()){
                        
                        // Krypterar lösenordet och skapar cookies hos klienten.
                        $loginPassword = $encryptionHandler->Encrypt($loginPassword);
                        $loginView->CreateCookie($loginUsername, $loginPassword);

                    }
                    $controlInfo = \View\LoginView::LOGGED_IN;
				}
				else {
                    $controlInfo = \View\LoginView::WRONG_USERNAME_OR_PASSWORD;
				}
    		}
        }

        // Kontrollerar åter om användaren är inloggad, och om så är fallet läggs logout-knappen till $xhtml-variabeln.
        // I annat fall läggs login-formuläret till $xhtml-variabeln.
        if ($loginHandler->IsLoggedIn()){
            $title = 'You are logged in';
            $pageView->setTitle($title);

        	$xhtml = $loginView->DoLogoutBox();
        }
        else {
            $title = 'Collaborage!';
            $pageView->setTitle($title);

            $regButton = $registerView->DoRegisterButton();
        	$xhtml = $loginView->DologinBox($regButton);
        }

	return $controlInfo . $xhtml;

    }
}
?>