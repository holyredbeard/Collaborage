<?php

namespace View;

class ValidationView {
	
	// Array som lagrar felmeddelanden.
	// private $m_errors = array();

	// Felmeddelanden för listor
	const NEED_LIST_NAME = "<span class='errorMessage'>You need to give the list a name.</span>";
	const NEED_MORE_LISTOBJECTS = "<span class='errorMessage'>You need to add at least three list objects.</span>";
	const NEED_MORE_USERS = "<span class='errorMessage'>You need to assign at least two users.</span>";

	// Felmeddelanden för registering
    const PASSWORD_DID_NOT_MATCH = "The passwords didn't match!";
    const USERNAME_WRONG_FORMAT = "Username contains illegal characters.";
    const USERNAME_TOO_SHORT = "Username is too short (minimum 5 characters).";
    const PASSWORD_WRONG_FORMAT = "Password is in wrong format.";
    const PASSWORD_TOO_SHORT = "Password is too short (minimum 6 characters).";
    const ALL_FIELDS_NOT_FILLED = "You must to fill in all fields.";
    const USERNAME_EXISTS = "Username already exists.";
}