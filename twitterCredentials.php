<?php
//include the abraham's twitteroauth library
require_once ('twitteroauth.php');
//create an app and get the settings from dev.twitter.com
$consumerKey = "UTIlZnShuN52jyG2wKFaeetSi"; //add the key from your app
$consumerSecret = "5iAl77slH4WNKiCOKVnvTOQnDGMqohcFbgT8y2LXR0ssGzh8Yg"; //add the secret from your app
$accessToken = "1000730095131115521-zmbEzM5hLIyAznXYfQ3g2EDFIQvxhb
"; //add the access token from your app
$accessSecret = "usWjbLBblhX9LwvRMNxhx38zIhbaN3gnbA6dAHpGKJt75"; //add the access secret from your app
$connection = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessSecret);
$connection -> post('statuses/update', array('status' => $message ));
?>

	
