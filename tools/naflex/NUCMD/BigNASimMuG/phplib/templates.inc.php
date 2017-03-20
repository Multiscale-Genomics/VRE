<?php

/*
 * templates.inc.php
 */

function getTemplate($fn, $idioma = False) {
#print "htmlib/$fn";
    if ($idioma)
        $fn = $GLOBALS['idioma'] . '/' . $fn;
    return file_get_contents($GLOBALS['htmlib'] . '/' . $fn, FILE_TEXT);
}

function existTemplate($fn, $idioma = False) {
    if ($idioma)
        $fn = $GLOBALS['idioma'] . '/' . $fn;
    return file_exists($GLOBALS['htmlib'] . "/$fn");
}

function parseTemplate($f, $txt, $indirFields = '', $dateFields = '', $incRec = True, $recursive = False) {
    if ($incRec)
        $txt = replaceLabel($txt);
    foreach (array_keys($f) as $k) {
        if ($f[$k])
            $txt = preg_replace("/%%$k%([^%]+)%%/", '\\1', $txt);
        else
            $txt = preg_replace("/%%$k%([^%]+)%%/", '', $txt);
    }

    if ($indirFields) {
        foreach (array_keys($indirFields) as $k) {
            $txt = str_replace("##$k##", $GLOBALS[$indirFields[$k]][$f[$k]], $txt);
        }
    }
    if ($dateFields) {
        foreach (array_values($dateFields) as $k) {
            $txt = str_replace("##$k##", prdata($_SESSION['idioma'], $f[$k]), $txt);
        }
    }
    foreach (array_keys($f) as $k)
        $txt = str_replace("##$k##", $f[$k], $txt);
    if (!$recursive)
        $txt = preg_replace("/##([^#]*)##/", "<!--\\1-->", $txt);
    return $txt;
}

function getFormTemplate($templTxt, $formData, $errorData, $m) {
    if (!$m)  // eliminem text formulari marcat amb [%%...%%]
        $templTxt = preg_replace('/\[%%[^%]*%%\]/', '', $templTxt);
    else {
        $templTxt = preg_replace('/\[%%/', '', $templTxt);
        $templTxt = preg_replace('/%%\]/', '', $templTxt);
    }
    $fields = preg_split('/(\[|\])/', $templTxt, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach (array_keys($fields) as $k) {
        if (preg_match('/#/', $fields[$k])) {
            $fields[$k - 1] = '';
            $fields[$k + 1] = '';
            $k1 = str_replace('#', '', $fields[$k]);
            $ff = explode(',', $k1);
            # filtro para comas en instrucciones javascript
            $ff[0] = str_replace(";", ",", $ff[0]);
            switch ($ff[1]) {
                case 'D':
                    $fields[$k] = getInput($ff[0], $m, $formData[$ff[0]], $ff[2], $ff[3]) . " (aaaammdd)";
                    break;
                case 'DH':
                    $fields[$k] = getInput($ff[0], $m, $formData[$ff[0]], $ff[2], $ff[3]) . " (aaaammddHHMM)";
                    break;
                case 'F':
                case 'I':
                case 'T':
                    $fields[$k] = getInput($ff[0], $m, $formData[$ff[0]], $ff[2], $ff[3], $ff[4]);
                    break;
                case 'P':
                    $fields[$k] = getPassword($ff[0], $m, $formData[$ff[0]], $ff[2], $ff[3], $ff[4]);
                    break;
                case 'S':
                    $fields[$k] = getSelect($ff[0], $m, $GLOBALS[$ff[2]], $formData[$ff[0]], $ff[3], $ff[3], $ff[4], $ff[5]);
                    break;
                case 'R':
                    $fields[$k] = getRBut($ff[0], $m, $formData[$ff[0]], $GLOBALS[$ff[2]], $ff[3], $ff[4]);
                    break;
                case 'R1':
                    $fields[$k] = getR1But($ff[0], $m, $ff[2], $formData[$ff[0]], $ff[3]);
                    break;
                case 'C':
                    $fields[$k] = getCBox01($ff[0], $m, $formData[$ff[0]], '', 0, $ff[3]);
                    break;
                case 'L':
                    $fields[$k] = '';
                    break;
                case 'L1':
                    $fields[$k] = $ff[3];
                    break;
                case 'V':
                    $fields[$k] = $formData[$ff[0]];
                    break;
                case 'H':
                    $fields[$k] = getHidden($ff[0], $m, $formData[$ff[0]]);
                    break;
                case 'A':
                    list ($r, $c) = explode('-', $ff[2]);
                    $fields[$k] = getTextArea($ff[0], $m, $formData[$ff[0]], $r, $c, $ff[3]);
                    break;
                case 'IMG':
                    $fields[$k] = getFile($ff[0], $m, $formData[$ff[0]], $ff[2], $ff[3], $ff[4]);
                    break;
            }
            if ($errorData[$ff[0]]) {
                foreach (array_values($errorData[$ff[0]]) as $idErr)
                    $fields[$k] = errorText($idErr) . "<br/>".$fields[$k];
            }
        }
    }
    return join("", $fields);
}

?>
