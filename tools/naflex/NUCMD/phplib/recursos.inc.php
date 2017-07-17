<?php
/*
 * DO original 2009
 * recursos.inc.php
 */
$GLOBALS['label']['ca'] = array ();
$GLOBALS['label']['cs'] = array ();
$GLOBALS['label']['en'] = array ();
//loadLabelDB();
//===============================================================
function loadLabelDB () {
//    $rs = getRecordSet("SELECT * FROM Labels");
//    while ($rsF=mysql_fetch_array($rs)) {
//        $GLOBALS['label'][$rsF['idioma']][$rsF['idLabel']]=$rsF['label'];
//    }
    $labCol = $GLOBALS['db']->Labels;
    $cursor = $labCol->find();
    foreach ($cursor as $idLabel => $value) {
        $GLOBALS['label'][$GLOBALS['idioma']][$idLabel]=$value;
    }
}
//
function getLabel ($id) {
    if (isset ($GLOBALS['label'][$GLOBALS['idioma']][$id]))
        return $GLOBALS['label'][$GLOBALS['idioma']][$id];
    else
        return '##'.$id.'##';
}

function printLabel ($id) {
    print getLabel($id);
}

function labelList () {
    return array_keys ($GLOBALS['label'][$GLOBALS['idioma']]);
}
function replaceLabel ($txt) {
    foreach (labelList() as $k)
        $txt = str_replace ("##$k##",getLabel($k),$txt);
    return $txt;
}
?>