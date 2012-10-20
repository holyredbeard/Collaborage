<?php

namespace Model;

class ListHandler {

	private $m_db = null;

	public function __construct(Database $db) {
		$this->m_db = $db;
	}

	public function ChangeListOrder($listId) {
		// TODO: Implement function!

		/*$listOptions = $listHandler->GetListOptions($listId);		// Array

		$output = $listView->ShowList;*/
	}

	public function AddNewList() {
		// TODO: Implement function!
	}

	// KLAR!
	public function GetListOptions($listId) {
		$listId = 1;	// Detta ska bort så småningom
		
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
					INNER JOIN listElemDesc as led
					ON le.listElemId = led.listElemId
					WHERE le.listId=?";

		$stmt = $this->m_db->Prepare($query);

		$stmt->bind_param("i", $listId);
		
		$listElements = $this->m_db->GetListElements($stmt);

		return $listElements;
	}

	// KLAR!
	public function GetListUsers($listId) {

		$query = "SELECT lu.userId, u.username, lu.isFinished
					FROM listUsers AS lu
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