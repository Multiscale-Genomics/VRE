<?php
/*
 * MDWeb
 * libMMB.inc.php
 * General layout
 */

function headerTP($title, $user='',$menu=True) {
    $userData = '';
    if ($user) 
        $userData = $user['name']." ".$user['surname'];

    $header = "header.inc.htm";
    if (preg_match("/MuG/",$_SERVER[REQUEST_URI])){
        $header = "headerMuG.inc.htm";
    }
    return parseTemplate(
                    Array('title' => $title, 'userData' => $userData, 'menu' => $menu, 'homeURL' => $GLOBALS['homeURL']), getTemplate($header)
    );
}

function footerTP() {
    $footer = "footer.inc.htm";
    if (preg_match("/MuG/",$_SERVER[REQUEST_URI])){
        $footer = "footerMuG.inc.htm";
    }

    return parseTemplate(Array(), getTemplate($footer));
}

function errorPage($title, $text) {
    return headerTP($title) . $text . footerTP();
}

function formError($idErr, $txtErr='') {
    if ($_SESSION['errorData'][$idErr]) {
        return "<tr><td colspan=\"2\"><span style=\"color:red\">" . $GLOBALS['errors']['formErrors'][$idErr] . "</span></td></tr>";
    } else
        return '';
}
?>
