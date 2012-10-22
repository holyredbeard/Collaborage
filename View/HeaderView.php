<?php

namespace View;

class HeaderView {
	
	public function GetMenu() {

		$menu = "<ul>
					<li><a href='index.php?type=list&action=newList'>Create list</a></li>
    				<li><a href='index.php?type=list&action=viewList'>Lists</a></li>
    				<li><a href='index.php?type=users&action=showLists'>People</a></li>
    				<li><a href='index.php?type=admin&action=showUsers'>Admin</a></li>
  				</ul>
  				<div class='clear'></div>";

		return $menu;
	}
}