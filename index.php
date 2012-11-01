<?php
session_start();

    //require_once ('Include')

    // Models
    require_once ('Model/DBConfig.php');
    require_once ('Model/Database.php');
    require_once ('Model/RegisterHandler.php');
    require_once ('Model/LoginHandler.php');
    require_once ('Model/EncryptionHandler.php');
    require_once ('Model/UserHandler.php');
    require_once ('Model/ValidationHandler.php');

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
    require_once ('Common/PageHeader.php');
    require_once ('Common/PageFooter.php');

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

            $IsLoggedIn = $loginHandler->IsLoggedIn();

            if ($registerView->WantToRegister() || $registerView->TredToRegister()) {
                $body .= $registerController->DoControl($registerHandler, $registerView, $encryptionHandler, $loginHandler, $userHandler);
            }
            else {
                $body .= $loginController->DoControl($loginHandler, $loginView, $registerView, $encryptionHandler, $pageView);
            }

            $actionType = $URLQueryView->GetType();

            $user = $loginHandler->GetStoredUser();

            $isAdmin = $loginHandler->CheckIfAdmin($user['userId']);

            if ($actionType == 'list'){
                $body .= $listController->DoControl($loginHandler, $db, $URLQueryView, $IsLoggedIn, $pageView, $validation);
            }

            else if (($actionType == 'admin') && ($IsLoggedIn) && ($isAdmin)) {
                $body .= $userController->DoControl($userHandler, $userView, $pageView);
            }
            else {
                $body .= $mainView->ShowMainView();
            }

            // TODO: Ändra namn på filerna till AdminView etc...

            //Close the database since it is no longer used
            $db->Close();

            $header = $headerController->DoControl($loginHandler->IsLoggedIn(), $isAdmin);
            //$footer = new \Controller\FooterController();
            
            //Generate output
            return $pageView->GetHTMLPage($header, $body);
        }
    }

    echo MasterController::doControl();