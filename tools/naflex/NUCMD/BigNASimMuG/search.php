<?php
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
//print "<pre>";print_r($_REQUEST);print "</pre>";
#
$sort = array('time' => -1);

$_SESSION['inputData'] = $_REQUEST;

$cond = array();
$condOr = array();

    if ($_SESSION['inputData']['idSubSeqPDB']){
	$idSubSeqPDB = $_SESSION['inputData']['idSubSeqPDB'];
    }

    if ($_SESSION['inputData']['idBPS'] and $_SESSION['inputData']['idBPS'] != "BPS"){
	$bps = $_SESSION['inputData']['idBPS'];
	$idSubSeq = $bps;
    }
    if ($_SESSION['inputData']['idSubSeq']){
	$idSubSeq = $_SESSION['inputData']['idSubSeq'];
    }
    if ($_SESSION['inputData']['idQuickSearch']){
	#$idSubSeq = $_SESSION['inputData']['idQuickSearch'];
	#$idPdb = $_SESSION['inputData']['idQuickSearch'];
	#$idDesc = $_SESSION['inputData']['idQuickSearch'];
	$idQuickSearch = $_SESSION['inputData']['idQuickSearch'];
    }

    if ($idQuickSearch) {
	$rex = new MongoRegex("/$idQuickSearch/i");
	$condOr[] = array('PDB' => $rex);
	$condOr[] = array('altPDB' => $rex);
	$condOr[] = array('sequence' => $rex);
	$condOr[] = array('description' => $rex);
	$condOr[] = array('authors' => $rex);
	$condOr[] = array('_id' => $rex);
    }

#    if ($idDesc) {
#	$rex = new MongoRegex("/$idDesc/i");
#	$condOr[] = array('Description' => $rex);
#    }

    $idSubSeq = strtoupper($idSubSeq);

    $flank = 0;
    if ($_SESSION['inputData']['flank']){
	$flank = $_SESSION['inputData']['flank'];
    	$reg_flank = "{".$flank."}";

	$flankUp = str_repeat('X',$flank);
	$flankDown = str_repeat('X',$flank);
	#$idSubSeqFull = $flankUp.$idSubSeq.$flankDown;

	$filter = "<mark style='background-color: #FF9900;'>$flankUp</mark><mark>$idSubSeq</mark><mark style='background-color: #FF9900;'>$flankDown</mark>";
    }

    if($idSubSeq and $idSubSeq != "SELECT BASE PAIR STEP") {
	if($reg_flank){
	    $rex = new MongoRegex("/\w$reg_flank$idSubSeq\w$reg_flank/i");
	}
	else{
	    $rex = new MongoRegex("/$idSubSeq/i");
	}
	    $cond[] = array('sequence' => $rex);
    }
    else if ($idSubSeq == "SELECT BASE PAIR STEP"){
	$idSubSeq = '';
    }

    $ontoList = '';
    if (isset($_SESSION['inputData']['typeForm'])) {
	$arrayOnto = array();
	foreach ($_SESSION['inputData']['typeForm'] as $onto => $v) {

                if ($onto == "Structure" and $v == "Structure")
                        continue;
                if ($onto == "TrajectoryType" and $v == "TrajectoryType")
                        continue;
                if ($onto == "HelicalConf" and $v == "OriginalHelicalConformation")
                        continue;
                if ($onto == "LocalStructures" and $v == "LocalStructures")
                        continue;
                if ($onto == "SeqFeatures" and $v == "SequenceFeatures")
                        continue;

                if ($onto == "NAType" or $onto == "SequenceModifications" or $onto == "SystemType" or $onto == "SeqFeatures" or $onto == "LocalStructures"){

                        if (isset($_SESSION['inputData']['typeForm'][$onto][$onto])){
                                continue;
                        }

                        if (! empty($_SESSION['inputData']['typeForm'][$onto])){

                                $condNAtype = array();
				$ontoList.="$onto: ";
				$cont = 0;
                                foreach ($_SESSION['inputData']['typeForm'][$onto] as $natype => $v2) {
                                        $codeOnto = $ontoHash2[$v2];
                                        $codeOnto += 0;
                                        $arrayOnto = new MongoRegex( "/^". $codeOnto ."/" );
                                        $condNAtype[] =  array('ontology' => $arrayOnto);
					if($cont)
						$ontoList.=", $v2";
					else
						$ontoList.="$v2";
					$cont++;
                                }
				$ontoList.="<br/>";
                                $fcondNAtype = array('$or' => $condNAtype);
                                $cond[] = $fcondNAtype;
                        }
                        continue;
                }
                if ($onto == "temperature"){
                        if ($v == "Select Temperature:")
                                continue;
                }
                if ($onto == "forceField"){
                        if ($v == "Select Force Field:")
                                continue;
                }
                if ($onto == "waterType"){
                        if ($v == "Select Water Type:")
                                continue;
                }
                if ($onto == "ionicConc"){
                        if ($v == "Select Ionic Concentration:")
                                continue;
                }
                if ($onto == "ionsParams"){
                        if ($v == "Select Ions Parameters:")
                                continue;
                }
                if ($onto == "length"){
                        if ($v == "Select Simulation Length:")
                                continue;
                }

                $codeOnto = $ontoHash2[$v];
		$ontoList.="$onto: $v<br/>";

                #echo "$onto -$v- $codeOnto <br/>";
                $codeOnto += 0;
                $arrayOnto = new MongoRegex( "/^". $codeOnto ."/" );
                $cond[] =  array('ontology' => $arrayOnto);
	}
    }

    if(!empty($cond)){
	$fcond = array('$and' => $cond);
    }
    else {
	$fcond = array('_id' => array( '$exists' => 1));
    }

    if(!empty($condOr)){
	$fcond = array('$or' => $condOr);
    }

    $_SESSION['inputData']['cond'] = $fcond;

