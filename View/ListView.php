<?php

namespace View;

class ListView {
	public function ShowList($list) {
		
		$listId = $list['listId'];
		$listName = $list['listOptions']['listName'];
		$listCreator = $list['listOptions']['listCreator'];

		$creationDate = $list['listOptions']['creationDate'];

		if ($list['listOptions']['isPublic'] == true){
			$isPublic = "<p><strong>Public list: </strong>Yes</p>";
		}
		else {
			$isPublic = "<p><strong>Public list: </strong>No</p>";
		}

		if ($list['listOptions']['expireDate'] != NULL) {
			$expireDate = $list['listOptions']['expireDate'];

			$showExpireDate = "<p><strong>Expire date:</strong> $expireDate</p>";
		}

		function CreateListElements($list) {

			// Shuffles the list elements
			shuffle($list['listElements']);

			foreach ($list['listElements'] as $element) {

				$listElemId = $element['listElemId'];
				$listElemName = $element['listElemName'];
				$listElemDesc = $element['listElemDesc'];
				//$orderPlace = $element['listElemOrderPlace'];

				$showElements .= "<div id='listElement'>
									<strong>$listElemName (Id: $listElemId)</strong><br>
									$listElemDesc
								</div>";
			}

			return $showElements;
		}

		function CreateListUsers($list) {
			foreach ($list['listUsers'] as $user) {
				$userId = $user['userId'];
				$username = $user['username'];
				$isFinished = $user['isFinished'];

				if ($isFinished == 1) {
					$isFinished = 'Yes';
				} else {
					$isFinished = 'No';
				}

				$showUsers .= "<li><a href='index.php?showUser=$userId'>$username</a> | <strong>Done:</strong> $isFinished</strong></li>";
			}

			$showUsers .= "</ul>";

			return $showUsers;
		}

		$showUsers = CreateListUsers($list);
		$showElements = CreateListElements($list);

		// Create list
		$listHTML = "<div id='listContainer'>
						<h1>List</h1>
						<h3>$listName</h3>
						<div id='listElements'>
							$showElements
						</div>
						<div id='listUsers'>
							<h3>Collaborators</h3>
							$showUsers
						</div>
						<div id='listInfo'>
							<h3>List info</h3>
							<strong>List creator:</strong> $listCreator<br/>
							<strong>Creation date:</strong> $creationDate<br/>
							$showExpireDate
							$isPublic
						</div>
					 </div>";

		return $listHTML;
	}
}