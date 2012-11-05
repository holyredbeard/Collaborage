<?php

namespace Model;

class ListHandler {

	private $m_db = null;

	// Tabellnamn
	private $m_tableUser = 'user';
	private $m_tableListUser = 'listUser';
	private $m_tableList = 'list';
	private $m_tableListElement = 'listElement';
	private $m_tableListElemOrder = 'listElemOrder';

	// Kolumner
	private $m_columnListId = 'listId';
	private $m_columnUserId = 'userId';
	private $m_columnListName = 'listName';
	private $m_columnCreationDate = 'creationDate';
	private $m_columnListElemId = 'listElemId';
	private $m_columnListElemOrderPlace = 'listElemOrderPlace';
	private $m_columnIsFinished = 'isFinished';
	private $m_columnListElemName = 'listElemName';
	private $m_columnListElemDesc = 'listElemDesc';
	private $m_columnListIsDone = 'listIsDone';


	public function __construct(Database $db) {
		$this->m_db = $db;
	}

	/**
    * Hämtar samtliga listor från db
    * 
    * @param \View\ListView() 
    * @return \Model\StoreListHandler() lists
    */
	public function GetAllLists($listView) {

		// Skapar queryn
		$query = "SELECT * FROM list";

		$stmt = $this->m_db->Prepare($query);

		// Exekverar queryn, vilken returnerar en array med samtliga listor
		$allLists = $this->m_db->GetLists($stmt);

		return $allLists;
	}

	/**
    * Hämtar de listor från db som är knutna till användaren
    * 
    * @param Int $userId
    * @return \Model\StoreListHandler() lists
    */
	public function GetAssignedLists($userId) {

		// Skapar queryn
		$query = "SELECT l.listId, l.userId, l.listName, l.creationDate, l.listIsDone
					FROM $this->m_tableList AS l
	                INNER JOIN $this->m_tableListUser AS lu
	                ON l.listId = lu.listId
	                WHERE lu.userId = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param("i", $userId);

		// Exekverar queryn, vilken returnerar en array med listor
		$assignedLists = $this->m_db->GetLists($stmt);

		return $assignedLists;
	}

	/**
    * Hämtar de listor som användaren skapat
    * 
    * @param Int $userId
    * @return \Model\StoreListHandler() lists
    */
	public function GetUsersLists($userId) {

		// Skapar queryn
		$query = "SELECT *
					FROM $this->m_tableList
					WHERE $this->m_columnUserId = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param('i', $userId);

		// Exekverar queryn, vilken returnerar en array med de listor användaren skapat
		$usersLists = $this->m_db->GetLists($stmt);

		return $usersLists;
	}

	/**
    * Sparar en lista i databasen.
    * 
    * @param Int $userId, String $listName, Array $listObjects, Array $listObjectDescs
    * @return Int listid
    */
	public function SaveNewList($userId, $listName, $listObjects, $listObjectDescs, $userCheckBoxes) {

		// Hämtar dagens datum
		$date = getdate();

		// Lägger på en nolla till dag och/eller månad om endast en siffra
		if (($date['mon']) < 10) {
			$date['mon'] = '0' . $date['mon'];
		}
		if (($date['mday']) < 10) {
			$date['mday'] = '0' . $date['mday'];
		}

		// Lägger ihop år, månad och dag till ny variabel
		$creationDate = $date['year'] . $date['mon'] . $date['mday'];

		// Skapar queryn
		$query = "INSERT INTO $this->m_tableList ($this->m_columnUserId, $this->m_columnListName, $this->m_columnCreationDate)
					VALUES(?, ?, ?)";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametrarna
		$stmt->bind_param("iss", $userId, $listName, $creationDate);

		// Exekverar queryn och hämtar list-id:t som den sparade listan fått.
		$listId = $this->m_db->CreateNewList($stmt);

		// Kör funktion för att lägga till listobjekten
		$this->InsertListObjects($listId, $listObjects, $listObjectDescs);

		// Lägger till användarid't till arrayen med användare
		array_push($userCheckBoxes, $userId);

		// Kör funktion för att lägga till användarna
		$this->InsertListUsers($listId, $userCheckBoxes);

		return $listId;
	}

