<?php

require "../phplib/genlibraries.php";

redirectOutside();

if(! $_REQUEST['uploadType']){
	$_SESSION['errorData']['getData'][]="Please specify a source data";
	redirect("/workspace/");
}

switch ($_REQUEST['uploadType']){
	case 'file': getData_fromLocal();
							 break;
	case 'url': $source = $_REQUEST['url'];
							getData_fromURL($source);
							break;
	case 'txt': getData_fromTXT();
							break;
	case 'id':  $source = getSourceURL(); 
							getData_fromURL($source['url'], $source['ext']);
                            break;
    case 'repository': getData_fromRepository($_REQUEST);
                            break;
    case 'sampleData': getData_fromSampleData($_REQUEST);
                            break;
	default:
		die(0);
}


?>
