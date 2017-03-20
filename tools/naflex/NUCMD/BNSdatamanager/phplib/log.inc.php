<?php # log.inc.php
function logger($entry) {
    $login = $_SESSION['userData']['login'];
    $entry = timestamp()." | $login | $entry \n";
    $fh = fopen($GLOBALS['logFile'], 'a') or die("can't open file ".$GLOBALS['logFile']);
    fwrite($fh, $entry);
    fclose($fh);
}
?>