	/**
    * Sparar ner listobjekten till databasen
    * 
    * @param Int $listId, Array $listObjects, Array $listObjectDescs
    * @return boolean
    */
	public function InsertListObjects($listId, $listObjects, $listObjectDescs) {

		$i = 0;

		// Loopar igenom listobjekten och lägger till dem ett och ett i databasen
		foreach ($listObjects as $listObject) {

			// Använder iteratorn $i för att loopa igenom listObjectDesc (listobjekt-beskrivningarna)
			$listObjectDesc = $listObjectDescs[$i];

			// Skapar queryn
			$query = "INSERT INTO $this->m_tableListElement ($this->m_columnListElemName, $this->m_columnListId, $this->m_columnListElemDesc)
						VALUES(?, ?, ?)";

			$stmt = $this->m_db->Prepare($query);

			// Binder parametrarna
			$stmt->bind_param('sis', $listObject, $listId, $listObjectDesc);

			// Exekverar queryn
			$ret = $this->m_db->RunInsertQuery($stmt);

			if ($ret == false) {
				return false;
			}

			$i += 1;
		}
		return true;
	}

	/**
    * Sparar ner användarna som är knutna till listan i db
    * 
    * @param Int $listId, Array $checkedUsers
    */
	public function InsertListUsers($listId, $checkedUsers) {

		// Loopar igenom arrayen med användare och lägger till dem en och en i databasen
		foreach ($checkedUsers as $checkedUser) {

			// Skapar queryn
			$query = "INSERT INTO $this->m_tableListUser ($this->m_columnListId, $this->m_columnUserId)
						VALUES(?, ?)";

			$stmt = $this->m_db->Prepare($query);

			// Binder parametrarna
			$stmt->bind_param('ii', $listId, $checkedUser);

			// Exeverar queryn
			$this->m_db->RunInsertQuery($stmt);
		}
	}

	/**
    * Sparar ner en användares listordning
    * 
    * @param Int $userId, String $listOrder, Int $listId
    * @return
    */
	public function SaveListOrder($userId, $listOrder, $listId) {

		// Skapar en array av strängen med listordningen
		$listOrderArray = explode('.', $listOrder);
		$isFinished = 1;

		$i = 0;

		// Loopar igenom arrayen med de ordnade elementen
		foreach ($listOrderArray as $listElem) {

			//Query för att lägga till sparad ordning av listobjekten
			$query = "INSERT INTO $this->m_tableListElemOrder ($this->m_columnListId, $this->m_columnListElemId, $this->m_columnUserId, $this->m_columnListElemOrderPlace)
						VALUES(?, ?, ?, ?)";	

			$stmt = $this->m_db->Prepare($query);

			// Hämtar listelementets id från arrayen
			$listElemId = $listOrderArray[$i];

			// Använder iteratorn $i för att sätta listobjektets placering
			$listElemOrderPlace = $i+1;

			// Binder parametrarna
			$stmt->bind_param('iiii', $listId, $listElemId, $userId, $listElemOrderPlace);

			// Exekverar queryn
			$ret = $this->m_db->RunInsertQuery($stmt);

			if ($ret == false) {
				return false;
			}

			$i += 1;
		}

		// Query för att uppdatera fältet "isFinised" för att visa att användaren har sorterat
		$query = "UPDATE $this->m_tableListUser SET $this->m_columnIsFinished = ?
				  WHERE $this->m_columnListId = ?
				  AND $this->m_columnUserId = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametrarna
		$stmt->bind_param('iii', $isFinished, $listId, $userId);

		// Exekverar queryn
		$ret = $this->m_db->RunInsertQuery($stmt);

		return true;
	}

