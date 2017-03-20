<?php

#

# genlib.inc.php

# Utilitats generals 

# Copyright 2007 Josep Ll. Gelpi

#



function pad($t, $n) {

    return str_pad($t, $n, "0", STR_PAD_LEFT);

}



function elimEspais($t) {

    return (trim($t));

}



function elimCRLF($t) {

    return str_replace("\r\n", " ", $t);

}



function translStr($txt, $a, $b) {

    return str_replace($a, $b, $txt);

}



function elimStr($txt, $a) {

    return str_replace($a, "", $txt);

}



function protCom($t) {

    $t1 = str_replace("\"", "&quot;", $t);

    return str_replace("'", "&#146;", $t1);

}



function protNum($t) {

    return str_replace(",", ".", $t);

}



function elimNoChar($t) {

# elimina dels extrems

    $a = "������������������������<>()";

    while (strpos($a, substr($t, 0, 1)) !== false)

        $t = delStr($t, 0, 1);

    while (strpos($a, substr($t, strlen($t) - 1, 1)) !== false)

        $t = delStr($t, strlen($t) - 1, 1);

    return $t;

}



function noAccents($t) {

    return translStr($t, "������������������������", "aaeeiioouucnAAEEIIOOUUCN");

}



function delStr($a, $p1, $l) {

    return substr_replace($a, "", $p1, $l);

}



function isUpper($a, $l) {

    $n=0.;

    $ua = strtoupper($a);    

    for ($i=0;$i<strlen($a);$i++)  {

        if (substr($a,$i,1) != substr($ua,$i,1))

                $n++;

    }

    return ((100.*$n/strlen($a)) < $l);

}

   

# Dates



function avui() {

    return date("Ymd");

}



function ara() {

    return date("His");

}



function araNos() {

    return date("Hi");

}



function moment() {

    return date("YmdHis");

}



function timestamp() {

    return date("d/m/y : H:i:s", time());

}



function momentNos() {

    return date("YmdHi");

}



function getTimestamp($dat) {

    if (strlen($dat) == 8)

        return mktime(0, 0, 0, substr($dat, 4, 2), substr($dat, 6, 2), substr($dat, 0, 4));

    else

        return mktime(substr($dat, 8, 2), substr($dat, 10, 2), substr($dat, 12, 2), substr($dat, 4, 2), substr($dat, 6, 2), substr($dat, 0, 4));

}



function prdata($idi, $dat) {

    if (strlen($dat) == 8)

        return date("d.m.Y", getTimestamp($dat));

    else

        return date("d.m.Y H:i \h", getTimestamp($dat));

}



function prdataText($idi, $dat) {

    $tst = getTimestamp($dat);

    $txt = $GLOBALS['dayNamesComp'][date('w', $tst)] . " " . date('j', $tst) . 

            ' de ' . $GLOBALS['monthNames'][date('n', $tst) - 1];

    if (strlen($dat) > 8) {

        $txt .= " a les " . date('H', $tst) . ":" . date('i', $tst) . " h";

    }

    return $txt;

}



function redirect($url) {

    header("Location:$url");

    exit;

}

## Compression

function gzip($data) {

    if (function_exists('gzencode'))

        return gzencode($data);

    else {

        $fn = tmpDir . "/" . uniqId('gztmp');

        file_put_contents($fn, $data);

        exec("/bin/gzip $fn");

        $data = file_get_contents($fn.'.gz');

        unlink($fn.'.gz');

        return $data;

    }

}



function gunzip($data) {

    if (function_exists('gzdecode'))

        return gzdecode($data);

    else {

        $fn = tmpDir . "/" . uniqId('gztmp');

        file_put_contents($fn . ".gz", $data);

        exec("/bin/gunzip $fn.gz");

        $data = file_get_contents($fn);

        unlink($fn);

        return $data;

    }

}

