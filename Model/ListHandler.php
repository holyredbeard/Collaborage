<?php

namespace Model;

class ListHandler {

	private $m_db = null;

	public function __construct(Database $db) {
		$this->m_db = $db;
	}

	public function GetAllLists($listView) {

		$query = "SELECT * FROM list";

		$stmt = $this->m_db->Prepare($query);

		$lists = $this->m_db->GetLists($stmt);

		return $lists;
	}

	public function GetAllPublicLists() {

		$query = "SELECT * FROM list
					WHERE isPublic=1";

		$stmt = $this->m_db->Prepare($query);

		$publicLists = $this->m_db->GetAllPublicLists($stmt);

		return $publicLists;
	}
	

	public function GetAssignedLists($userId) {

		$query = "SELECT l.listId, l.userId, l.listName, l.creationDate, l.expireDate, l.isPublic
					FROM list AS l
	                INNER JOIN listUser AS lu
	                ON l.listId = lu.listId
	                WHERE lu.userId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $userId);

		$assignedLists = $this->m_db->GetAssignedLists($stmt);

		return $assignedLists;
	}


	public function SaveNewList($list) {

		// TODO: Lägg till desc!!!


		$query = "INSERT INTO list (userId, listName, creationDate, expireDate, isPublic) VALUES(?, ?, ?, ?, ?)";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("isssi", $list['userId'],
								   $list['listName'],
								   $list['creationDate'],
								   $list['expireDate'],
								   $list['isPublic']);

		$listId = $this->m_db->CreateNewList($stmt);

		$objectsAdded = $this->InsertListObjects($listId, $list['listObjects']);

		if ($isPublic == false) {
			$this->InsertListUsers($listId, $list['checkedUsers']);
		}

		$list['listId'] = $listId;

		return $list;
	}

	public function SaveListOrder($userId, $listOrder, $listId) {

		$listOrderArray = explode('.', $listOrder);
		$isFinished = 1;

		$i = 0;
		foreach ($listOrderArray as $listElem) {

			$query = "INSERT INTO listElemOrder (listId, listElemId, userId, listElemOrderPlace) VALUES(?, ?, ?, ?)";	

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

		$query = "UPDATE listUser SET isFinished = ?
				  WHERE listId = ?
				  AND userId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('iii', $isFinished, $listId, $userId);

		// TODO: Fixa RunUpdateQuery??????
		$ret = $this->m_db->RunInsertQuery($stmt);

		return true;
	}

	public function InsertListObjects($listId, $listObjects) {

		var_dump($listObjects);
		foreach ($listObjects as $listObject) {

			$listObjectName = $listObject['listObjectName'];
			$listObjectDesc = $listObject['listObjectDesc'];

			$query = "INSERT INTO listElement (listElemName, listId, listElemDesc) VALUES(?, ?, ?)";

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
			$query = "INSERT INTO listUser (listId, userId) VALUES(?, ?)";

			$stmt = $this->m_db->Prepare($query);

			$stmt->bind_param('ii', $listId, $checkedUser);

			$this->m_db->RunInsertQuery($stmt);
		}
	}

	public function HasFinishedSorting($userId, $listId) {

		$query = "SELECT isFinished FROM listUser
					WHERE userId = ?
					AND listId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('ii', $userId, $listId);

		$result = $this->m_db->HasFinishedSorting($stmt);

		if ($result != null) {
			return $result;
		}
	}

	public function CheckListStatus($listId) {

		$query = "SELECT isFinished FROM listUser
					WHERE listId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $listId);

		$listIsDone = $this->m_db->CheckListStatus($stmt);

		return $listIsDone;
	}

	public function ShowList($listId, $listView, $userIsFinished, $listIsDone) {

		$listOptions = $this->GetListOptions($listId);		// : Array

		$listElements = $this->GetListElements($listId);		// : Array
		$listUsers = $this->GetListUsers($listId);			// : Var

		$list = array('listId' => $listId,
					  'listOptions' => $listOptions,
					  'listElements' => $listElements,
					  'listUsers' => $listUsers);
		
		$output = $listView->ShowList($list, $userIsFinished, $listIsDone);

		return $output;
	}

	// KLAR!
	public function GetListOptions($listId) {
		
		$query = "SELECT l.listId, l.userId, l.listName, l.creationDate, .l.expireDate, l.isPublic, u.username
					FROM list AS l
					INNER JOIN user AS u
					ON l.userId = u.userId
					WHERE l.listId=?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $listId);
		

		$listOptions = $this->m_db->GetListOptions($stmt);

		return $listOptions;
	}

	// KLAR!
	public function GetListElements($listId) {
		
		$query = "SELECT listElemId, listElemName, listElemDesc, listElemOrderPlace
					FROM listElement
					WHERE listId=?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listElements = $this->m_db->GetListElements($stmt);

		return $listElements;
	}

	public function GetListUsersIds($listId) {

		$query = "SELECT userId
					FROM listUser
					WHERE listId = ?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param('i', $listId);

		$listUsers = $this->m_db->GetListUsersIds($stmt);

		return $listUsers;
	}

	public function GetListOrders($listId, $listUsers) {

		foreach ($listUsers as $listUser) {

			$query = "SELECT listElemId, listElemOrderPlace
						FROM listElemOrder
						WHERE userId = ?
						AND listId = ?";

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
		
		foreach ($listOrders as $listOrder) {
			$i = 0;
			foreach ($listOrder as $listElem) {
				$orderPlaces[$i]['listElemId'] = $listElem['listElemId'];
				$orderPlaces[$i]['listElemPoints'] += $listElem['listElemPoints'];
				$i += 1;
			}
		}

		function subval_sort($a, $subkey) {
			foreach($a as $k=>$v) {
				$b[$k] = strtolower($v[$subkey]);
			}
			arsort($b);
			foreach($b as $key=>$val) {
				$c[] = $a[$key];
			}
			return $c;
		}

		$orderedList = subval_sort($orderPlaces, 'listElemPoints');

		for ($i = 0; $i < count($orderedList); $i++) {
			$orderedList[$i]['listElemOrderPlace'] = $i+1;
		}

		return $orderedList;
	}

	public function AddListElemOrderPlaces($orderedList) {

		foreach ($orderedList as $listElem) {

			$query = "UPDATE listElement SET listElemOrderPlace = ?
				  WHERE listElemId = ?";

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
					FROM listUser AS lu
					INNER JOIN user as u
					ON lu.userId = u.userId
					WHERE lu.listId=?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listUsers = $this->m_db->GetListUsers($stmt);

		return $listUsers;
	}

	//TODO: Implement test!
}