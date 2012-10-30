<?php

namespace View;

class HeaderView {
	
	public function GetMenu($isLoggedIn) {

		if ($isLoggedIn == false) {
			$menu = "<div id='loginMenu'>
						Login
						<img id='loginArrow' src='img/arrow.png' />
					</div>";
		}

		$menu .= "<div id='headerDiv'><ul class='menu'>
					<div id='logo'>Collaborage</div>
					<li class='menu'><a href='index.php?type=list&action=newList'>+Create list</a></li>
    				<li class='menu'><a href='index.php?type=list&action=showLists'>Lists</a></li>
    				<li class='menu'><a href='index.php?type=admin&action=showUsers'>Admin</a></li>
  				</ul>
  				<div class='clear'></div></div>";

		echo $menu;

		return $menu;
		
	}
}