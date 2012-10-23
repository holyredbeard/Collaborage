<?php

namespace Model;

class RetrieveAjax {

	public function RetrieveAjax() {
		$json = $_POST['json'];
        $person = json_decode($json);

        echo $person;
        echo 'hej';
	}
}