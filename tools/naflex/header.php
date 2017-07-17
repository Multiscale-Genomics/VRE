<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
		<title>Multiscale Complex Genomics | Virtual Research Environment</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
					<link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
	        <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
			        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
					<link href="/assets/pages/css/customized-tools.css" rel="stylesheet" type="text/css" />
					<link rel="stylesheet" type="text/css" href="http://mmb.irbbarcelona.org/NAFlex2/css/estil_NA.css" />

<link rel="stylesheet" type="text/css" href="http://mmb.irbbarcelona.org/NAFlex2/css/slider.css" />
	
			        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
				
			<link href="/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        	<link href="/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />			
			        <link href="/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="icon" href="/assets/layouts/layout/img/icon.png" sizes="32x32" />
    </head>
    <!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid">
  <div class="page-wrapper">

  
<!-- BEGIN HEADER -->
            <div class="page-header navbar navbar-fixed-top">
                <!-- BEGIN HEADER INNER -->
                <div class="page-header-inner ">
                    <!-- BEGIN LOGO -->
                    <div class="page-logo">
                        <a href="/workspace/">
                            <img src="/assets/layouts/layout/img/logo.png" alt="logo" class="logo-default" />
                        </a>
                        <div class="menu-toggler sidebar-toggler">
                            <span></span>
                        </div>
                    </div>
                    <!-- END LOGO -->
                    <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
                    <!-- END RESPONSIVE MENU TOGGLER -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <img alt="" class="img-circle display-hide" id="avatar-with-picture" src="" />
									<div class="img-circle" id="avatar-no-picture" style="background-color:#ffb858">TY</div>
                                    <span class="username username-hide-on-mobile">Test</span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
									                                    <li>
                                        <a href="/user/usrProfile.php">
                                            <i class="glyphicon glyphicon-user"></i> My Profile </a>
									</li>
																		<li>
                                        <a href="/admin/dashboard.php">
                                            <i class="glyphicon glyphicon-dashboard"></i> Dashboard </a>
									</li>
									                                    <li class="divider"> </li>
                                    <li>
                                        <a href="/user/lockScreen.php">
                                            <i class="fa fa-lock" style="font-size:16px;"></i> Lock Screen </a>
                                    </li>
									                                    <li>
                                        <a id="logout-button" href="javascript:;">
                                            <i class="fa fa-power-off" style="font-size:16px;"></i> Log Out </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- END USER LOGIN DROPDOWN -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END HEADER INNER -->
            </div>
            <!-- END HEADER -->
  

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

                            <li class="nav-item  active open">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-home"></i>
                                    <span class="title">User Workspace</span>
                                    <span class="selected"></span>                                    <span class="arrow open"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item active open">
                                        <a href="/workspace/" class="nav-link ">
                                            <span class="title">User Data</span>
                                        </a>
                                    </li>
                                    <li class="nav-item ">
                                        <a href="/handlefiles/uploadForm.php" class="nav-link ">
                                            <span class="title">Upload Files</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-notebook"></i>
                                    <span class="title">Repository</span>
									                                    <span class="arrow "></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item  ">
                                        <a href="/repository/repositoryList.php" class="nav-link ">
                                            <span class="title">List of Experiments</span>
                                        </a>
                                    </li>                                    
                                </ul>
                            </li>
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-link"></i>
                                    <span class="title">External Links</span>
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item  ">
                                        <a href="http://www.multiscalegenomics.eu/MuGVRE/modules/BigNASimMuG/" target="_blank" class="nav-link ">
                                            <span class="title">BigNASim</span>
                                        </a>
                                    </li>
									<li class="nav-item  ">
                                        <a href="http://mmb.irbbarcelona.org/NucleosomeDynamics/" target="_blank" class="nav-link ">
                                            <span class="title">Nucleosome Dynamics</span>
                                        </a>
                                    </li>
                                    <li class="nav-item  ">
                                        <a href="http://www.multiscalegenomics.eu/MuGVRE/flexibility-browser/" target="_blank" class="nav-link ">
                                            <span class="title">Flexibility Browser</span>
                                        </a>
                                    </li>
                                    <li class="nav-item  ">
                                        <a href="http://sgt.cnag.cat/3dg/tadkit/" target="_blank" class="nav-link ">
                                            <span class="title">TADKit 3D genome visualizer</span>
                                        </a>
                                    </li>
                                    <li class="nav-item  ">
                                        <a href="http://www.multiscalegenomics.eu/MuGVRE/modules/ConnectivityBrowser/" target="_blank" class="nav-link ">
                                            <span class="title">MuG Information Network</span>
                                        </a>
                                    </li>
                                </ul>
							</li>
							<li class="nav-item">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-users"></i>
                                    <span class="title">Forum</span>
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item">
                                        <a href="/forum/" target="_blank"  class="nav-link ">
                                            <span class="title">Go to Forum</span>
                                        </a>
									</li>
								</ul>
							</li>
							<li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-question"></i>
                                    <span class="title">Help</span>
									                                    <span class="arrow "></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item  ">
                                        <a href="/help/help1.php" class="nav-link ">
                                            <span class="title">Help Page 1</span>
                                        </a>
                                    </li>
                                    <li class="nav-item  ">
                                        <a href="javascript:;" class="nav-link ">
                                            <span class="title">Help Page 2</span>
                                        </a>
                                    </li>
                                    <li class="nav-item  ">
                                        <a href="javascript:;" class="nav-link ">
                                            <span class="title">Help Page 3</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
							                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">User</span>
									                                    <span class="arrow "></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item  ">
                                        <a href="/user/usrProfile.php" class="nav-link ">
                                            <span class="title">My Profile</span>
                                        </a>
									</li>
								</ul>
                            </li>
														                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-settings"></i>
                                    <span class="title">Admin</span>
									                                    <span class="arrow "></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item  ">
                                        <a href="/admin/dashboard.php" class="nav-link ">
                                            <span class="title">Dashboard</span>
                                        </a>
									</li>
                                    <li class="nav-item  ">
                                        <a href="/admin/adminUsers.php" class="nav-link ">
                                            <span class="title">Users Administration</span>
                                        </a>
                                    </li>
								</ul>
                            </li>
							
                        </ul>
                        <!-- END SIDEBAR MENU -->
                        <!-- END SIDEBAR MENU -->
                    </div>
                    <!-- END SIDEBAR -->
                </div>
                <!-- END SIDEBAR -->

<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <span>User Workspace</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Tools</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>NAFlex</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Results
                            <small>Nucleic Acids Flexibility</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
