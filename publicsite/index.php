<?php

require "../phplib/genlibraries.php";
//redirectOutside();

if($_REQUEST["type" ] == "tools") {

	$tls = getTools_List(1);
	$tlsProv = getTools_List(0);
	//$vslzrs = getVisualizers_List();

	$toolList = array_merge($tls, $tlsProv/*, $vslzrs*/);

} else {
	
	$toolList = getVisualizers_List();

}

sort($toolList);

?>

<?php require "../htmlib/header.inc.php"; ?>



<body class="">

<!-- BEGIN CONTENT -->
                <div class="page-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        
												<div class="portfolio-content portfolio-3" style="background-color:#fafafa!important">

														<input type="hidden" id="fake-home" value="1">														

														<?php 
														
														$kw = array();
														foreach($toolList as $t) { 
															foreach($t['keywords'] as $tk) $kw[] = $tk;
														}

														$kw = array_unique($kw);
														sort($kw);	

														?>

                            <div class="clearfix">
                                <div id="js-filters-lightbox-gallery2" class="cbp-l-filters-button cbp-l-filters-left">
									<div data-filter="*" class="cbp-filter-item-active cbp-filter-item btn blue btn-outline uppercase">All</div>

                                       <?php foreach($kw as $k) { ?>
    										<div data-filter=".<?php echo $k; ?>" class="cbp-filter-item btn blue btn-outline uppercase"><?php echo /*str_replace("-", " ", $k);*/ $k; ?></div>
										<?php } ?>
                                    </div>
                                    </div>
									<div id="js-grid-lightbox-gallery" class="cbp">

    								  <?php 
									  foreach($toolList as $t) { 
                                            $kw = implode(" ", $t['keywords']);

											if (strpos($kw, 'visualizer') === false) $type = 'tools';
											else $type = 'visualizers';

											?>

										<div class="cbp-item <?php echo $kw; ?>">
    										<!-- REMOVE cbp-singlePageInline to go to new page -->
											<a href="<?php echo $type; ?>/<?php echo $t['_id']; ?>/assets/home/index.html" class="cbp-caption cbp-singlePageInline" data-title="<?php echo $t['title']; ?>" rel="nofollow">
                                                <div class="cbp-caption-defaultWrap">
                                                    <img src="<?php echo $_REQUEST['type']; ?>/<?php echo $t['_id']; ?>/assets/home/logo.png" alt="">
                                                </div>
                                              <div class="cbp-caption-activeWrap">
                                                <div class="cbp-l-caption-alignLeft">
                                                    <div class="cbp-l-caption-body">
													    <div class="cbp-l-caption-title"><?php echo $t['title']; ?></div>
                                                        <div class="cbp-l-caption-desc"><?php echo $t['short_description']; ?></div>
                                                    </div>
                                                </div>
                                              </div>
                                           </a>
										</div>

									<?php } ?>

                            </div>

                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<script>

window.onload = function(e) { 
	document.getElementsByClassName("page-content")[0].style.backgroundColor = "#fafafa";
}

</script>

<?php 

require "../htmlib/js.inc.php";

?>


