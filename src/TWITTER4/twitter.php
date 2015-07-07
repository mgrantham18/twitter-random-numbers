<?php
set_time_limit(600);
//random finding logistics
$GLOBALS['tweets'] = array();
function startRandom($tweet) {
    array_push($GLOBALS['tweets'], $tweet);
    if(count($GLOBALS['tweets']) == 10) {
        getRandom($GLOBALS['tweets']);
        unset($GLOBALS['tweets']);
        $GLOBALS['tweets'] = array();
    }
}
function getRandom($strings) { //expects array of 8 strings
    $charset = array(
        '1','2','3','4','5','6','7','8','9','0'
    );
    $amountOfChars = count($charset) - 1;
    $random = '';
    for($i = 0; strlen($random) < 8 && count($strings) > $i; $i++) {
        $charNumber = random($strings[$i]);
        if ($charNumber > 9) {
            continue;
        }
            $random .= $charset[random($strings[$i])];
    }
    saveRandom($random);
}

//random calculations
function random($string) {
	$saltset = array(
        	'1','2','3','4','5','6','7','8','9','0','a','s','d','f','g','h','j','k','l','q','w','e','r','t','u','i','o','p','z','x','c','v','b','n','m'
    	);
        $salt = '';
        for($i = 0; $i < 64; $i++) {
            $salt .= $saltset[rand(0,count($saltset))];
        }
    $string = $string . $salt;
    $string =  sha1($string); //creates sha1 hash of value
    $string =  str_replace(" ", "", $string);
    $string =  str_split($string);
    $sizeOfCalc = count($string);
    $numbers = 0;
    $number = 0;
    for ($chars = 0; $chars < $sizeOfCalc; $chars++) {
        $value = unpack('H*', $string[$chars]);
        $value = str_split(base_convert($value[1], 16, 2));
        for($numbersAdded = 0; $numbersAdded < count($value); $numbersAdded++) {
            $x = abs($value[$numbersAdded]);
            $number += $x;
        }
    }
    $number = abs($number);
    $numberLength = count(array_filter(str_split($number),'is_numeric'));
    $maxLength = 10;
    $number = $number / pow(10, ($numberLength - 1));
    $number = $number * ($maxLength - 1);
    $number = ($number - sin($number)) * pow($number, 0.5); //modified fractal equation
    $numberTen = floor($number);
    $number = ($number * 10) - ($numberTen * 10); //actual math stuff
    $number = round($number);
    $numbers = $number;
    return $numbers;
}
//save random
function saveRandom($random) {
    if(strlen($random) == 8) {

        print($random . "<br/>");
    }
    else {
        print("<br/><br/><h1>ERROR: NOT CORRECT STRING RETURNED. EXPECTED 00000000 GOT " . $random . ".</h1><br/><br/>");
    }
}
//GET TWEETS
require_once('phirehose/lib/Phirehose.php');
require_once('phirehose/lib/OauthPhirehose.php');
class SampleConsumer extends OauthPhirehose
{
    public function enqueueStatus($status)
    {
        $data = json_decode($status, true);
        if (is_array($data) && isset($data['user']['screen_name'])) {
            $tweet = ($data['user']['screen_name'] . urldecode($data['text']));
            startRandom($tweet);
        }
    }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "");
define("TWITTER_CONSUMER_SECRET", "");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "");
define("OAUTH_SECRET", "");

// Start streaming
$sc = new SampleConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_SAMPLE, Phirehose::FORMAT_JSON, 'en');
$sc->consume();


?>                                
                            
