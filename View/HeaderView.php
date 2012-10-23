<?php

namespace View;

class HeaderView {
	
	public function GetMenu() {

		$menu = "<ul class='menu'>
					<li class='menu'><a href='index.php?type=list&action=newList'>Create list</a></li>
    				<li class='menu'><a href='index.php?type=list&action=showLists'>Lists</a></li>
    				<li class='menu'><a href='index.php?type=users&action=viewPeople'>People</a></li>
    				<li class='menu'><a href='index.php?type=admin&action=showUsers'>Admin</a></li>
  				</ul>
  				<div class='clear'></div>";

		return $menu;
	}
}