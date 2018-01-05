<?php

require "../phplib/genlibraries.php";

$user = $GLOBALS['usersCol']->findOne(array('_id' => $_SESSION['User']['_id']));
		
if ($user['_id']) {
    $newdata = array('$set' => array( 'firstTime' =>1));
    $GLOBALS['usersCol']->update(array('_id' => $_SESSION['User']['_id']), $newdata);
    $_SESSION['User']['firstTime'] = 1;
}
redirect('/workspace/');

?>
