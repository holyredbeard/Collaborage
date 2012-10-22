<?php

namespace Model;

class ListHandler {

	private $m_db = null;

	public function __construct(Database $db) {
		$this->m_db = $db;
	}

	public function GetAllLists($listView) {

		$query = "SELECT listId, listName, isPublic FROM list";

		$stmt = $this->m_db->Prepare($query);

		$lists = $this->m_db->GetAllLists($stmt);

		return $lists;
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

	public function InsertListObjects($listId, $listObjects) {

		foreach ($listObjects as $listObject) {
			$query = "INSERT INTO listElement (listElemName, listId) VALUES(?, ?)";

			$stmt = $this->m_db->Prepare($query);

			$stmt->bind_param('si', $listObject, $listId);

			$ret = $this->m_db->RunInsertQuery($stmt);
		}
	}

	public function InsertListUsers($listId, $checkedUsers) {

		foreach ($checkedUsers as $checkedUser) {
			$query = "INSERT INTO listUser (listId, userId) VALUES(?, ?)";

			$stmt = $this->m_db->Prepare($query);

			$stmt->bind_param('ii', $listId, $checkedUser);

			$this->m_db->RunInsertQuery($stmt);
		}
	}

	public function ChangeListOrder($listId) {
		// TODO: Implement function!

		/*$listOptions = $listHandler->GetListOptions($listId);		// Array

		$output = $listView->ShowList;*/
	}

	public function ShowList($listId, $listView) {

		$listOptions = $this->GetListOptions($listId);		// : Array

		$listElements = $this->GetListElements($listId);		// : Array
		$listUsers = $this->GetListUsers($listId);			// : Var

		$list = array('listId' => $listId,
					  'listOptions' => $listOptions,
					  'listElements' => $listElements,
					  'listUsers' => $listUsers);

		$output = $listView->ShowList($list);

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

		$stmt->bind_param("i", $listId);
		
		$listOptions = $this->m_db->GetListOptions($stmt);

		return $listOptions;
	}

	// KLAR!
	public function GetListElements($listId) {
		
		$query = "SELECT le.listElemId, le.listElemName, le.listElemOrderPlace, led.listElemDesc
					FROM listElement AS le
					LEFT JOIN listElemDesc as led
					ON le.listElemId = led.listElemId
					WHERE le.listId=?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listElements = $this->m_db->GetListElements($stmt);

		return $listElements;
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