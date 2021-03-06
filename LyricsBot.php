<?php

include ('cron.php');
include('twitterCredentials.php');
include("LyricsBotHelpers.php");
require_once('TwitterAPIExchange.php');
header('Content-Type: text/html; charset=utf-8');

/** Set access tokens here - see: https://apps.twitter.com/ **/
$APIsettings = array(
    '1000730095131115521-zmbEzM5hLIyAznXYfQ3g2EDFIQvxhb' => $oauthToken,
    'usWjbLBblhX9LwvRMNxhx38zIhbaN3gnbA6dAHpGKJt75' => $oauthTokenSecret,
    'UTIlZnShuN52jyG2wKFaeetSi' => $consumerKey,
    '5iAl77slH4WNKiCOKVnvTOQnDGMqohcFbgT8y2LXR0ssGzh8Yg' => $consumerSecret
);

/** Set Lyrics Wikia Artist Page here **/
$artistWikiaLink = "http://http://lyrics.wikia.com/wiki/Vampire_Weekend"; // For example, for Manchester Orchestra: http://lyrics.wikia.com/wiki/Manchester_Orchestra

// Get list of songs with lyrics from artist page
$artistLink = substr(strrchr( $artistWikiaLink, '/' ), 1);
$artistPage = getCURLOutput($artistWikiaLink);
$artistXpath = getDOMXPath($artistPage);
$songsNodes = $artistXpath->query('//b/a[starts-with(@href, "/wiki/'.$artistLink.':") and not(contains(@href, "?action=edit"))]');


if($songsNodes->length > 0){
  // Create songs array to filter duplicate songs
  $songs = array();
  foreach($songsNodes as $node) {
    $songs[] = $node->getAttribute("title") . '/href=' . $node->getAttribute("href");
  }
  $songs = array_values(array_filter(array_unique($songs)));

  $start = time();
  $tweet = "";
  // Checking time to prevent the script to run indefinitely
  while(time() - $start < 300 && (strlen($tweet) < 20 || strlen($tweet) > 140)){
    // Take a random song
    $idx = intval(rand(0, count($songs) - 1));
    $song = $songs[$idx];
    $songAttrTitle = substr($song, 0, strrpos($song, "/href="));
    $songAttrHref = substr($song, strrpos($song, "/href=") + strlen("/href="));
    $artistName = substr($songAttrTitle, 0 , strrpos($songAttrTitle, ":"));
    $songName = substr($songAttrTitle, strrpos($songAttrTitle, ":") + 1);
    $lyricsLink = "http://lyrics.wikia.com" . $songAttrHref;

    // Get the lyrics and credits
    $lyricsPage = getCURLOutput($lyricsLink);
    $lyricsXpath = getDOMXPath($lyricsPage);
    $lyricsQuery = $lyricsXpath->query('//div[@class="lyricbox"]/text()');
    $creditsQuery = $lyricsXpath->query('//div[@class="song-credit-box"]/text()');
    for($i = 0; $i < $lyricsQuery->length; $i++){
      $lyrics .= $lyricsQuery->item($i)->nodeValue;
      $lyrics .= "\n";
    }
    $lyrics = delete_all_between("(", ")", $lyrics);

    // Create the tweet between 1 and 4 sentences
    $splitLyrics = explode("\n", $lyrics);
    $sentences = intval(rand(1));
    // Try to create a tweet, but if no success in 20 tries, take another random song
    $tryCounter = 0;
    do{
      $tryCounter++;
      $tweet = "";
      $sentencesCounter = 0;
      $randomIdx = intval(rand(0, count($splitLyrics)));
      while($randomIdx < count($splitLyrics) && strlen($tweet . $splitLyrics[$randomIdx]) < 140 && $sentencesCounter < $sentences){
        $tweet .= "\n" . $splitLyrics[$randomIdx];
        $randomIdx++;
        $sentencesCounter++;
      }
    }while((strlen($tweet) < 20 || strlen($tweet) > 140) && $tryCounter < 20);
  }

  // Check if the created tweet is satisfactory
  if(!(strlen($tweet) < 20 || strlen($tweet) > 140)){
    $tweet = trim($tweet, ",");

    // Post the tweet
    $postfields = array(
        'status' =>  $tweet);
    $url = "https://api.twitter.com/1.1/statuses/update.json";
    $requestMethod = "POST";
    $twitter = new TwitterAPIExchange($APIsettings);
    echo $twitter->buildOauth($url, $requestMethod)
                  ->setPostfields($postfields)
                  ->performRequest();
  }
}

<?/** suppose we have 1 hour and 1 minute inteval 01:01 */

$interval_source = "04:00";
$time_now = strtotime( "now" ) / 60;
$interval = substr($interval_source,0,2) * 60 + substr($interval_source,3,2);


if( $time_now % $interval == 0){
/** do cronjob */
}
 ?>
