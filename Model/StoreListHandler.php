<?php

namespace Model;

class StoreListHandler {
	
	private $m_list = array();

	/**
     * Setter för listor
     * 
     * @param Int $listId, Int $userId, String $listName, String $creationDate, Boolean $listIsDone
     * @return boolean
     */
	public function SetLists($listId, $userId, $listName, $creationDate, $listIsDone) {
		$this->m_list[] = array('listId' => $listId,
							    'userId' => $userId,
							    'listName' => $listName,
							    'creationDate' => $creationDate,
							    'listIsDone' => $listIsDone);
	}

	/*
	public function SetListObjects($listElemName, $listId, $listElemDesc) {
		$this->m_listObjects[] = array('listElemName' => $listElemName,
									 'listId' => $listId,
									 'listElemDesc' => $listElemDesc);
	}

	public function SetListUsers($listElemName, $listId, $listElemDesc) {
		$this->m_listObjects[] = array('listElemName' => $listElemName,
									 'listId' => $listId,
									 'listElemDesc' => $listElemDesc);
	}*/

	/**
     * Getter för listor
     * 
     * @return Array listor
     */
	public function GetLists() {
		return $this->m_list;
	}

}