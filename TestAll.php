<?php
session_start();

	
	require_once "Model/LoginHandler.php";
	require_once "Model/UserHandler.php";
	require_once "Model/RegisterHandler.php";
	require_once "Model/EncryptionHandler.php";
	require_once "Model/Validation.php";
	require_once "Model/Database.php";
	require_once "Model/DBConfig.php";
	require_once "View/LoginView.php";

	$xhtml = "<h1>Tester</h1>";

	class TestAll {

		/**
		 * Kör testerna
		 * 
		 * @return String, XHTML
		 */
		public static function RunTests(){

			/* --------------- Test av Database() ------------------- */

			$xhtml .= "<hr><h2>Test av klasser</h2>";

			if (\Model\Database::Test() == FALSE) {
				$xhtml .= "<br/>Testet av Database() misslyckades.<hr/>";
			}
			else {
				$xhtml .= "<br/>Testet av Database() lyckades.<hr/>";
			}


			/* --------------- Test av LoginHandler() ------------------- */

			$db = new \Model\Database();
            $db->Connect(new \Model\DBConfig());
			
			if (\Model\LoginHandler::Test($db) == FALSE) {
				$xhtml .= "<br/>Testet av LoginHandler() misslyckades.<hr/>";
			}
			else {
				$xhtml .= "<br/>Testet av LoginHandler() lyckades.<hr/>";
			}


			/* --------------- Test av Validation() ------------------- */

			if (\Model\Validation::Test() == FALSE) {
				$xhtml .= "<br/>Testet av Validation() misslyckades.<hr/>";
			}
			else {
				$xhtml .= "<br/>Testet av Validation() lyckades.<hr/>";
			}

			/* --------------- Test av RegisterHandler() ------------------- */

			$db = new \Model\Database();
            $db->Connect(new \Model\DBConfig());

			if (\Model\RegisterHandler::Test($db) == FALSE) {
				$xhtml .= "<br/>Testet av RegisterHandler() misslyckades.<hr/>";
			}
			else {
				$xhtml .= "<br/>Testet av RegisterHandler() lyckades.<hr/>";
			}

			/* --------------- Test av UserHandler() ------------------- */

			if (\Model\UserHandler::Test($db) == FALSE) {
				$xhtml .= "<br/>Testet av UserHandler() misslyckades.<hr/>";
			}
			else {
				$xhtml .= "<br/>Testet av UserHandler() lyckades.<hr/>";
			}

			/* --------------- Test av EncryptionHandler() ------------------- */

			if (\Model\EncryptionHandler::Test($db) == FALSE) {
				$xhtml .= "<br/>Testet av EncryptionHandler() misslyckades.<hr/>";
			}
			else {
				$xhtml .= "<br/>Testet av EncryptionHandler() lyckades.<hr/>";
			}

			return $xhtml;
		}
	}

	// Kör TestAll() för att köra testerna.
	$xhtml .= TestAll::RunTests();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    </head>
    <body class="">
        <p>
        <?php

        //Skriver ut innehållet i dokumentet.
        echo $xhtml;

        ?>
        </p>
    </body>
</html>