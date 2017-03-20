<?php

require "../phplib/genlibraries.php";
redirectOutside();

$tls = getTools_List();
$vslzrs = getVisualizers_List();

$tools = array_merge($tls, $vslzrs);

sort($tools);

?>

<?php require "../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../htmlib/top.inc.php"; ?>
	<?php require "../htmlib/menu.inc.php"; ?>

<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <span>Home</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Homepage
                        </h1>
                        <!-- END PAGE TITLE-->
												<!-- END PAGE HEADER-->

												<p>
                          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum faucibus tempor augue, id condimentum libero vestibulum id. Nulla tristique sed odio vitae vulputate. Integer placerat massa sem, sed pulvinar lacus convallis sed. Donec viverra tortor ac ipsum pulvinar porttitor. Curabitur eu nisl ante. Donec felis neque, euismod sed laoreet a, laoreet et dui. Vestibulum tincidunt est ut orci finibus egestas. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                        </p>
												<div class="portfolio-content portfolio-3">

														<?php 
														
														$kw = array();
														foreach($tools as $t) { 
															foreach($t['keywords'] as $tk) $kw[] = $tk;
														}

														$kw = array_unique($kw);
														sort($kw);	

														?>

                            <div class="clearfix">
                                <div id="js-filters-lightbox-gallery2" class="cbp-l-filters-button cbp-l-filters-left">
																		<div data-filter="*" class="cbp-filter-item-active cbp-filter-item btn blue btn-outline uppercase">All</div>
		
																		<?php foreach($kw as $k) { ?>
																		<div data-filter=".<?php echo $k; ?>" class="cbp-filter-item btn blue btn-outline uppercase"><?php echo str_replace("-", " ", $k); ?></div>
																		<?php } ?>																	
	
                                </div>
                            </div>
														<div id="js-grid-lightbox-gallery" class="cbp">
		
																<?php 

																foreach($tools as $t) { 

																$kw = implode(" ", $t['keywords']);

																if (strpos($kw, 'visualizer') === false) $type = 'tools';
																else $type = 'visualizers';

																?>

																	<div class="cbp-item <?php echo $kw; ?>">
																	<!-- REMOVE cbp-singlePageInline to go to new page -->
                                    <a href="<?php echo $type; ?>/<?php echo $t['_id']; ?>/assets/home/index.html" class="cbp-caption cbp-singlePageInline" data-title="<?php echo $t['title']; ?>" rel="nofollow">
                                        <div class="cbp-caption-defaultWrap">
                                            <img src="<?php echo $type; ?>/<?php echo $t['_id']; ?>/assets/home/logo.png" alt="">
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

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