	/**
    * Kontrollerar om användaren har sorterat klart
    * 
    * @param Int $userId, Int $listId
    * @return boolean
    */
	public function HasFinishedSorting($userId, $listId) {

		// Skapar queryn
		$query = "SELECT $this->m_columnIsFinished FROM $this->m_tableListUser
					WHERE $this->m_columnUserId = ?
					AND $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder paramterarna
		$stmt->bind_param('ii', $userId, $listId);

		// Exekverar queryn
		$result = $this->m_db->HasFinishedSorting($stmt);

		return $result;
	}

	/**
    * Kontrollerar om samtliga användare är klara med sorteringen
    * 
    * @param Int $listId
    * @return boolean
    */
	public function AllHasSorted($listId) {

		// Skapar queryn
		$query = "SELECT $this->m_columnIsFinished
				  FROM $this->m_tableListUser
				  WHERE $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param('i', $listId);

		// Exekverar queryn och hämtar resultatet
		$result = $this->m_db->AllHasSorted($stmt);

		// Om true (alla användare har sorterat)
		if ($allHasSorted) {
			// Query för att uppdatera fältet "listIsDone" på listan.
			$query = "UPDATE $this->m_tableList
						SET $this->m_columnListIsDone = ?
						WHERE $this->m_columnListId = ?";

			$stmt = $this->m_db->Prepare($query);

			// Binder parametrarna
			$stmt->bind_param('ii', $result, $listId);

			// Exekverar queryn
			$ret = $this->m_db->RunInsertQuery($stmt);
		}
		
		return $result;
	}

	/**
    * Hämtar färdig-sorterade listor
    * 
    * @return Array $sortedLists
    */
	public function GetSortedLists() {

		$isFinished = 1;

		// Skapar queryn
		$query = "SELECT * FROM $this->m_tableList
					WHERE $this->m_columnListIsDone = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param('i', $isFinished);

		// Exekverar queryn och hämtar array med sorterade listor
		$sortedLists = $this->m_db->GetLists($stmt);

		return $sortedLists;
	}

	/**
    * Kontrollerar om listan är färdig-sorterad
    * 
    * @param Int $listId
    * @return boolean
    */
	public function CheckListStatus($listId) {

		// Skapar queryn
		$query = "SELECT $this->m_columnListIsDone FROM $this->m_tableList
					WHERE $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param('i', $listId);

		// Exekverar queryn som returnerar true eller false
		$listIsSorted = $this->m_db->CheckListStatus($stmt);

		return $listIsSorted;
	}

	/**
    * Visar en lista
    * 
    * @param Int $listId, \View\ListView() $listView, Boolean $userIsFinished, boolean $allHasSorted, Array $theUser
    * @return
    */
	public function ShowList($listId, $listView, $userIsFinished, $allHasSorted, $theUser) {

		// Kör funktion för att hämta listinformationen, vilken returnerar en array
		$listOptions = $this->GetListOptions($listId);

		if ($allHasSorted) {
			$listElements = $this->GetListElements($listId);
		}
		else if ($userIsFinished) {
			$listElements = $this->GetOrderedElements($theUser['userId'], $listId);

		}
		else {
			$listElements = $this->GetListElements($listId);
		}

		$listUsers = $this->GetListUsers($listId);

		// Skapar en array med hjälp av listans olika delar
		$list = array('listId' => $listId,
					  'listOptions' => $listOptions,
					  'listElements' => $listElements,
					  'listUsers' => $listUsers);
		
		// Kör Showlist för att visa listan
		$output = $listView->ShowList($list, $userIsFinished, $allHasSorted, $theUser);

		return $output;
	}

	/**
    * Hämtar en lista med dess information
    * 
    * @param Int $listId
    * @return Array $listOptions
    */
	public function GetListOptions($listId) {
		
		// Skapar queryn
		$query = "SELECT l.listId, l.userId, l.listName, l.creationDate, u.username
					FROM $this->m_tableList AS l
					INNER JOIN $this->m_tableUser AS u
					ON l.userId = u.userId
					WHERE l.listId=?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param('i', $listId);

		// Exekverar queryn, vilken returnerar en array med listinformationen
		$listOptions = $this->m_db->GetListOptions($stmt);

		return $listOptions;
	}

