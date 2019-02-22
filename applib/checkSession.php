<?php

session_start();
require "../phplib/globals.inc.php";
require "../phplib/funclib.inc.php";

// timeout in seconds for the ending of sessions
$timeout = $GLOBALS['TIMEOUT']; 

// Check if the timeout field exists.
if(isset($_SESSION['VREtimeout'])) {
    // See if the number of seconds since the last
    // visit is larger than the timeout period.
    $duration = time() - (int)$_SESSION['VREtimeout'];

    if($duration > $timeout) {
				echo '{"hasSession":false, "duration":"'.secondsToTime($duration).'"}';
    } else {
				// restarting sessions
				$remaining = $timeout - $duration;
				echo '{"hasSession":true, "remaining":"'.sprintf('%02d', $remaining).'"}';
		}
}
 
