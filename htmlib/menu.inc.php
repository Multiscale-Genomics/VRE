
<?php

$currentSection = '';

switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
	case 'index':
		if(dirname($_SERVER['PHP_SELF']) == '/home'){
            $currentSection = 'hp';
        }elseif(dirname($_SERVER['PHP_SELF']) == '/helpdesk') {
            $currentSection = 'hd';
		}else{
				$currentSection = 'uw';
		}
		break;
	case 'input': 
	case 'output': $currentSection = 'uw';
								 break;
	case 'editFile':$currentSection = 'dt';
								  break;
	case 'uploadForm': 
	case 'uploadForm2': $currentSection = 'dt';
					 	    $currentSubSection = 'lc';
					 	    break;
	case 'general': $currentSection = 'he';
					  $currentSubSection = 'h1';
					  break;
	case 'starting':$currentSection = 'he';
					  $currentSubSection = 'h2';
					  break;
	case 'upload':	$currentSection = 'he';
					  $currentSubSection = 'h3';
					  break;	
	case 'ws':$currentSection = 'he';
					  $currentSubSection = 'h4';
					  break;
	case 'launch':$currentSection = 'he';
					  $currentSubSection = 'h5';
						break;
	case 'tools':$currentSection = 'he';
					  $currentSubSection = 'h6';
					  break;

	case 'hdesk':$currentSection = 'he';
					  $currentSubSection = 'h7';
					  break;
	case 'related':$currentSection = 'he';
					  $currentSubSection = 'h8';
					  break;
	case 'refs':$currentSection = 'he';
					  $currentSubSection = 'h9';
					  break;
	case 'ackn':$currentSection = 'he';
					  $currentSubSection = 'h10';
					  break;
	case 'repositoryList': 
	case 'experiment': $currentSection = 'dt';
					 	   $currentSubSection = 'rp';
					  	   break;
	case 'dataFromTxt': $currentSection = 'dt';
					 	   $currentSubSection = 'tx';
					  	   break;
	case 'dataFromID': $currentSection = 'dt';
					 	   $currentSubSection = 'id';
					  	   break;
    case 'sampleDataList': $currentSection = 'dt';
                          $currentSubSection = 'sd';
                          break; 
	case 'usrProfile': $currentSection = 'up';
					  	   $currentSubSection = 'mp';
						   break;
	case 'dashboard':  $currentSection = 'ad';
					  	   $currentSubSection = 'ds';
					  	   break;
	case 'adminUsers': $currentSection = 'ad';
					  	   $currentSubSection = 'au';
					  	   break;
	case 'adminTools': $currentSection = 'ad';
					  	   $currentSubSection = 'at';
								 break;
	case 'jsonValidator': $currentSection = 'ad';
					  	   $currentSubSection = 'jv';
					  	   break;
	case 'help': $currentSection = 'he';
							 $currentSubSection = 'h6';
							 $a = explode("/", dirname($_SERVER['PHP_SELF']));
							 $currentSubSubSection = $a[sizeof($a) - 2];
							 break;
	case 'method': $currentSection = 'he';
							 $currentSubSection = 'h6';
							 $a = explode("/", dirname($_SERVER['PHP_SELF']));
							 $currentSubSubSection = $a[sizeof($a) - 2];
							 $currentSubSubSubSection = 'met';
							 break;
	case 'inputs': $currentSection = 'he';
							 $currentSubSection = 'h6';
							 $a = explode("/", dirname($_SERVER['PHP_SELF']));
							 $currentSubSubSection = $a[sizeof($a) - 2];
							 $currentSubSubSubSection = 'inp';
							 break;
	case 'outputs': $currentSection = 'he';
							 $currentSubSection = 'h6';
							 $a = explode("/", dirname($_SERVER['PHP_SELF']));
							 $currentSubSubSection = $a[sizeof($a) - 2];
							 $currentSubSubSubSection = 'out';
							 break;
	case 'results': $currentSection = 'he';
							 $currentSubSection = 'h6';
							 $a = explode("/", dirname($_SERVER['PHP_SELF']));
							 $currentSubSubSection = $a[sizeof($a) - 2];
							 $currentSubSubSubSection = 'res';
							 break;
	case 'tutorials': $currentSection = 'he';
							 $currentSubSection = 'h6';
							 $a = explode("/", dirname($_SERVER['PHP_SELF']));
							 $currentSubSubSection = $a[sizeof($a) - 2];
							 $currentSubSubSubSection = 'tut';
							 break;
	case 'references': $currentSection = 'he';
							 $currentSubSection = 'h6';
							 $a = explode("/", dirname($_SERVER['PHP_SELF']));
							 $currentSubSubSection = $a[sizeof($a) - 2];
							 $currentSubSubSubSection = 'ref';
							 break;


}