	/**
    * Hämtar alla listor som är ordnade från db
    * 
    * @param \Model\ListHandler() $listHandler, \View\ListView() $listView, Int $listId, Int $userId
    * @return String $output
    */
	public function ShowOrderedList($listHandler, $listView, $listId, $userId) {

		// Kör funktion för att hämta användarIds knutna till listan, vilka returneras
		$listUsers = $this->GetListUsersIds($listId);

		// Kör funktion för att hämta samtliga användares listordningar, vilka returneras
		$listOrders = $this->GetListOrders($listId, $listUsers);

		// Kör funktion för att räkna ut den genomsnittliga listordningen, vilket returneras
		$orderedList = $this->CalculateOrder($listOrders);

		// Lägger till de slutgiltiga placeringarna til listobjekten
		$this->AddListElemOrderPlaces($orderedList);
								 
		//$listId, $listView, $userIsFinished, $allHasSorted, $theUser
		$output .= $this->ShowList($listId, $listView, true, true, $userId);

		return $output;
	}

	/**
    * Hämtar alla användarIds knutna till en lista
    * 
    * @param Int $listId
    * @return Array $listUsers
    */
	public function GetListUsersIds($listId) {

		// Skapar queryn
		$query = "SELECT $this->m_columnUserId
					FROM $this->m_tableListUser
					WHERE $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param('i', $listId);

		// Exekverar queryn, vilken returnerar en array med användarids
		$listUsers = $this->m_db->GetListUsersIds($stmt);

		return $listUsers;
	}

	/**
    * Hämtar de olika användarnas listordningar för en lista
    * 
    * @param Int $listId, Array $listUsers
    * @return Array $listOrders
    */
	public function GetListOrders($listId, $listUsers) {

		// Loopar igenom arrayen med användare och hämtar ut varje enskild användares listordning
		foreach ($listUsers as $listUser) {

			// Skapar queryn
			$query = "SELECT $this->m_columnListElemId, $this->m_columnListElemOrderPlace
						FROM $this->m_tableListElemOrder
						WHERE $this->m_columnUserId = ?
						AND $this->m_columnListId = ?
						ORDER BY $this->m_columnListElemId";

			$stmt = $this->m_db->Prepare($query);

			// Binder parametrarna
			$stmt->bind_param('ii', $listUser, $listId);

			// Exekverar queryn, vilken returnerar en array med listordningar
			$listOrder = $this->m_db->GetListOrders($stmt);

			if ($listOrder != null) {
				$listOrders[] = $listOrder;
			}
		}

		return $listOrders;
	}

	/**
    * Räknar ut den genomsnittliga listordningen med hjälp av de olika användarnas listordningar
    * 
    * @param Array $listOrders
    * @return Array $orderedList
    */
	public function CalculateOrder($listOrders) {

		$orderPlaces = array();

		function subval_sort($a, $subkey) {
			foreach($a as $k=>$v) {
				$b[$k] = strtolower($v[$subkey]);
			}
			asort($b);
			foreach($b as $key=>$val) {
				$c[] = $a[$key];
			}
			return $c;
		}

		foreach ($listOrders as $listOrder) {
			$i = 0;
			foreach ($listOrder as $listElem) {
				$orderPlaces[$i]['listElemId'] = $listElem['listElemId'];
				$orderPlaces[$i]['listElemPoints'] += $listElem['listElemPoints'];
				$i += 1;
			}
		}

		$orderedList = subval_sort($orderPlaces, 'listElemPoints');

		for ($i = 0; $i < count($orderedList); $i++) {
			$orderedList[$i]['listElemOrderPlace'] = $i+1;
		}

		return $orderedList;
	}

