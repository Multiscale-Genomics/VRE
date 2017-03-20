<?php
require "phplib/global.inc.php";

$sort = array('mtime' => -1);

try {
        $pdbConn = new MongoClient("mongodb://dataLoader:mdbwany2015@ms1.mmb.pcb.ub.es");
}
catch (MongoConnectionException $e){
        #die('Error connecting to MongoDB server');
        redirect("problems.php");
}
catch (MongoException $e) {
        #die('Error: ' . $e->getMessage());
        redirect("problems.php");
}

$db1          = $pdbConn->NuclDyn;
$filesCol     = $db1->files;
$filesMetaCol = $db1->filesMetadata;


#$stringId = (string) $mongoId;

$count = $filesCol->count();

$results1 = $filesCol->find()->sort($sort); // MongoCursor no persisteix a la sessio
$results2 = $filesMetaCol->find()->sort($sort); // MongoCursor no persisteix a la sessio
$results = $filesCol->find()->sort($sort);

print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");
print "<h4>COUNT = $count</h4>";

?>
<script>

	function stopPropagation(evt) {
		if (evt.stopPropagation !== undefined) {
			evt.stopPropagation();
		} else {
			evt.cancelBubble = true;
		}
	};

	function format ( d ) {
	    // `d` is the original data object for the row
	    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
	        '<tr>'+
	            '<td>Full name:</td>'+
	            '<td>'+d.name+'</td>'+
	        '</tr>'+
	        '<tr>'+
	            '<td>Extension number:</td>'+
	            '<td>'+d.extn+'</td>'+
	        '</tr>'+
	        '<tr>'+
	            '<td>Extra info:</td>'+
	            '<td>And any further details here (images etc)...</td>'+
	        '</tr>'+
	    '</table>';
	}

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

                var select = $('<select style="width: 100%;"><option value="">Select</option></select>')
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


    $('#browseTable #headerSearch tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );



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

function formatData($data) {
        $data['mtime'] = strftime('%d %b %G - %H:%M', $data['mtime']->sec);
	return $data;
}

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
	print parseTemplate($_REQUEST, $templates['searchList1']['headerTempl1']);
	foreach ($results as $r) {
		#echo sprintf('<p>Added on %s. Last viewed on %s. Viewed %d times. </p>', $r['createdAt'], $r['lastViewed'], $r['counter']);
		var_dump(formatData($r));
                print parseTemplate(formatData($r), $templates['searchList1']['dataTempl1']);
	}
} 
?>
	</table>
	<div style="float: right; margin-top:10px;">
	<input type="submit" style="margin-left: 20px; margin-bottom:20px;" value="Open Analyses for selected simulations" onclick="return oneChecked()">
	<input type="button" value="Mark all" onclick='markAll()'>
	<input type="button" value="Unmark all" onclick='unMarkAll()'>
	<input type="reset" value="Reset">
	</div>
    </form>

<?php

print footerMMB();
