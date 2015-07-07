<?php
set_time_limit(6);
$GLOBALS['tweets'] = array();
function startRandom($tweet) { //expects string (tweet) returns none, continues to getRandom when 10 tweets have been accumulated
    array_push($GLOBALS['tweets'], $tweet);
    if(count($GLOBALS['tweets']) == 10) {
        getRandom($GLOBALS['tweets']);
	exit();
    }
}
function getRandom($strings) { //expects array of 8 strings returns none, calls random, continues to saveRandom
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

function random($string) { //expects string (tweet) returns single 0-9 digit
	$saltset = array(
        	'1','2','3','4','5','6','7','8','9','0','a','s','d','f','g','h','j','k','l','q','w','e','r','t','u','i','o','p','z','x','c','v','b','n','m'
    	);
        $salt = '';
        for($i = 0; $i < 64; $i++) {
            $salt .= $saltset[rand(0,34)];
        }
    $string = $string . $salt;
    $string =  sha1($string);
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
    $number = ($number - sin($number)) * pow($number, 0.5);
    $numberTen = floor($number);
    $number = ($number * 10) - ($numberTen * 10);
    $number = round($number);
    $numbers = $number;
    return $numbers;
}

function saveRandom($random) { //expects random number returns none, prints value or prints error
    if(strlen($random) == 8) {
	print $random;
	print "\n";
    }
    else {
        print "ERROR: NOT CORRECT STRING RETURNED. EXPECTED ######## GOT " . $random . ".\n";
    }
}
function isPrime($num) {
    //1 is not prime. See: http://en.wikipedia.org/wiki/Prime_number#Primality_of_one
    if($num == 1)
        return false;

    //2 is prime (the only even number that is prime)
    if($num == 2)
        return true;

    /**
     * if the number is divisible by two, then it's not prime and it's no longer
     * needed to check other even numbers
     */
    if($num % 2 == 0) {
        return false;
    }

    /**
     * Checks the odd numbers. If any of them is a factor, then it returns false.
     * The sqrt can be an aproximation, hence just for the sake of
     * security, one rounds it to the next highest integer value.
     */
    for($i = 3; $i <= ceil(sqrt($num)); $i = $i + 2) {
        if($num % $i == 0)
            return false;
    }

    return true;
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
            $GLOBALS['randoms'] = array();
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
function stream() {
	$sc = new SampleConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_SAMPLE, Phirehose::FORMAT_JSON, 'en');
	$sc->consume();
}
stream();
?>                                
                            