	/**
    * Sätter de slutgiltiga placeringarna till listobjekten
    * 
    * @param Array @orderedList
    * @return boolean
    */
	public function AddListElemOrderPlaces($orderedList) {

		// Loopar igenom arrayen med listobjekt och lägger till placeringar på en och en i databasen
		foreach ($orderedList as $listElem) {

			$listElemOrderPlace = $listElem['listElemOrderPlace'];
			$listElemId = $listElem['listElemId'];

			// Skapar queryn
			$query = "UPDATE $this->m_tableListElement SET $this->m_columnListElemOrderPlace = ?
				  WHERE $this->m_columnListElemId = ?";

			$stmt = $this->m_db->Prepare($query);

			// Binder parametrarna
			$stmt->bind_param('ii', $listElemOrderPlace, $listElemId);

			// Exekverar queryn
			$ret = $this->m_db->RunInsertQuery($stmt);
		}

		return $ret;
	}

	/**
    * Hämtar en listas listobjekt
    * 
    * @param Int $listId
    * @return Array $listElements
    */
	public function GetListElements($listId) {
		
		// Skapar queryn
		$query = "SELECT $this->m_columnListElemId, $this->m_columnListElemName, $this->m_columnListElemDesc, $this->m_columnListElemOrderPlace
					FROM $this->m_tableListElement
					WHERE listId=?
					ORDER BY listElemOrderPlace";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listElements = $this->m_db->GetListElements($stmt);

		return $listElements;
	}

	/**
    * Hämtar en användares ordnade listobjekt från databasen
    * 
    * @param Int $userId, Int $listId
    * @return Array $listElements
    */
	public function GetOrderedElements($userId, $listId) {

		// Skapar queryn
		$query = "SELECT le.listElemId, le.listElemName, le.listElemDesc, lo.listElemOrderPlace
					FROM $this->m_tableListElement AS le
					INNER JOIN $this->m_tableListElemOrder AS lo
					USING ($this->m_columnListId, $this->m_columnListElemId)
					WHERE lo.userId = ?
					AND lo.listId = ?
					ORDER BY lo.listElemOrderPlace";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametrarna
		$stmt->bind_param("ii", $userId, $listId);
		
		// Exekverar queryn, vilket returnerar en array med listelementen.
		$listElements = $this->m_db->GetListElements($stmt);

		return $listElements;		
	}

	/**
    * Hämta användare som är knutna till en lista
    * 
    * @param Int $listId
    * @return Array $listUsers
    */
	public function GetListUsers($listId) {

		// Query för att hämta användare knutna till en lista
		$query = "SELECT lu.userId, u.username, lu.hasStarted, lu.isFinished
					FROM $this->m_tableListUser AS lu
					INNER JOIN $this->m_tableUser as u
					ON lu.userId = u.userId
					WHERE lu.listId=?";

		$stmt = $this->m_db->Prepare($query);

		// Binder parametern
		$stmt->bind_param("i", $listId);
		
		// Exekverar queryn, vilken returnerar en arrayen med listanvändare
		$listUsers = $this->m_db->GetListUsers($stmt);

		return $listUsers;
	}

