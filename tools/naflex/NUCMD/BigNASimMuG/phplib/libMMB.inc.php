<?php
/*
 * MDWeb
 * libMMB.inc.php
 * General layout
 */

function headerMMB($title, $user=array(),$menu=True) {
    $userData='Anonymous';
#var_dump($_SESSION);
    if($_SESSION['User'] and $_SESSION['User']['Name'] != "Anonymous") {
	$userName = $_SESSION['User']['Name'];
	$userSurname = $_SESSION['User']['Surname'];
        $userData = $userName." ".$userSurname;
        $userId = $_SESSION['User']['id'];
	#echo "UserData: $userData";
        return parseTemplate(
                    Array('title' => $title, 'userData' => $userData, 'userId' => $userId, 'menu' => $menu, 'homeURL' => $GLOBALS['homeURL']), getTemplate('headerLogged.inc.htm')
            );
    }
    else
    	return parseTemplate(
                    Array('title' => $title, 'userData' => $userData, 'menu' => $menu, 'homeURL' => $GLOBALS['homeURL']), getTemplate('header.inc.htm')
            );
	
}

function headerMMBHeadTxt ($title, $headTxt) {
    return headerMMB($title);
}

function footerMMB() {
    return parseTemplate(Array(), getTemplate('footerMuG.inc.htm'));
}

//function headerMMB($title) {
//return  "<!DOCTYPE HTML>
//    <html>
//<head>
//
//<title>$title</title>
//<link rel=\"stylesheet\" type=\"text/css\" href=\"http://mmb.pcb.ub.es/estil.css\">
//</head><body bgcolor=\"#ffffff\">
//<div id=\"divbase\">
//<img border=\"0\" src=\"http://mmb.pcb.ub.es/bannerweb.jpg\" width=\"658\" height=\"114\">
//";
////};

//function headerMMBHeadTxt($title, $headTxt) {
//return  "<html>
//<head>
//<title>$title</title>
//<link rel=\"stylesheet\" type=\"text/css\" href=\"http://mmb.pcb.ub.es/estil.css\">
//$headTxt
//</head><body bgcolor=\"#ffffff\">
//<div id=\"divbase\">
//<img border=\"0\" src=\"http://mmb.pcb.ub.es/bannerweb.jpg\" width=\"658\" height=\"114\">
//";
//};

//function footerMMB () {
//return '
//<table id="Tabla_01" width="658" height="97" border="0" cellpadding="0" cellspacing="0">
//<tr>
//<td style="background-color:#ffffff"><a href="http://www.irbbarcelona.org" target="_blank"><img src="
//http://mmb.pcb.ub.es/images/peuMMB_01.gif" width="89" height="97" alt="IRB Barcelona"border="0"></a></td>
//<td style="background-color:#ffffff"><a href="http://www.ub.edu" target="_blank"><img src="http://mmb.pcb.ub.es/images/peu
//MMB_02.gif" width="156" height="97" alt="Universitat de Barcelona" border="0"></a></td>
//<td style="background-color:#ffffff"><a href="http://www.bsc.es/" target="_blank"><img border="0" src=
//"http://mmb.pcb.ub.es/images/peuMMB_03.gif" width="174" height="97" alt="Barcelona Supercomputing Center"></a></td>
//<td style="background-color:#ffffff"><a href="http://www.inab.org" target="_blank"><img src="http://mmb.pcb.ub.es/images/p
//euMMB_04.gif" width="116" height="97" alt="Instituto Nacional de Bioinform�tica" border="0"></a></td>
//<td style="background-color:#ffffff"><a href="http://www.icrea.es" target="_blank"><img src="http://mmb.pcb.ub.es/images/p
//euMMB_05.gif" width="123" height="97" alt="ICREA" border="0"></td>
//</tr>
//</table>
//</div>
//<script>
//  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
//  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
//  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
//  })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
//
//  ga(\'create\', \'UA-48591078-9\', \'irbbarcelona.org\');
//  ga(\'send\', \'pageview\');
//
//</script>
//</body>
//</html>';
//}

function errorPageMMB($title, $text) {
    return headerMMB($title) . $text . footerMMB();
}

function formError($idErr, $txtErr='') {
    if ($_SESSION['errorData'][$idErr]) {
        return "<tr><td colspan=\"2\"><span style=\"color:red\">" . $GLOBALS['errors']['formErrors'][$idErr] . "</span></td></tr>";
    } else
        return '';
}
?>
