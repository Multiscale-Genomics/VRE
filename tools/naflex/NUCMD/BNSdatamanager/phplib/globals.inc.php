<?php
#
# require "phplib/bdconn.inc.php";
#

# Set Manager filesystem (fs|gridfs)
$GLOBALS['fsStyle'] = 'gridfs';

# Set Password management (mongodb|passFile)
$GLOBALS['passStyle'] = 'mongodb';

# set environment 
if (!isset($_ENV['GRIDFSHOST'])){
    $_ENV['GRIDFSHOST'] = 'localhost';
//  $_ENV['GRIDFSHOST'] =dataLoader:mdbwany2015@mmb.pcb.ub.es");
}
#
//require_once "phplib/Classes/PMut/UserClass.php";
//require_once "phplib/Classes/FtpClass.php";
//define('FTP_HOST', 'localhost');
require_once "phplib/mongoDB.inc.php";

#
require_once "phplib/session.inc.php";
require_once "phplib/libraries.inc.php";
#
require_once "phplib/globalVars.inc.php";
require_once "phplib/recursos.inc.php";
#

