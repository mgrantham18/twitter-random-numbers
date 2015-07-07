                                <html><body><?php
require_once('../lib/Phirehose.php');
require_once('../lib/OauthPhirehose.php');
/**
 * Example of using Phirehose to display the 'sample' twitter stream.
 */
class SampleConsumer extends OauthPhirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
    /*
     * In this simple example, we will just display to STDOUT rather than enqueue.
     * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
     *       enqueued and processed asyncronously from the collection process.
     */
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
      $tweet = ($data['user']['screen_name'] . urldecode($data['text']));
        print getRandom($tweet);
        print "</br>";
    }
  }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "kF8DnyU4Vab6f0Jb5NRcpoczU");
define("TWITTER_CONSUMER_SECRET", "U05I1FBme6PazpVMHExEuvCWYZbHLk4xMKRI9VbgQCaNV1jBWT");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "2821484464-U4T0kHRhDAYWSf4cKbNS0H7tXcAO9pv2gUnCjmH");
define("OAUTH_SECRET", "4NCu0eFPjzPdkhUdvD9PoyQLJrcNSxOMDfWN7FDkGHI0c");

// Start streaming
$sc = new SampleConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_SAMPLE);
$sc->consume(); ?></body></html>

                            