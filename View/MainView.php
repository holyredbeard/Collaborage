<?php

namespace View;

class MainView {

	public function ShowMainNotLoggedIn () {
		return "<div id='mainViewContainer'>
					<h2>Welcome to Collaborage!</h2>
					<p>Collaborage is a service that brings democracy to a new level when it comes to making decisions.</p>
					<p>With the use of Collaborage you and your workmates, friends or family can make decisions from whatever
					options you have when needed, and everyone has the same influence of the final decision.</p>
					
					<h3 class='mainView'>How does it work?</h3>
					<p><strong>We'll explain how it works with a straightforward example:</strong></p>
					<p>You and three of your friends are going on a roadtrip in Arizona and have three places left in the planning
					that you shall visit. The problem is that you have a lot to choose from, and not all of you wants to
					go to the same places.</p>
					<p>You decide to give Collaborage a chance, so and one of you creates a list with the options you choose from:
					<strong><i>Grand Canyon, Lowell Observatory, Saguaro National Park, The Phoenix Zoo, Lake Powell, Montezuma Castle and Jerome.</strong></i></p>
					<p>You log in one at a time and drag the options in the order that means the most interesting in the top descending, and saves.<p>
					<p>When all of you are done with the prioritazion you'll the the average order, and the top three ones are going to be the ones you'll visit.
				</div>";
	}

	public function ShowMainLoggedIn ($username) {
		return "<div id='mainViewContainer'>
					<h2>Welcome back, $username!</h2>

					<h3 class='mainView'>Text here...</h3>
					<p>And here...</p>
				</div>";
	}
}