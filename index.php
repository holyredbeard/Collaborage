<?php
session_start();

    // Models
    require_once ('Model/DBConfig.php');
    require_once ('Model/Database.php');
    require_once ('Model/RegisterHandler.php');
    require_once ('Model/LoginHandler.php');
    require_once ('Model/EncryptionHandler.php');
    require_once ('Model/UserHandler.php');
    require_once ('Model/ValidationHandler.php');
    require_once ('Model/StoreListHandler.php');

  	//Views
  	require_once ('View/RegisterView.php');
    require_once ('View/LoginView.php');
    require_once ('View/UserView.php');
    require_once ('View/URLQueryView.php');
    require_once ('View/MainView.php');

    // Controllers
    require_once ('Controller/RegisterController.php');
    require_once ('Controller/LoginController.php');
    require_once ('Controller/UserController.php');
    require_once ('Controller/ListController.php');
    require_once ('Controller/HeaderController.php');
    //require_once ('Controller/FooterController.php');

    // Common
    require_once ('Common/PageView.php');

    $title = "Login form";
    $body = "";

    class MasterController{

        public static function DoControl(){

            $reqAction = null;

            $db = new \Model\Database();
            $db->Connect(new \Model\DBConfig());

            // Initiate objects
            $registerView = new \View\RegisterView();
            $loginView = new \View\LoginView();
            $userView = new \View\UserView();
            $URLQueryView = new \View\URLQueryView();
            $mainView = new \View\MainView();
            
            $registerHandler = new \Model\RegisterHandler($db);
            $loginHandler = new \Model\LoginHandler($db);
            $encryptionHandler = new \Model\EncryptionHandler();
            $userHandler = new \Model\UserHandler($db);

            $registerController = new \Controller\RegisterController();
            $loginController = new \Controller\LoginController();
            $userController = new \Controller\UserController();
            $listController = new \Controller\ListController();
            $headerController = new \Controller\HeaderController();

            $pageView = new \Common\PageView();

            // Om användaren valt att registrera sig eller försökt att registrera sig (klickat på submit i reg-formuläret)
            // körs registreringskontrollern...
            if ($registerView->WantToRegister() || $registerView->TredToRegister()) {
                $regBox .= $registerController->DoControl($registerHandler, $registerView, $encryptionHandler, $loginHandler, $userHandler, $pageView);
            }
            //...annars körs loginkontrollern.
            else {
                $body .= $loginController->DoControl($loginHandler, $loginView, $registerView, $encryptionHandler, $pageView);
            }

            // Kör IsLoggedIn() som returnerar om användaren är inloggad eller inte
            $IsLoggedIn = $loginHandler->IsLoggedIn();

            // Hämtar vad användaren valt att göra från URL:en (antingen 'list' eler 'admin')
            $actionType = $URLQueryView->GetType();

            // Hämtar array med användaruppgifter (id och användarnamn)
            $user = $loginHandler->GetStoredUser();

            // Kollar om användaren har adminbehörighet
            $isAdmin = $loginHandler->CheckIfAdmin($user['userId']);

            // Om actiontype är 'list' körs nedan...
            if ($actionType == 'list'){
                $body .= $listController->DoControl($loginHandler, $db, $URLQueryView, $IsLoggedIn, $pageView, $validation);
            }
            //... är actiontype 'admin' körs nedan...
            else if (($actionType == 'admin') && ($IsLoggedIn) && ($isAdmin)) {
                $body .= $userController->DoControl($userHandler, $userView, $pageView);
            }
            //... annars körs nedan
            else {
                // Är användaren inloggad visas förstasidan i inloggad vy
                if ($IsLoggedIn) {
                    $body .= $mainView->ShowMainLoggedIn($user['username']);
                }
                else if ($registerView->WantToRegister() || $registerView->TredToRegister()) {
                    $body = $mainView->ShowRegisterView($regBox);
                }
                // annars visas utloggad vy
                else {
                    $body .= $mainView->ShowMainNotLoggedIn();
                }
            }

            //Close the database since it is no longer used
            $db->Close();

            $header = $headerController->DoControl($loginHandler->IsLoggedIn(), $isAdmin);
            //$footer = new \Controller\FooterController();
            
            //Generate output
            return $pageView->GetHTMLPage($header, $body);
        }
    }

    echo MasterController::doControl();