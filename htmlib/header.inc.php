<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
		<title><?php echo $GLOBALS['SITETITLE']; ?></title>
			<base href="<?php echo $GLOBALS['BASEURL']; ?>" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
		<?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'index': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>
        <link href="assets/pages/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/pages/css/treeTable.dataTables.css" rel="stylesheet" type="text/css" />
				<link href="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />	
				<link href="assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />	
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home'){ ?>
				<link href="assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
			<?php } else { ?>
				<link href="assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        	<link href="assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
			<?php } ?>
			<?php break; 
			case 'usrProfile': ?>
			<link href="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
			<?php break; 
			case 'dataFromID': ?>
			<link href="assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css" />
			<?php break; 
			case 'uploadForm': ?>
			<link href="assets/global/plugins/dropzone/dropzone.min.css" rel="stylesheet" type="text/css" />
	        <link href="assets/global/plugins/dropzone/basic.min.css" rel="stylesheet" type="text/css" />
			<?php break; 
			case 'adminUsers':
			case 'dashboard':
			case 'repositoryList': ?>
			<link href="assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
	        <link href="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
			<?php break;
			case 'output': ?>
				<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/pydock'){ ?>
				<link href="assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
	        <link href="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/naflex'){ ?>
				<link href="tools/naflex/css/styles.css" rel="stylesheet" type="text/css" />
				<?php } ?>
			<?php break;
			case 'input': ?>
				<?php if (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/naflex'){ ?>
				<link href="assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
					<link href="assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/nucldynwf'){ ?>
				<link href="assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        	<link href="assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/tadbit'){ ?>
				<link href="assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css" />
				<link href="assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        	<link href="assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
				<?php } ?>

			<?php break ?>;

		<?php } ?>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
		<?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'resetPassword':
			case 'index': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>	
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home'){ ?>
			<link href="assets/pages/css/portfolio.min.css" rel="stylesheet" type="text/css" />
			<?php } else { ?>
			<link href="assets/pages/css/login.min.css" rel="stylesheet" type="text/css" />
			<?php } ?>
			<?php break; 
			case 'lockScreen': ?>		
        	<link href="assets/pages/css/lock.min.css" rel="stylesheet" type="text/css" />
			<?php break; 
			case 'usrProfile': ?>
			<link href="assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />	
			<?php break; 
			case 'input':
			case 'output': ?>
			<link href="assets/pages/css/customized-tools.css" rel="stylesheet" type="text/css" />	
			<?php break; ?>
		<?php } ?>
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
		<?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'index':
			case 'home': 
			case 'help1': 	
			case 'usrProfile':
			case 'uploadForm': 
			case 'uploadForm2':
			case 'editFile':
			case 'adminUsers':
			case 'dashboard':
			case 'repositoryList':
			case 'experiment':
			case 'dataFromTxt':
			case 'dataFromID':
			case 'input':
			case 'output':
			case 'loading_output':
	?>		
			<link href="assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        	<link href="assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />			
			<?php break; ?>
		<?php } ?>
        <link href="assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
				<link rel="icon" href="assets/layouts/layout/img/icon.png" sizes="32x32" />
		
    </head>
    <!-- END HEAD -->
