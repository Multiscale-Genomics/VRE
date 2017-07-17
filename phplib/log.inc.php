<?php # log.inc.php

function timestamp() {
    return date("d/m/y : H:i:s", time());
}

function logger($entry) {
    $entry = timestamp()." | $entry \n";
    $fh = fopen($GLOBALS['logFile'], 'a') or die("can't open file ".$GLOBALS['logFile']);
    fwrite($fh, $entry);
    fclose($fh);
}

?>
