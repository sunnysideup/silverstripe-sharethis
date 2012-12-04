<?php

/*
Application ID
216039248422508
API Key
e397a7d034ff4bd746274fd7249d62ed
App secret
bf0ca9026ea1715a2ddc1c2746948ec8
Site URL
http://photowarehouse.com/
Site Domain
photowarehouse.com

*/

/**
 * @author: http://matthom.com/archive/2011/03/27/using-the-facebook-api-with-php
 *
 **/

include "classes/facebook.php";
include "functions/facebook.php";


$facebook_post_array = array(
		"message" => "I can't believe the Beefy Crunch Burrito went up 50 cents!",
		"description" => "Taco Bell customer enraged that the seven burritos he ordered had gone up in price fired an air gun at an employee and later fired an assault rifle at officers",
		"link" => "http://www.suntimes.com/4432609-417/angry-taco-bell-customer-fires-at-officers.html",
		"picture" => "http://matthom.com/images/emblem_m1.jpg",
);

if ($facebook_me) {
	$facebooked = $facebook->api("/me/feed", "POST", $facebook_post_array);
}

if ($facebook_me) {
	$facebooked = $facebook->api("/4376853125542/feed", "POST", $facebook_post_array);
}
