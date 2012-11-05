<?php

namespace Controller;

require_once ('Model/ValidationHandler.php');

class RegisterController {

	public function DoControl(\Model\RegisterHandler $registerHandler,
                              \View\RegisterView $registerView,
                              \Model\EncryptionHandler $encryptionHandler,
                              \Model\LoginHandler $loginHandler,
                              \Model\UserHandler $userHandler,
                              \Common\PageView $pageView) {

        $validation = new \Model\ValidationHandler();
        $loginView = new \View\loginView();
        $loginController = new \Controller\loginController();

		$xhtml = '';
        $errorMessages = array();   // array för att lagra felmeddelanden
        $okToRegister = true;       // håller reda på om validering är okej och det därmed är ok att registrera


        // Kontrollerar om användaren klickat på knappen Registrera
    	if ($registerView->WantToRegister()) {
    		$xhtml = $registerView->DoRegisterBox();
    	}

        // Kontrollerar om användaren skickat iväg registreringsformuläret
    	else if ($registerView->TredToRegister()) {
            $regUsername = $registerView->GetUsername();
            $regPassword = $registerView->GetPassword();
            $regPassword2 = $registerView->GetPassword2();

            // Kontrollerar att alla fält är ifyllda och avbryter registrering om så ej är fallet
            if ((!$regUsername) || (!$regPassword) || (!$regPassword2)){ 
                $errorMessages[] = \View\RegisterView::ALL_FIELDS_NOT_FILLED;
                $okToRegister = FALSE;
            }

            // Är allt okej kontrolleras att användarnamnet inte finns registrerat sedan tidigare och sedan valideras fälten.
            else {
                $usersArray = $userHandler->GetAllUsers();  // hämtar samtliga användare
                $users = $usersArray[1];                    // hämtar ut användarnamnen

                $checkUnique = $registerHandler->CheckUnique($regUsername, $users);

                // Är användarnamnet inte unikt avbryts registreringen
                if (!$checkUnique) {
                    $errorMessages[] = \View\RegisterView::USERNAME_EXISTS;
                    $okToRegister = FALSE;
                }

                // Validerar användarnamn och lösenord
                $validation = new \Model\ValidationHandler();
                $validate = $validation->DoValidateRegistering($regUsername, $regPassword, $regPassword2);

                // Gick valideringen inte igenom avbryts registreringen
                if (!$validate) {
                    $validationErrors = $validation->GetValidationError();
                    $okToRegister = FALSE;
                }
            }

            // Har validering gått igenom görs ett försök att registrera användaren efter att lösenord krypterats
            if ($okToRegister) {
                $encryptedPass = $encryptionHandler->Encrypt($regPassword);
                $regTry = $registerHandler->DoRegister($regUsername, $encryptedPass);

                // Visar meddelande för användare beroende på hur registrering gick
                if ($regTry){
                    $xhtml = \View\RegisterView::SUCCESSFULLY_REGISTERED;
                    $xhtml.= $loginController->DoControl($loginHandler, $loginView, $registerView, $encryptionHandler, $pageView);
                }
                else {
                    $xhtml = \View\RegisterView::UNSUCCESSFULLY_REGISTERED;
                    $xhtml.= $registerView->DoRegisterBox();
                }
            }

            // Gick valideringen inte igenom hämtas felmeddelanden som visas för användaren.
            else {
                $showErrors = $registerView->GetErrorMessages($errorMessages, $validationErrors);

                $xhtml = \View\RegisterView::UNSUCCESSFULLY_REGISTERED;
                $xhtml.= $showErrors;
                $xhtml.= $registerView->DoRegisterBox();
            }
    	}
    	return $xhtml;
	}
}