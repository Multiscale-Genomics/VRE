<?php
# libraries.inc.php
require "phplib/genlib.inc.php";
require "phplib/libMMB.inc.php";
require "phplib/session.inc.php";
require "phplib/templates.inc.php";
require "phplib/recursos.inc.php";
require "phplib/libform.inc.php";
require "phplib/sge_functions.inc.php";
require "phplib/ProcessSGE.php";

// Log functions not required, they are imported
// from NAFlex2
//require "phplib/log.inc.php";
//
require "phplib/mongo.inc.php";
require "phplib/pdbconn.inc.php";
//
require "phplib/pdb.inc.php";
//require "phplib/aux.inc.php";
//require "phplib/uniprot.inc.php";

# NAFlex libs
//require "../NAFlex2/phplib/users.inc.php";
require "../NAFlex2/phplib/log.inc.php";
//require "../phplib/globals.inc.php";

# Users
require "phplib/errors.inc.php";

//require "../phplib/bdconn.inc.php";
//require "../phplib/globalVars.inc.php";
//require "../phplib/genlib.inc.php";
//require "../phplib/operations.inc.php";
#
//$dbh = bdconn($GLOBALS['database']);
#

# basically you have a variable with the same name as your session. ex:
# $_SESSION['var1'] = null;
# $var1 = 'something';
# which will reproduce this error. you can stop PHP from trying to find 
# existing variables and warning you about them by adding these lines 
# to your script:

ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);
?>
