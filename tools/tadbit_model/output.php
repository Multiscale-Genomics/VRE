<?php

require "../../phplib/genlibraries.php";
redirectOutside();

function listFolderFiles($dir, $ext) {
	$di = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$it = new RecursiveIteratorIterator($di);

	$list = array();
	foreach($it as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) == $ext) {
					$list[] = pathinfo(dirname($file), PATHINFO_BASENAME).'/'.pathinfo($file, PATHINFO_BASENAME);
			}
	}

	return $list;
}

$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];
$indexFile = $wd.'/index';

$results = file($indexFile);

$dir = basename(getAttr_fromGSFileId($_REQUEST['project'],'path'));

//processing results files

$tmp_dir  = $GLOBALS['dataDir'].'/'.$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];
$pathTemp = 'files/'               .$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];

$PNGs = listFolderFiles($tmp_dir, "png");
$TSVs = listFolderFiles($tmp_dir, "tsv");

/*$PNGs = glob("$tmp_dir/*.png");
$PNGs = array_map('basename',$PNGs);

$QC_plots      = array_values(preg_grep("/^QC-plot/",$PNGs));
$hist_frag     = array_values(preg_grep("/^histogram_fragment_sizes_/",$PNGs));
$coverage      = array_values(preg_grep("/^genomic_coverage_/",$PNGs));
$ints_vs_coord = array_values(preg_grep("/^interactions_vs_genomic-coords/",$PNGs));
$map_raw       = array_values(preg_grep("/^genomic_maps_raw/",$PNGs));
$bad_cols      = array_values(preg_grep("/^bad_columns_/",$PNGs));
$map_nrm       = array_values(preg_grep("/^genomic_maps_nrm/",$PNGs));
$comparts      = array_values(preg_grep("/^chr.*(?<!summ.png)$/",$PNGs));
$comparts_sum  = array_values(preg_grep("/^chr.*summ.png$/",$PNGs));*/
/*
var_dump($PNGs);
var_dump($QC_plots);
var_dump($hist_frag);
var_dump($coverage);
var_dump($ints_vs_coord);
var_dump($map_raw);
var_dump($bad_cols);
var_dump($map_nrm);
var_dump($comparts);
var_dump($comparts_sum);
*/

$pathTGZ = 'files/'.$_SESSION['User']['id']."/".$dir;

//project folder ID
$dirName = basename(getAttr_fromGSFileId($_REQUEST['project'],'path'));

?>

<?php require "../../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../../htmlib/top.inc.php"; ?>
  <?php require "../../htmlib/menu.inc.php"; ?>

<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <a href="home/">Home</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <a href="workspace/">User Workspace</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Tools</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>TADbit Model</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLize()-->
                        <h1 class="page-title"> Results
                            <small>TADbit Model</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
              		    <div class="col-md-12">
					<p style="margin-top:0;">
					Download a compressed version of the results that include all the images and files previsualized here.
       					</p>
               		    </div>
               		    <div class="col-md-12">
					<div class="note note-info">
					<h4><a href="workspace/workspace.php?op=downloadFile&fn=<?php echo $results[0]; ?>" style="text-decoration:none;"><i class="fa fa-download"></i> Download all in a compressed tar.gz file</a></h4>
				</div>
                  	    </div>
                        </div>
			

										<!-- PNGs -->
			
			<?php
				if(!empty($PNGs)) {
					$c = 0;
					foreach($PNGs as $p) { ?>
					<?php if(($c % 2) == 0) { ?> <div class="row"> <?php } ?>
					<div class="col-md-6"><img src="<?php echo $pathTemp."/".$p; ?>" style="width:100%;" /></div>
					<?php if(($c % 2) == 1) { ?> </div><div class="row">&nbsp;</div> <?php } ?>
					<?php 
					$c ++;
					} 
				}
				if($c == 1) echo '</div><div class="row">&nbsp;</div>';
			?>
			

												
						<!-- TSVs -->

				<?php 

				//$GLOBALS['dataDir'];

				if(!empty($TSVs)) {

					$pt = explode('/', $pathTemp);
					$aux = array_shift($pt);
					$pt = implode('/', $pt);

					foreach($TSVs as $t) {
						?>

						<h4><?php echo $t; ?></h4>

						<div class="row">
							<div class="col-md-12">
								<table  class="table table-striped">
						<?php				

						$row = 1;
						if (($handle = fopen($GLOBALS['dataDir'].$pt."/".$t, "r")) !== FALSE) {
							$c = 0;
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
								$cols = preg_split('/\s+/', $data[0]);
								if($c == 0) echo('<strong>'.implode(" ", $cols).'</strong>');
								?>
									<?php if($c == 1) { ?> 
									<thead>
										<tr>
											<?php foreach($cols as $col) { ?>
												<th><?php echo $col; ?></th>
											<?php } ?>
										<tr>
									</thead>
									<?php } else if($c != 0){ ?>
									<?php if($c == 2) { ?><tbody><?php } ?>
										<tr>
											<?php foreach($cols as $col) { ?>
												<td><?php echo $col; ?></td>
											<?php } ?>
										<tr>
									<?php } ?>
									<?php 
									$c ++;
								}
								fclose($handle);
							}
							?>
									</tbody>	
								</table>
							</div>
						</div>
						<div class="row">&nbsp;</div>

					<?php
					}
				}
		

				?>



                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
