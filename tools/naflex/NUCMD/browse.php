<?php

require "phplib/global.inc.php";

$sort = array('time' => -1);

if (preg_match("/MuG/",$_SERVER[REQUEST_URI])){
	$in = array('dataset' => array('$in' => array('MuG')));
	$count = $simData->find($in)->count();
	$results = $simData->find($in)->sort($sort); // MongoCursor no persisteix a la sessio
}
else {
	$count = $simData->count();
	$results = $simData->find()->sort($sort); // MongoCursor no persisteix a la sessio
}

//print headerMMB();

$external_ap = "bignasim";
require "../master/header.php";


print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");

//require "phplib/header.php";

?>

<div id="pagewrap_vre">


<div class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb"><span class="trail-begin"><a href="/MuGVRE" title="Virtual Research Environment" rel="home" class="trail-begin">Home</a></span> <span class="sep">&raquo;</span> <span class="trail-end">BigNASim</span></div>


<script>

	function stopPropagation(evt) {
		if (evt.stopPropagation !== undefined) {
			evt.stopPropagation();
		} else {
			evt.cancelBubble = true;
		}
	};

  $(document).ready( function() {

        menuTabs("Browse");

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
</script>
<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>
<?php

if (!$count)
    print "<h3>No records found</h3>";
else {
?>
    <script type="text/javascript" src="js/markList.js"></script>
    <hr style="margin-bottom:0px;"></hr>

    <p><strong>Click on Id to open Simulation Metadata and Analyses</strong></p>

    <form name="retrieve" action="analyses2.php" method="post" style="margin: 5px;">
	<table id="browseTable">
<?php
	print parseTemplate($_REQUEST, $templates['searchList']['headerTempl']);
	foreach ($results as $r) {
		#echo sprintf('<p>Added on %s. Last viewed on %s. Viewed %d times. </p>', $r['createdAt'], $r['lastViewed'], $r['counter']);
                print parseTemplate(prepNUCDBData($r), $templates['searchList']['dataTempl']);
	}
} 
?>
	</table>
	<div style="float: right; margin-top:10px;">
<?php
    if (! preg_match("/MuG/",$_SERVER[REQUEST_URI])){
?>
	<input type="submit" style="margin-left: 20px; margin-bottom:20px;" value="Open Analyses for selected simulations" onclick="return oneChecked()">
	<input type="button" value="Mark all" onclick='markAll()'>
	<input type="button" value="Unmark all" onclick='unMarkAll()'>
	<input type="reset" value="Reset">
<?php
}
?>
	</div>
    </form>

</div>

<?php

//print footerMMB();

require "../master/footer.php";

?>
