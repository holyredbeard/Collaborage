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


	public function __construct(Database $db) {
		$this->m_db = $db;
	}

	public function GetAllLists($listView) {

		$query = "SELECT * FROM $this->$m_tableList";

		$stmt = $this->m_db->Prepare($query);

		$lists = $this->m_db->GetLists($stmt);

		return $lists;
	}

	public function GetAssignedLists($userId) {

		$query = "SELECT l.listId, l.userId, l.listName, l.creationDate
					FROM $this->m_tableList AS l
	                INNER JOIN $this->m_tableListUser AS lu
	                ON l.listId = lu.listId
	                WHERE lu.userId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $userId);

		$assignedLists = $this->m_db->GetLists($stmt);

		return $assignedLists;
	}

	public function GetUsersLists($userId) {

		$query = "SELECT *
					FROM $this->m_tableList
					WHERE $this->m_columnUserId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $userId);

		$usersLists = $this->m_db->GetLists($stmt);

		return $usersLists;
	}

	public function GenerateListArray($user, $listName, $listObjectNames, $listObjectDescs, $userCheckBoxes) {

		$userId = $user['userId'];
		$checkedUsers = array();

		$date = getdate();
		$creationDate = $date['year'] . $date['mon'] . $date['mday'];

		$nrOf = count($listObjectNames);

		for($i = 0; $i < $nrOf; $i++) {
			$listObjectName = $listObjectNames[$i];

			$listObjectDesc = $listObjectDescs[$i];

			$listObjects[] = array('listObjectName' => $listObjectName,
								 'listObjectDesc' => $listObjectDesc);
		}

		foreach($userCheckBoxes as $user) {
        	$checkedUsers[] = $user;
    	}

		$newList = array('listName' => $listName,
						 'creationDate' => $creationDate,
						 'listObjects' => $listObjects,
						 'checkedUsers' => $checkedUsers,
						 'userId' => $userId);

		return $newList;
	}

	public function SaveNewList($list) {

		$query = "INSERT INTO $this->m_tableList ($this->m_columnUserId, $this->m_columnListName, $this->m_columnCreationDate)
					VALUES(?, ?, ?)";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("iss", $list['userId'],
								   $list['listName'],
								   $list['creationDate']);

		$listId = $this->m_db->CreateNewList($stmt);

		$objectsAdded = $this->InsertListObjects($listId, $list['listObjects']);

		$usersToBeAssigned = $list['checkedUsers'];
		array_push($usersToBeAssigned, $list['userId']);

		$this->InsertListUsers($listId, $usersToBeAssigned);

		$list['listId'] = $listId;

		return $list;
	}

	public function SaveListOrder($userId, $listOrder, $listId) {

		$listOrderArray = explode('.', $listOrder);
		$isFinished = 1;

		$i = 0;
		foreach ($listOrderArray as $listElem) {

			$query = "INSERT INTO $this->m_tableListElemOrder ($this->m_columnListId, $this->m_columnListElemId, $this->m_columnUserId, $this->m_columnListElemOrderPlace)
						VALUES(?, ?, ?, ?)";	

			$stmt = $this->m_db->Prepare($query);

			$listElemId = $listOrderArray[$i];
			$listElemOrderPlace = $i+1;

			$stmt->bind_param('iiii', $listId, $listElemId, $userId, $listElemOrderPlace);

			$ret = $this->m_db->RunInsertQuery($stmt);

			if ($ret == false) {
				return false;
			}

			$i += 1;
		}

		$query = "UPDATE $this->m_tableListUser SET $this->m_columnIsFinished = ?
				  WHERE $this->m_columnListId = ?
				  AND $this->m_columnUserId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('iii', $isFinished, $listId, $userId);

		// TODO: Fixa RunUpdateQuery??????
		$ret = $this->m_db->RunInsertQuery($stmt);

		return true;
	}

	public function InsertListObjects($listId, $listObjects) {

		foreach ($listObjects as $listObject) {

			$listObjectName = $listObject['listObjectName'];
			$listObjectDesc = $listObject['listObjectDesc'];

			$query = "INSERT INTO $this->m_tableListElement ($this->m_columnListElemName, $this->m_columnListId, $this->m_columnListElemDesc)
						VALUES(?, ?, ?)";

			$stmt = $this->m_db->Prepare($query);

			$stmt->bind_param('sis', $listObjectName, $listId, $listObjectDesc);

			$ret = $this->m_db->RunInsertQuery($stmt);

			if ($ret == false) {
				return false;
			}
		}
		return true;
	}

	public function InsertListUsers($listId, $checkedUsers) {

		foreach ($checkedUsers as $checkedUser) {
			$query = "INSERT INTO $this->m_tableListUser ($this->m_columnListId, $this->m_columnUserId)
						VALUES(?, ?)";

			$stmt = $this->m_db->Prepare($query);

			$stmt->bind_param('ii', $listId, $checkedUser);

			$this->m_db->RunInsertQuery($stmt);
		}
	}

	public function HasFinishedSorting($userId, $listId) {

		$query = "SELECT $this->m_columnIsFinished FROM $this->m_tableListUser
					WHERE $this->m_columnUserId = ?
					AND $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('ii', $userId, $listId);

		$result = $this->m_db->HasFinishedSorting($stmt);

		return $result;
	}

	public function AllHasSorted($listId) {

		$query = "SELECT $this->m_columnIsFinished
				  FROM $this->m_tableListUser
				  WHERE $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $listId);

		$result = $this->m_db->AllHasSorted($stmt);
		
		return $result;
	}

	public function CheckListStatus($listId) {

		$query = "SELECT $this->m_columnIsFinished FROM $this->m_tableListUser
					WHERE $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $listId);

		$listIsDone = $this->m_db->CheckListStatus($stmt);

		return $listIsDone;
	}

	public function ShowList($listId, $listView, $userIsFinished, $allHasSorted, $theUser) {

		$listOptions = $this->GetListOptions($listId);		// : Array

		if ($userIsFinished) {
			$listElements = $this->GetOrderedElements($theUser['userId'], $listId);		// : Array
		}
		else {
			$listElements = $this->GetListElements($listId);		// : Array
		}

		$listUsers = $this->GetListUsers($listId);			// : Var

		$list = array('listId' => $listId,
					  'listOptions' => $listOptions,
					  'listElements' => $listElements,
					  'listUsers' => $listUsers);
		
		$output = $listView->ShowList($list, $userIsFinished, $listIsDone, $theUser);

		return $output;
	}

	// KLAR!
	public function GetListOptions($listId) {
		
		$query = "SELECT l.listId, l.userId, l.listName, l.creationDate, u.username
					FROM $this->m_tableList AS l
					INNER JOIN $this->m_tableUser AS u
					ON l.userId = u.userId
					WHERE l.listId=?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $listId);
		

		$listOptions = $this->m_db->GetListOptions($stmt);

		return $listOptions;
	}

	// KLAR!
	public function GetListElements($listId) {
		
		$query = "SELECT $this->m_columnListElemId, $this->m_columnListElemName, $this->m_columnListElemDesc, $this->m_columnListElemOrderPlace
					FROM $this->m_tableListElement
					WHERE listId=?
					ORDER BY listElemOrderPlace";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listElements = $this->m_db->GetListElements($stmt);

		return $listElements;
	}

	public function GetOrderedElements($userId, $listId) {

		$query = "SELECT le.listElemId, le.listElemName, le.listElemDesc, lo.listElemOrderPlace
					FROM $this->m_tableListElement AS le
					INNER JOIN $this->m_tableListElemOrder AS lo
					USING ($this->m_columnListId, $this->m_columnListElemId)
					WHERE lo.userId = ?
					AND lo.listId = ?
					ORDER BY lo.listElemId";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("ii", $userId, $listId);
		
		$listElements = $this->m_db->GetOrderedElements($stmt);

		return $listElements;		
	}

	public function GetListUsersIds($listId) {

		$query = "SELECT $this->m_columnUserId
					FROM $this->m_tableListUser
					WHERE $this->m_columnListId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $listId);

		$listUsers = $this->m_db->GetListUsersIds($stmt);

		return $listUsers;
	}

	public function GetListOrders($listId, $listUsers) {

		foreach ($listUsers as $listUser) {

			$query = "SELECT $this->m_columnListElemId, $this->m_columnListElemOrderPlace
						FROM $this->m_tableListElemOrder
						WHERE $this->m_columnUserId = ?
						AND $this->m_columnListId = ?
						ORDER BY $this->m_columnnListElemId";

			$stmt = $this->m_db->Prepare($query);

			$stmt->bind_param('ii', $listUser, $listId);

			$listOrder = $this->m_db->GetListOrders($stmt);

			if ($listOrder != null) {
				$listOrders[] = $listOrder;
			}
		}

		return $listOrders;
	}

	public function CalculateOrder($listOrders) {

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

	public function AddListElemOrderPlaces($orderedList) {

		foreach ($orderedList as $listElem) {

			$query = "UPDATE $this->m_tableListElement SET $this->m_columnListElemOrderPlace = ?
				  WHERE $this->m_columnListElemId = ?";

			$stmt = $this->m_db->Prepare($query);

			$listElemOrderPlace = $listElem['listElemOrderPlace'];
			$listElemId = $listElem['listElemId'];

			$stmt->bind_param('ii', $listElemOrderPlace, $listElemId);

			$ret = $this->m_db->RunInsertQuery($stmt);
		}

		return $ret;
	}

	// KLAR!
	public function GetListUsers($listId) {

		$query = "SELECT lu.userId, u.username, lu.hasStarted, lu.isFinished
					FROM $this->m_tableListUser AS lu
					INNER JOIN $this->m_tableUser as u
					ON lu.userId = u.userId
					WHERE lu.listId=?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listUsers = $this->m_db->GetListUsers($stmt);

		return $listUsers;
	}

	//TODO: Implement test!
}