<?php

namespace Model;

class EncryptionHandler {

	private $username;
	private $password;

	/**
	 * Krypterar lösenordet vilket returneras
	 * 
	 * @param String $toBeEncrypted, lösenordet (okrypterat)
	 * @return String
	 */
	public function Encrypt($toBeEncrypted) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$key = "The secret key is";
		$encrypedText = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $toBeEncrypted, MCRYPT_MODE_ECB, $iv));

		return $encrypedText;
	}

	/**
	 * Dekrypterar lösenordet vilket returneras
	 * 
	 * @param String $encrypedText, lösenordet (krypterat)
	 * @return String
	 */
	public function Decrypt($encrypedText) {
		$key = "The secret key is";
		$decryptedText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($encrypedText), MCRYPT_MODE_ECB);

		$trimmedString = rtrim($decryptedText);

		return $trimmedString;
	}

	/**
	 * Kedje-tester för applikationen
	 *
	 * @return boolean
	 */
	public static function Test(Database $db) {

		$encryptionHandler = new \Model\EncryptionHandler();

		/**
		 * Test 1: Testa så att det går att hämta array med användare.
		 */

		$password = 'testpass';
		$encryptedPass = $encryptionHandler->Encrypt($password);

		if ($password === $encryptedPass) {
			echo "Test 1: Encrypt(), misslyckades (lösenordet blev inte krypterat).";
			return false;
		}


		/**
		 * Test 2: Testa så att det går att ta bort flera användare.
		 */
		$newpass = $encryptionHandler->Decrypt($encryptedPass);

		if ($newpass != $password) {
			echo "Test 1: Decrypt(), misslyckades (lösenordet blev inte dekrypterat).";
			return false;
		}
		
		return true;
	}
}