$tools = getTools_List();
sort($tools);



?>

<!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                <!-- BEGIN SIDEBAR -->
                <div class="page-sidebar-wrapper">
                    <!-- BEGIN SIDEBAR -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <div class="page-sidebar navbar-collapse collapse">
                        <!-- BEGIN SIDEBAR MENU -->
                        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                            <li class="sidebar-toggler-wrapper hide">
                                <div class="sidebar-toggler">
                                    <span></span>
                                </div>
                            </li>
                            <!-- END SIDEBAR TOGGLER BUTTON -->
														<li class="nav-item  <?php if($currentSection == 'hp') { ?>active open<?php } ?>">
                                <a href="home/" class="nav-link nav-toggle">
                                    <i class="icon-home"></i>
                                    <span class="title">Homepage</span>
                                </a>
                            </li>

                            <li class="nav-item  <?php if($currentSection == 'uw') { ?>active open<?php } ?>">
                                <a href="workspace/" class="nav-link nav-toggle">
                                    <i class="icon-screen-desktop"></i>
                                    <span class="title">User Workspace</span>
                                    <!--<?php if($currentSection == 'uw') { ?><span class="selected"></span><?php } ?>
                                    <span class="arrow <?php if($currentSection == 'uw') { ?>open<?php } ?>"></span>-->
                                </a>
														</li>
														
														<li class="nav-item  <?php if($currentSection == 'dt') { ?>active open<?php } ?>">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-cloud-upload"></i>
                                    <span class="title">Get Data</span>
                                    <?php if($currentSection == 'dt') { ?><span class="selected"></span><?php } ?>
                                    <span class="arrow <?php if($currentSection == 'dt') { ?>open<?php } ?>"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item <?php if($currentSubSection == 'lc') { ?>active open<?php } ?>">
                                        <a href="getdata/uploadForm.php" class="nav-link ">
                                            <span class="title">Upload Files</span>
                                        </a>
                                    </li>
                                    <li class="nav-item <?php if($currentSubSection == 'rp') { ?>active open<?php } ?>">
                                        <a href="repository/repositoryList.php" class="nav-link ">
                                            <span class="title">From Repository</span>
                                        </a>
                                    </li>
                                    <li class="nav-item <?php if($currentSubSection == 'sd') { ?>active open<?php } ?>">
                                        <a href="getdata/sampleDataList.php" class="nav-link ">
                                            <span class="title">Import sample data</span>
                                        </a>
                                    </li>
																		<!--<li class="nav-item <?php if($currentSubSection == 'id') { ?>active open<?php } ?>">
                                        <a href="getdata/dataFromID.php" class="nav-link ">
                                            <span class="title">From ID</span>
                                        </a>
                                    </li>
																		<li class="nav-item <?php if($currentSubSection == 'tx') { ?>active open<?php } ?>">
                                        <a href="getdata/dataFromTxt.php" class="nav-link ">
                                            <span class="title">From Text</span>
                                        </a>
                                    </li>-->
                                </ul>
                            </li>
                           
														<li class="nav-item  <?php if($currentSection == 'he') { ?>active open<?php } ?>">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-question"></i>
                                    <span class="title">Help</span>
																		<?php if($currentSection == 'he') { ?><span class="selected"></span><?php } ?>
                                    <span class="arrow <?php if($currentSection == 'he') { ?>open<?php } ?>"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item  <?php if($currentSubSection == 'h1') { ?>active open<?php } ?>">
                                        <a href="help/general.php" class="nav-link ">
                                            <span class="title">General information</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h2') { ?>active open<?php } ?>">
                                        <a href="help/starting.php" class="nav-link ">
                                            <span class="title">Getting Started</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h3') { ?>active open<?php } ?>">
                                        <a href="help/upload.php" class="nav-link ">
                                            <span class="title">Get Data</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h4') { ?>active open<?php } ?>">
                                        <a href="help/ws.php" class="nav-link ">
                                            <span class="title">Workspace</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h5') { ?>active open<?php } ?>">
                                        <a href="help/launch.php" class="nav-link ">
                                            <span class="title">Launch Job</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h6') { ?>active open<?php } ?>">
                                        <a href="help/tools.php" class="nav-link">
                                            <span class="title">Tools</span>
																						<span class="arrow <?php if($currentSubSection == 'h6') { ?>open<?php } ?>"></span>
																				</a>
																				<ul class="sub-menu">
																					<?php foreach($tools as $t) { 
																						
																						$s = $GLOBALS['helpsCol']->find(array('tool' => $t["_id"]));

																						$sections = iterator_to_array($s);

																						$arrSect = array();

																						foreach($sections as $sec) {
																							$arrSect[] = $sec['help'];
																						}

																					?>
																					<li class="nav-item <?php if($currentSubSubSection == $t["_id"]) { ?>active open<?php } ?>">
																						<a href="tools/<?php echo $t["_id"]; ?>/help/help.php" class="nav-link">
																						<span class="title"> <?php echo $t["name"]; ?> </span>
																						<span class="arrow <?php if($currentSubSubSection == $t["_id"]) { ?>open<?php } ?>"></span>
																						</a>
																						<ul class="sub-menu">
																						<?php if(in_array("method", $arrSect)) { ?>
																						<li class="nav-item <?php if($currentSubSubSubSection == 'met') { ?>active open<?php } ?>">
																							<a href="tools/<?php echo $t["_id"]; ?>/help/method.php" class="nav-link">
																							<span class="title"> Method </span>
																							</a>
																						</li>
																						<?php } ?>
																						<?php if(in_array("inputs", $arrSect)) { ?>
																						<li class="nav-item <?php if($currentSubSubSubSection == 'inp') { ?>active open<?php } ?>">
																							<a href="tools/<?php echo $t["_id"]; ?>/help/inputs.php" class="nav-link">
																							<span class="title"> Inputs </span>
																							</a>
																						</li>
																						<?php } ?>
																						<?php if(in_array("outputs", $arrSect)) { ?>
																						<li class="nav-item <?php if($currentSubSubSubSection == 'out') { ?>active open<?php } ?>">
																							<a href="tools/<?php echo $t["_id"]; ?>/help/outputs.php" class="nav-link">
																							<span class="title"> Outputs </span>
																							</a>
																						</li>
																						<?php } ?>
																						<?php if(in_array("results", $arrSect)) { ?>
																						<li class="nav-item <?php if($currentSubSubSubSection == 'res') { ?>active open<?php } ?>">
																							<a href="tools/<?php echo $t["_id"]; ?>/help/results.php" class="nav-link">
																							<span class="title"> Results </span>
																							</a>
																						</li>
																						<?php } ?>
																						<!--<li class="nav-item <?php if($currentSubSubSubSection == 'tut') { ?>active open<?php } ?>">
																							<a href="tools/<?php echo $t["_id"]; ?>/help/tutorials.php" class="nav-link">
																							<span class="title"> Tutorials </span>
																							</a>
																						</li>-->
																						<?php if(in_array("references", $arrSect)) { ?>
																						<li class="nav-item <?php if($currentSubSubSubSection == 'ref') { ?>active open<?php } ?>">
																							<a href="tools/<?php echo $t["_id"]; ?>/help/references.php" class="nav-link">
																							<span class="title"> References </span>
																							</a>
																						</li>
																						<?php } ?>
																				</ul>
																					</li>
																					<?php } ?>
																				</ul>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h7') { ?>active open<?php } ?>">
                                        <a href="help/hdesk.php" class="nav-link ">
                                            <span class="title">Helpdesk</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h8') { ?>active open<?php } ?>">
                                        <a href="help/related.php" class="nav-link ">
                                            <span class="title">Related Links</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h9') { ?>active open<?php } ?>">
                                        <a href="help/refs.php" class="nav-link ">
                                            <span class="title">References</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'h10') { ?>active open<?php } ?>">
                                        <a href="help/ackn.php" class="nav-link ">
                                            <span class="title">Acknowledgments</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
															<!--<li class="nav-item">
                                <a href="/forum/" target="blank" class="nav-link nav-toggle">
                                    <i class="icon-users"></i>
                                    <span class="title">Forum</span>
                                </a>
                            </li>-->
                            <li>
                                <li class="nav-item <?php if($currentSection == 'hd') { ?>active open<?php } ?>">
                                <a href="/helpdesk/" class="nav-link nav-toggle">
                                    <i class="icon-earphones"></i>
                                    <span class="title">Helpdesk</span>
                                </a>
                            </li>
														
							<!--<?php if(allowedRoles($_SESSION['User']['Type'], $GLOBALS['NO_GUEST'])){ ?>
                            <li class="nav-item  <?php if($currentSection == 'up') { ?>active open<?php } ?>">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">User</span>
									<?php if($currentSection == 'up') { ?><span class="selected"></span><?php } ?>
                                    <span class="arrow <?php if($currentSection == 'up') { ?>open<?php } ?>"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item  <?php if($currentSubSection == 'mp') { ?>active open<?php } ?>">
                                        <a href="user/usrProfile.php" class="nav-link ">
                                            <span class="title">My Profile</span>
                                        </a>
									</li>
								</ul>
                            </li>
                            <?php } ?>
                            -->
							<?php if(allowedRoles($_SESSION['User']['Type'], $GLOBALS['ADMIN']) || allowedRoles($_SESSION['User']['Type'], $GLOBALS['TOOLDEV'])){ ?>
                            <li class="nav-item  <?php if($currentSection == 'ad') { ?>active open<?php } ?>">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-settings"></i>
                                    <span class="title">Admin</span>
									<?php if($currentSection == 'up') { ?><span class="selected"></span><?php } ?>
                                    <span class="arrow <?php if($currentSection == 'ad') { ?>open<?php } ?>"></span>
                                </a>
																<ul class="sub-menu">
																		<?php if(!allowedRoles($_SESSION['User']['Type'], $GLOBALS['TOOLDEV'])){ ?>
                                    <li class="nav-item  <?php if($currentSubSection == 'ds') { ?>active open<?php } ?>">
                                        <a href="admin/dashboard.php" class="nav-link ">
                                            <span class="title">Dashboard</span>
                                        </a>
																		</li>
																		<?php } ?>
																		<?php if(!allowedRoles($_SESSION['User']['Type'], $GLOBALS['TOOLDEV'])){ ?>
                                    <li class="nav-item  <?php if($currentSubSection == 'au') { ?>active open<?php } ?>">
                                        <a href="admin/adminUsers.php" class="nav-link ">
                                            <span class="title">Users Administration</span>
                                        </a>
																		</li>
																		<?php } ?>
                                    <li class="nav-item  <?php if($currentSubSection == 'at') { ?>active open<?php } ?>">
                                        <a href="admin/adminTools.php" class="nav-link ">
                                            <span class="title">Tool Administration</span>
                                        </a>
																		</li>
																		<li class="nav-item  <?php if($currentSubSection == 'jv') { ?>active open<?php } ?>">
                                        <a href="admin/jsonValidator.php" class="nav-link ">
                                            <span class="title">JSON Validator</span>
                                        </a>
                                    </li>
								</ul>
                            </li>
							<?php } ?>

													<li class="nav-item active open beta-long" style="color:#b4bcc8;margin-left:18px;margin-top:10px;font-size:12px;">This is a BETA version of MuG VRE</li>
													<li class="nav-item active open beta-short" style="color:#b4bcc8;margin-left:8px;margin-top:10px;font-size:12px;display:none;">BETA</li>
											
                        </ul>
                        <!-- END SIDEBAR MENU -->
												<!-- END SIDEBAR MENU -->

                    </div>
                    <!-- END SIDEBAR -->
                </div>
                <!-- END SIDEBAR -->
