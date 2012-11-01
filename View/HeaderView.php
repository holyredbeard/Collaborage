<?php

namespace View;

class HeaderView {
	
	public function GetMenu($isLoggedIn, $isAdmin) {

		if ($isLoggedIn == false) {
			$menu = "<div id='loginMenu'>
						Login
						<img id='loginArrow' src='img/arrow.png' />
					</div>";
		}
		else {
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