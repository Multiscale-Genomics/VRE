<?php

# check.inc.php
# Controls de format
#
# Copyright 1999 Josep Ll. Gelpi G3 COM, S.L.
#

function checkOblig(&$falta, &$f, $oblig) {
# versio obligatoris en formulari
    foreach (explode("#", $oblig) as $cm) {
        if (!$f[$cm])
            $falta[$cm][] = 'oblig';
    }
    return count($falta);
}


function checkUploadFile($fileField, $targetField) {
    $errorCount = 0;
    loadFile($fileField, $targetField);
    if ($_SESSION['inputData'][$fileField.'Error'] == '-1') {
        $errorCount++;
        $_SESSION['errorData'][$fileField][] = "maxFileSize";
    } else if ($_SESSION['inputData'][$fileField.'Error']) {
        $errorCount++;
        $_SESSION['errorData'][$fileField][] = "uploadFile";
    }
    return $errorCount;
}

function loadFile($fileField, $targetField) {
    $resultError = 0;
    $resultData = '';
    if ($_FILES[$fileField]['name']) {
        if ($_FILES[$fileField]['error'])
            $resultError = $_FILES[$fileField]['error'];
        else if ($_FILES[$fileField]['size'] > $GLOBALS['limitFileSize'])
            $resultError = -1;
        else
            $resultData = file_get_contents($_FILES[$fileField]['tmp_name']);
    }
    if (!$resultError and $resultData)
        $_SESSION['inputData'][$targetField] = $resultData;
    else
        $_SESSION['inputData'][$fileField . 'Error'] = $resultError;
}
?>