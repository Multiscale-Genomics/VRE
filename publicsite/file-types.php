<?php

require "../phplib/genlibraries.php";

$filetypes = getFileTypesList();
?>

<?php require "../htmlib/header.inc.php"; ?>

<body class="" style="background-color:#fafafa!important">

<!-- BEGIN CONTENT -->
                <div class="page-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                       
												<table class="table table-striped table-bordered"> 

													<tbody>

													<?php 

													$c = 0;
													foreach($filetypes as $ft) { 

														if(($c % 6) == 0) echo "<tr>";

														echo "<td>".$ft["_id"]."</td>";

														if(($c % 6) == 5) echo "</tr>";

														$c ++;

													} 
														?>
												</tbody>
											</table>


                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->



<?php 

require "../htmlib/js.inc.php";

?>
