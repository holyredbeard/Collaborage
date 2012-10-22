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

  	//Views
  	require_once ('View/RegisterView.php');
    require_once ('View/LoginView.php');
    require_once ('View/UserView.php');
    require_once ('View/URLQueryView.php');

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
            
            $registerHandler = new \Model\RegisterHandler($db);
            $loginHandler = new \Model\LoginHandler($db);
            $encryptionHandler = new \Model\EncryptionHandler();
            $userHandler = new \Model\UserHandler($db);

            $registerController = new \Controller\RegisterController();
            $loginController = new \Controller\LoginController();
            $userController = new \Controller\UserController();
            $listController = new \Controller\ListController();
            $headerController = new \Controller\HeaderController();

            if ($registerView->WantToRegister() || $registerView->TredToRegister()) {
                $body .= $registerController->DoControl($registerHandler, $registerView, $encryptionHandler, $loginHandler, $userHandler);
            }
            else {
                $body .= $loginController->DoControl($loginHandler, $loginView, $registerView, $encryptionHandler);
            }

            if ($loginHandler->IsLoggedIn()){

                $actionType = $URLQueryView->GetType();

                if ($actionType == 'list'){
                    $body .= $listController->DoControl($loginHandler, $db, $URLQueryView);
                }
                else if ($actiontype == 'admin') {
                    $body .= $userController->DoControl($userHandler, $userView);
                }

                // TODO: Ändra namn på filerna till AdminView etc...
            }

            //Close the database since it is no longer used
            $db->Close();

            $header = $headerController->DoControl();
            //$footer = new \Controller\FooterController();
            
            //Generate output
            $pageView = new \Common\PageView();
            return $pageView->GetHTMLPage("Title", $header, $body);
        }
    }

    echo MasterController::doControl();