	/**
	 * Kedje-tester för applikationen
	 * 
	 * @param Database $db
	 * @return boolean
	 */
	public static function Test(Database $db) {

		// GetListUsersIds($listId)
		// GetListOrders($listId, $listUsers)
		// CalculateOrder($listOrders)
		// AddListElemOrderPlaces($orderedList)
		// GetListElements($listId)
		// GetOrderedElements($userId, $listId)
		// GetListUsers($listId)

		$listHandler = new ListHandler($db);
		$loginView = new \View\LoginView();
		$listView = new \View\ListView();
			
		/**
		 * Test 1: Testar GetAllLists()
		 */
		 
		if ($listHandler->GetAllLists($listView) == null){
			echo "ListHandler - Test 1: GetAllLists(), misslyckades (returnerade null).";
			return false;
		}

		/**
		 * Test 2: Testar GetAssignedLists()
		 */
		
		if ($listHandler->GetAssignedLists(1000) != null){
			echo "ListHandler - Test 2: GetAssignedLists(), misslyckades (returnerade INTE null).";
			return false;
		}

		/**
		 * Test 3: Testar GetUsersLists()
		 */
		
		if ($listHandler->GetUsersLists(1000) == null){
			echo "ListHandler - Test 3: GetUsersLists(), misslyckades (returnerade INTE null).";
			return false;
		}

		/**
		 * Test 4: Testar att spara ny lista
		 */
		// Testar: SaveNewList(), InsertListUsers(), InsertListObjects()
		
		$userId = 100;
		$listName = 'Testlista';
		$listObjects = array('Test', 'Test2');
		$listObjectDescs = array('Abc', 'def');
		$userCheckBoxes = array(100, 200, 300);
		
		$listId = $listHandler->SaveNewList($userId, $listName, $listObjects, $listObjectDescs, $userCheckBoxes);
		
		if (is_numeric($listId) == false){
			echo "ListHandler - Test 4: SaveNewList(), misslyckades (gick ej att spara lista).";
			return false;
		}


		/**
		 * Test 5: Testar hämta listobjekt, GetListOptions()
		 */
		
		$listOptions = $listHandler->GetListOptions($listId);

		if ($listOptions == null){
			echo "ListHandler - Test 5: GetListOptions(), misslyckades (gick ej att hämta listobjet).";
			return false;
		}

		/**
		 * Test 6: Testa att spara listordning, SaveListOrder()
		 */
		
		$listOrder = shuffle($listOptions);

		$return = $listHandler->SaveListOrder($userId, $listOrder, $listId);
		
		if ($return == false){
			echo "ListHandler - Test 6: SaveListOrder(), misslyckades (det gick inte att spara listordningen).";
			return false;
		}

		/**
		 * Test 7: Testa att spara listordning, SaveListOrder()
		 */
		
		$finished = $listHandler->HasFinishedSorting($userId, $listId);
		
		if ($finished == false){
			echo "ListHandler - Test 7: HasFinishedSorting(), misslyckades (det var inte sparat att användaren sorterat listan).";
			return false;
		}

		/**
		 * Test 8: Testa att spara listordning, SaveListOrder()
		 */
		
		$userIsFinished = $listHandler->HasFinishedSorting($userId, $listId);
		
		if ($userIsFinished == false){
			echo "ListHandler - Test 8: HasFinishedSorting(), misslyckades (det var inte sparat att användaren sorterat listan).";
			return false;
		}

		/**
		 * Test 9: Testa att spara listordning, SaveListOrder()
		 */

		$listOrder = shuffle($listOptions);
		$listHandler->SaveListOrder(200, $listOrder, $listId);
		$listOrder = shuffle($listOptions);
		$listHandler->SaveListOrder(300, $listOrder, $listId);

		$allHasSorted = $listHandler->AllHasSorted($listId);

		if ($allHasSorted == false) {
			echo "ListHandler - Test 9: AllHasSorted(), misslyckades (det var inte sparat att samtliga användare sorterat listan).";
			return false;
		}

		/**
		 * Test 10: Testa att hämta sorterade listor, GetSortedLists()
		 */
		
		$sortedLists = $listHandler->GetSortedLists($userId, $listId);

		
		if ($sortedLists == null){
			echo "ListHandler - Test 10: GetSortedLists(), misslyckades (det fanns inga sorterade listor).";
			return false;
		}

		/**
		 * Test 11: Testa att kolla att lista är sparad som färdigsorterad, CheckListStatus()
		 */
		
		$allHasSorted = $listHandler->CheckListStatus($listId);
		
		if ($allHasSorted == null){
			echo "ListHandler - Test 11: CheckListStatus(), misslyckades (listan var inte färdigsorterad).";
			return false;
		}

		/**
		 * Test 12: Testa att skapa output i form av listan
		 */
		
		$output = $listHandler->ShowList($listId, $listView, $userIsFinished, $allHasSorted, $userId);
		
		if ($output == null){
			echo "ListHandler - Test 12: ShowList(), misslyckades (funktionen genererade null).";
			return false;
		}

		/**
		 * Test 13: Testar ShowOrderedList()
		 */
		
		$output = $listHandler->ShowOrderedList($listHandler, $listView, $listId, $userId);
		
		if ($output == null){
			echo "ListHandler - Test 13: ShowOrderedList(), misslyckades (funktionen genererade null).";
			return false;
		}

		return true;
	}
}