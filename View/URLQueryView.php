<?php

namespace View;

// Klass som hämtar information från URL:en
class URLQueryView {

    /**
     * Hämtar och returnerar "type" från URL:en
     *
     * @return String
     */
	public function GetType() {

		if (isset($_GET['type'])) {
			$type = $_GET['type'];
		}

		return $type;
	}

	/**
     * Hämtar och returnerar "action" från URL:en
     *
     * @return String
     */
	public function GetAction() {

		if (isset($_GET['action'])) {
			$action = $_GET['action'];
		}

		return $action;
	}

	/**
     * Hämtar och returnerar "listId" från URL:en
     *
     * @return String
     */
	public function GetListId() {
		if (isset($_GET['listId'])) {
			$listId = $_GET['listId'];
		}

		return $listId;
	}

	/**
     * Hämtar och returnerar "userId" från URL:en
     *
     * @return String
     */
	public function GetUserId() {
		if (isset($_GET['userId'])) {
			$userId = $_GET['userId'];
		}

		return $userId;	
	}

	/**
     * Hämtar och returnerar "listOrder" från URL:en
     *
     * @return String
     */
	public function GetListOrder() {
		if (isset($_GET['listOrder'])) {
			$listOrder = $_GET['listOrder'];
		}

		return $listOrder;	
	}
}