<? # log.inc.php
function addLog ($user, $project, $txt, $filein="NULL", $fileout="NULL") {
    if ($filein != "NULL") {
        $fileinarr = split('[/ ]', $filein);
        $filein = $fileinarr[8];
    }
    if ($fileout != "NULL") {
        $fileoutarr = split('[/ ]', $fileout);
        $fileout = $fileoutarr[8];
    }
}

function logger($entry) {
    $entry = timestamp()." | $entry \n";
    $fh = fopen($GLOBALS['logFile'], 'a') or die("can't open file ".$GLOBALS['logFile']);
    fwrite($fh, $entry);
    fclose($fh);
}
?>
