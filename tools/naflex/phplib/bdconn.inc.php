<?php
# bdconn.inc
# script de connexio
#
function bdconn ($database) {
  
//    $host="mmb3.mmb.pcb.ub.es";
    $host="localhost";
//    $host="inb238.mmb.pcb.ub.es";
	$dbname=$database;
	$user="gelpi";
	$password="jl12gb";
  /*
    $host="localhost";
	$dbname=$database;
	$user="dcicinsain";
	$password="dam76365";
  */
	($conn=mysql_connect($host,$user,$password)) or
		die (mysql_error());
        ($id=mysql_select_db($dbname)) or
		die (mysql_error());
	return $conn;
}

?>
