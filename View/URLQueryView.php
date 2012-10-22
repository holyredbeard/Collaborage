<?php

namespace View;

class URLQueryView {

	public function GetType() {

		if (isset($_GET['type'])) {
			$type = $_GET['type'];
		}

		return $type;
	}

	public function GetAction() {

		if (isset($_GET['action'])) {
			$action = $_GET['action'];
		}

		return $action;
	}

	public function GetListId() {
		if (isset($_GET['listId'])) {
			$listId = $_GET['listId'];
		}

		return $listId;	
	}

	public function GetUserId() {
		if (isset($_GET['userId'])) {
			$userId = $_GET['userId'];
		}

		return $userId;	
	}
}