#print "<pre>";
#print json_encode($fcond);
#print "</pre>";

$count = $simData->find($_SESSION['inputData']['cond'])->count();

$results = $simData->find($_SESSION['inputData']['cond'])->sort($sort); // MongoCursor no persisteix a la sessio

foreach ($results as $r) {
	$id = $r['_id'];
	$seq = $r['sequence'];
	$metaTrajInfo[$id] = $seq;
}

if($idSubSeq){
	$listIds = getListIds($metaTrajInfo,$idSubSeq,$flank,$simData);
	$_SESSION['searchResults'] = $metaTrajInfo;
	if (count($listIds) > 1)
		$_SESSION['metaTrajList'] = $listIds;
	else
		unset($_SESSION['metaTrajList']);
}

$searchResultsSelected = array();
foreach ($_REQUEST as $k => $v){
	if(isset($metaTrajInfo[$k]))
		$searchResultsSelected[$k] = $v;
}
$_REQUEST = $searchResultsSelected;

print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");

?>

<script>

        function stopPropagation(evt) {
                if (evt.stopPropagation !== undefined) {
                        evt.stopPropagation();
                } else {
                        evt.cancelBubble = true;
                }
        };

  $(document).ready( function() {

        menuTabs("Search");

        // Setup - add a text input to each footer cell
        $('#browseTable #headerSearch .inputSearch').each( function () {
                var title = $('#browseTable thead th').eq( $(this).index() ).text();
                if(title){
                        $(this).html( '<input style="width: 75%;" type="text" onclick="stopPropagation(event);" placeholder="'+title+'" />' );
                }
        } );

        var table = $('#browseTable').DataTable( {
                orderCellsTop: true
        });

        $('#browseTable #headerSearch').children().each(function(index,element) {
           if($( this ).hasClass("selector")){
                var column = table.column(index);

                var select = $('<select style="width: 85%;"><option value="">Select</option></select>')
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );

                $(this).html(select); 

                $(this)
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                                $(this).find("select").val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
           }
        });

        // Apply the filter
        //$("#headerSearch input").on( 'keyup change', function () {
        $("#headerSearch input").on( 'keyup change', function () {
                if($( this ).parent().hasClass("inputSearch")){
                        table
                        .column( $(this).parent().index()+':visible' )
                        .search( this.value )
                        .draw();
                }
        } );

  });

function doMetaTraj() {
    if (! oneChecked()) return;
    $('#retrieve').attr('target','');
    $('#retrieve').attr('action','metatrajFragment.php');
    $('#retrieve').submit();
}
</script>

    <div class="metaImageSection" style="clear:both;">
        <div class="metaImage" id="searchInfo" style="background-color: lightgrey;padding:0px;width:400px;margin-left:auto;margin-right:auto;">
		<div id='titleReduced' class='mongoFilter' style='color: darkslategrey;'> 
			Search Info: 
			<?php
			if($idSubSeq)
				if($flank)
					print "<p>Sequence: $filter</p>\n";
				else
					print "<p>Sequence: $idSubSeq</p>\n";
			?>
			<?php
			if($idSubSeqPDB)
				print "<p>PDB: $idSubSeqPDB</p>\n";
			?>
			<?php
			if($idQuickSearch)
				print "<p>Quick Search: $idQuickSearch</p>\n";
			?>
			<?php
			if($ontoList)
				print "<p>Ontology:<br/><br/> $ontoList</p>\n";
			?>
		</div>
        </div>
    </div>

<?php 

if (!$count) 
    print "<h3>No records found</h3>";
else {
    ?>
    <script type="text/javascript" src="js/markList.js"></script>
    <hr style="margin-bottom:0px;"></hr>
<?php
	if($idSubSeq and isset($_SESSION['metaTrajList'])){
		?>
		<p class="metaTrajButton" style="cursor: pointer;"><a id='MetaTraj' class='unselected' onclick='doMetaTraj();' title='Retrieve Metatrajectory'>Retrieve MetaTrajectory</a></p>
		<img style="float: right;width: 70px; margin-top: 5px;" src="images/cassandra_logo_small.png">
		<?php
	}
?>

    <p><strong>Click on Id to open Simulation Metadata and Analyses</strong></p>

    <form name="retrieve" id="retrieve" action="analyses2.php" method="post">
        <table id="browseTable">
            <?php

            print parseTemplate($_REQUEST, $templates['searchList']['headerTempl']);
		foreach ($results as $r) {

		  $id = $r['_id'];
		  $seq = $r['sequence'];
		  $metaTrajInfo[$id] = $seq;

                  $row = parseTemplate(prepNUCDBData($r), $templates['searchList']['dataTempl']);

		  if($idSubSeq){
			$row = highlightFragment($row,$idSubSeq,$flank);
		  }
		  if($idQuickSearch){
			$row = highlightFragmentQS($row,$idQuickSearch,$flank);
		  }
		  print "$row";
                }
            ?>
        </table>

	<div style="float: right; margin-top:10px;">
        <input type="hidden" name="idSubSeq" value="<?=$idSubSeq?>"> 
        <input type="hidden" name="flank" value="<?=$flank?>"> 
        <input type="submit" style="margin-left: 20px; margin-bottom:20px;" value="Open Analyses for selected simulations" onclick="return oneChecked()"> 
        <input type="button" value="Mark all" onclick='markAll()'> 
        <input type="button" value="Unmark all" onclick='unMarkAll()'> 
        <input type="reset" value="Reset">
	</div>

    </form>
<?php
}
print footerMMB();

