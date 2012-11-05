<?php

namespace View;

class HeaderView {
	
	/**
    * Skapar menyn vilken returneras och visas med hjälp av controllern
    * 
    * @param Boolean $isLoggedIn, Boolean $isAdmin
    * @return String $menu 
    */
	public function GetMenu($isLoggedIn, $isAdmin) {

		// Om användaren inte är inloggad visas login-rutan.
		if ($isLoggedIn == false) {
			$menu = "<div id='loginMenu'>
						Login
						<img id='loginArrow' src='img/arrow.png' />
					</div>";
		}
		else {
			// Har användaren admin-behörighet visas detta alternativ.
			if ($isAdmin) {
				$admin = "<li class='menu'><a href='index.php?type=admin&action=showUsers'>Admin</a></li>";
			}
		}

		$menu .= "<div id='headerDiv'><ul class='menu'>
					<div id='logo'><a class='logo' href='index.php'>Collaborage</a></div>
					<li class='menu'><a href='index.php?type=list&action=newList'>+Create list</a></li>
    				<li class='menu'><a href='index.php?type=list&action=showLists'>Lists</a></li>
    				$admin
  				</ul>
  				<div class='clear'></div></div>";

		return $menu;
	}
}