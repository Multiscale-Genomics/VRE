<!--[if lt IE 9]>
<script src="assets/global/plugins/respond.min.js"></script>
<script src="assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<?php
switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
	case 'adminUsers':
	case 'newUser':
	case 'editUser':
	case 'adminTools':
	case 'adminJobs':
	case 'myNewTools':
	case 'newTool':
	case 'vmURL':
	case 'createTest':
	case 'index':
	case 'index2':
	case 'dashboard':
	case 'uploadForm':
	case 'uploadForm2':
	case 'editFile':
	case 'editFile2':
	?>
	<script src="htmlib/globals.js.inc.php"></script>
	<?php break; 
}
?>
        <!-- BEGIN CORE PLUGINS -->
        <script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
				<!-- BEGIN PAGE LEVEL PLUGINS -->
				<script src="assets/global/plugins/jquery-cookiebar/jquery.cookieBar.min.js" type="text/javascript"></script>
		<?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'index2': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>	
			<script src="assets/global/scripts/jquery.dataTables.min.js" type="text/javascript"></script>
			<!--<script src="assets/global/scripts/dataTables.treeTable.js" type="text/javascript"></script>
			<script src="assets/global/scripts/jquery.treetable.js" type="text/javascript"></script>-->
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
			<script src="assets/global/plugins/ngl.last.js" type="text/javascript"></script>
			<script src="assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home'){ ?>	
				<script src="assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
			<?php } else { ?>
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			<?php } ?>
			<?php break; 
			case 'resetPassword':
			case 'index': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>	
			<script src="assets/global/scripts/jquery.dataTables.min.js" type="text/javascript"></script>
			<!--<script src="assets/global/scripts/dataTables.treeTable.js" type="text/javascript"></script>
			<script src="assets/global/scripts/jquery.treetable.js" type="text/javascript"></script>-->
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
			<script src="assets/global/plugins/ngl.last.js" type="text/javascript"></script>
			<script src="assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/clipboardjs/clipboard.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'launch'){ ?>	
			<script src="assets/global/scripts/jquery.dataTables.min.js" type="text/javascript"></script>
			<!--<script src="assets/global/scripts/dataTables.treeTable.js" type="text/javascript"></script>
			<script src="assets/global/scripts/jquery.treetable.js" type="text/javascript"></script>-->
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			<?php } elseif((dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home') || (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'publicsite')){ ?>	
				<script src="assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
			<?php } else { ?>
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			<?php } ?>
			<?php break; 
			case 'lockScreen': ?>		
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<?php break; 
			case 'repositoryList':
			case 'logs':
			case 'bignasimList': ?>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
    		<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
	        <script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<?php break; 	
			case 'usrProfile': ?>
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
			<script src="assets/global/plugins/clipboardjs/clipboard.min.js" type="text/javascript"></script>
			<?php break; 	
			case 'restoreLink': ?>
			<script src="assets/global/plugins/clipboardjs/clipboard.min.js" type="text/javascript"></script>
			<?php break; 	
			case 'listOfProjects': ?>
			<script src="assets/global/scripts/jquery.dataTables.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			<?php break; 
			case 'uploadForm': ?>
			<script src="assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>	
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
			<?php break; 
			case 'dataFromTxt': ?>
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
			<?php break; 
			case 'uploadForm2':
			case 'editFile':
			case 'editFile2':?>
			  <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			  <script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			<?php break; 
			case 'dataFromID': ?>
			  <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			  <script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
			<?php break; 
			case 'sampleDataList': ?>
			  <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<?php break;
			case 'adminUsers': ?>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<?php break;
			case 'editUser': ?>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			<?php break;
			case 'adminJobs':
			case 'myNewTools': ?>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
            <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
			<?php break; 
            case 'adminTools': ?>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
            <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
			<script src="assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/flot/jquery.flot.threshold.min.js" type="text/javascript"></script>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
			<?php break; 
			case 'newTool':
			case 'vmURL':
			case 'createTest':
			?>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
			  <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<?php break;
			case 'dashboard': ?>
			<script src="assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/flot/jquery.flot.threshold.min.js" type="text/javascript"></script>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
			<?php break;
			case 'input':?>
				<?php if(strrpos(dirname($_SERVER['PHP_SELF']), "tools")){ ?>
				<script src="assets/pages/scripts/tool-input.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
				<script src="tools/<?php echo $toolId; ?>/assets/js/input.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<?php } elseif(strrpos(dirname($_SERVER['PHP_SELF']), "visualizers")){ ?>
				<script src="assets/pages/scripts/visualizer-input.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<!--<script src="visualizers/<?php echo $toolId; ?>/assets/js/input.js?v=<?php echo rand(); ?>" type="text/javascript"></script>-->
				<?php } ?>
			<?php break;
			case 'output': ?>
				<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/pydockdna'){ ?>
				<script src="assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<script src="tools/pydockdna/assets/js/output.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/pydock'){ ?>
				<script src="assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<script src="tools/pydock/assets/js/output.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/naflex'){ ?>
				<script type="text/javascript" src="tools/naflex/js/sequenceSelection.js"></script>
				<script type="text/javascript" src="tools/naflex/js/NA_Checks.js"></script>
				<script type="text/javascript" src="tools/naflex/js/sortable.js"></script>
				<script type="text/javascript" src="tools/naflex/NUCMD/jsmol/JSmol.min.nojq.js"></script>
				<script type="text/javascript" src="tools/naflex/NUCMD/js/jmolScripts.js"></script>
				<script type="text/javascript" src="tools/naflex/NUCMD/js/imagePreview.js"></script>
				<script type="text/javascript" src="tools/naflex/NUCMD/js/jqueryImages/jqueryImages.js"></script>
				<script type="text/javascript">
				$(document).ready( function() {
				<?php if($analysisType == 'PCAZIP') { ?> $("#insertJmol").html(Jmol.getAppletHtml("jmol",Info)); <?php } ?>
						imagePreview();
				});
				</script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/dnadyn'){ ?>
				<script src="assets/global/plugins/ngl.last.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/chromatindyn'){ ?>
				<script src="assets/global/plugins/ngl.last.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/minimizedStruct'){ ?>
				<script src="assets/global/plugins/ngl.last.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/nucldynwf'){ ?>
				<script src="assets/global/scripts/jquery.dataTables.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/fixedColumns/dataTables.fixedColumns.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<script src="tools/nucldynwf/assets/js/output.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/nucldynwf_pmes'){ ?>
				<script src="assets/global/scripts/jquery.dataTables.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/fixedColumns/dataTables.fixedColumns.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<script src="tools/nucldynwf_pmes/assets/js/output.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<?php } ?>
			<?php break;
			case 'help':
			case 'method':
			case 'inputs':
			case 'outputs':
			case 'results':
			case 'tutorials':
			case 'references':?>
			<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js"></script>
			<script src="assets/global/plugins/markdown/marked.min.js" type="text/javascript"></script>
			<script src="assets/global/plugins/markdown/bootstrap-markdown-editor.js" type="text/javascript"></script>
			<?php break;
			case 'jsonSpecValidator':
			case 'jsonTestValidator':	 ?>
			<script src="assets/global/plugins/codemirror/lib/codemirror.js" type="text/javascript"></script>
				<script src="assets/global/plugins/codemirror/addon/edit/matchbrackets.js"></script>
				<script src="assets/global/plugins/codemirror/addon/display/placeholder.js"></script>
        <script src="assets/global/plugins/codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
				<script src="assets/global/plugins/codemirror/lib/jsonlint.js"></script>
				<script src="assets/global/plugins/codemirror/addon/lint/lint.js"></script>
				<script src="assets/global/plugins/codemirror/addon/lint/json-lint.js"></script>
			<?php break;
			case 'newProject':
			case 'editProject':?>
			  <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			<?php break;
			case 'tools':
			case 'visualizers':?>
				<script src="assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
			<?php break;?>
		<?php } ?>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
		<?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'resetPassword': ?>
			<script src="assets/pages/scripts/resetPassword.js?v=<?php echo rand(); ?>" type="text/javascript"></script>	
			<?php break; 
			case 'index2': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>		
			<script src="assets/pages/scripts/datatables-page.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/components-knob-dials.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/run-tools.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/ngl-home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/0.7.24/webcomponents-lite.min.js"></script>
    	<link rel="import" href="visualizers/tadkit/tadkit-viewer/dist/tadkit-viewer.html">
			<script src="assets/pages/scripts/tadkit-home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/actions-home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home'){ ?>	
							
            <script src="assets/pages/scripts/portfolio.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
            <?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'helpdesk'){ ?>
            <script src="assets/pages/scripts/helpdesk.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php } else { ?>
			<script src="assets/pages/scripts/login.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php } ?>
			<?php break; 
			case 'index': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>		
			<script src="assets/pages/scripts/datatables-page.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/components-knob-dials.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/run-tools.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/ngl-home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/0.7.24/webcomponents-lite.min.js"></script>
    	<link rel="import" href="visualizers/tadkit/tadkit-viewer/dist/tadkit-viewer.html">
			<script src="assets/pages/scripts/tadkit-home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/actions-home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/restore-link.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'launch'){ ?>	
			<script src="assets/pages/scripts/launch-tool.js" type="text/javascript"></script>
			<?php } elseif((dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home') || (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'publicsite')){ ?>		
						<script src="assets/pages/scripts/home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
            <script src="assets/pages/scripts/portfolio.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
            <?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'helpdesk'){ ?>
            <script src="assets/pages/scripts/helpdesk.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php } else { ?>
			<script src="assets/pages/scripts/login.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php } ?>
			<?php break; 
			case 'lockScreen': ?>	
			<script src="assets/pages/scripts/lock.js?v=<?php echo rand(); ?>" type="text/javascript"></script>	
			<?php break; 
			case 'usrProfile': ?>
			<script src="assets/pages/scripts/profile.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'repositoryList': ?>
			<script src="assets/pages/scripts/table-repository.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'bignasimList': ?>
			<script src="assets/pages/scripts/table-bignasim.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'experiment': ?>
			<script src="assets/pages/scripts/experiment.js?v=<?php echo rand(); ?>" type="text/javascript"></script>	
			<?php break; 	
			case 'logs': ?>
			<script src="assets/pages/scripts/table-logs.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'listOfProjects': ?>
			<script src="assets/pages/scripts/list-projects.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'uploadForm': ?>	
			<script src="assets/pages/scripts/form-dropzone.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/form-down-remotefile.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/form-validateinput.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'editFile':
			case 'editFile2':
			?>	
			<script src="assets/pages/scripts/form-validatefiles.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/get-taxon-id.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'uploadForm2': 
			?>	
			<script src="assets/pages/scripts/form-validatefiles.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/pages/scripts/get-taxon-id.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'dataFromTxt':
			?>	
			<script src="assets/pages/scripts/form-validateinput.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'dataFromID':
			?>
			<script src="assets/pages/scripts/pdb-typeahead.js?v=<?php echo rand(); ?>" type="text/javascript"></script>	
			<script src="assets/pages/scripts/form-validateinput.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'adminUsers': ?>
			<script src="assets/pages/scripts/table-datatables-editable.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'editUser': 
			?>	
			<script src="assets/pages/scripts/edit-user.js?v=<?php echo rand(); ?>" type="text/javascript"></script>	
			<?php break; 
			case 'adminTools': ?>
			<script src="assets/pages/scripts/adminTools.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'adminJobs': ?>
			<script src="assets/pages/scripts/adminJobs.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'myNewTools': ?>
			<script src="assets/pages/scripts/myNewTools.js?v=<?php echo rand(); ?>" type="text/javascript"></script>	
			<?php break; 
			case 'newTool': ?>
			<script src="assets/pages/scripts/newTool.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'vmURL': ?>
			<script src="assets/pages/scripts/vmURL.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'createTest': ?>
			<script src="assets/pages/scripts/createTest.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'dashboard': ?>
			<script src="assets/pages/scripts/dashboard.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'jsonTestValidator': ?>
			<script src="assets/pages/scripts/json-test-validator.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'jsonSpecValidator': ?>
			<script src="assets/pages/scripts/json-spec-validator.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'tools':
			case 'visualizers': ?>
			<script src="assets/pages/scripts/portfolio.tools.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'sampleDataList': ?>
				<script src="assets/pages/scripts/sample-data.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 	
			case 'restoreLink': ?>
			<script src="assets/pages/scripts/restore-link.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 	
			case 'newProject':
			case 'editProject': ?>
			<script src="assets/pages/scripts/new-project.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 	
			case 'input': ?>
			<script src="assets/pages/scripts/home.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break;
			case 'help':
			case 'method':
			case 'inputs':
			case 'outputs':
			case 'results':
			case 'tutorials':
			case 'references':?>
			<script src="assets/pages/scripts/help-editor.js?v=<?php echo rand(); ?>" type="text/javascript"></script>	
			<?php break;?>
		<?php } ?>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'index2': 				
			case 'index': 
			case 'home': 
			case 'repositoryList':
			case 'bignasimList':
			case 'experiment':
			case 'usrProfile':
			case 'restoreLink':
			case 'uploadForm':
			case 'uploadForm2': 
			case 'editFile':
			case 'editFile2':
			case 'adminUsers': 
			case 'newUser': 
			case 'newProject':
			case 'editProject':
			case 'listOfProjects':
			case 'editUser':
			case 'adminTools':
			case 'adminJobs':
			case 'myNewTools':
			case 'newTool':
			case 'vmURL':
			case 'createTest':
			case 'jsonSpecValidator':
			case 'jsonTestValidator':
			case 'dashboard':
			case 'dataFromTxt':
			case 'dataFromID':
			case 'input':
			case 'output': 
			case 'loading_output':

			case 'general':
			case 'starting':
			case 'upload':		
			case 'ws':
			case 'launch':
			case 'hdesk':
			case 'related':
			case 'refs':
			case 'ackn':
			case 'help':
			case 'method':
			case 'inputs':
			case 'outputs':
			case 'results':
			case 'tutorials':
			case 'references':
			case 'tools':
			case 'visualizers':
			case 'sampleDataList':
			case 'form':
			case 'logs':

			?>
			<script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
			<script src="assets/layouts/layout/scripts/main.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<script src="assets/layouts/layout/scripts/cookie-toolbar.js" type="text/javascript"></script>
			<?php break; ?>
		<?php } ?>
		<!-- END THEME LAYOUT SCRIPTS -->
		<?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'home': 
			case 'repositoryList':
			//case 'bignasimList':
			//case 'experiment':
			//case 'usrProfile':
			//case 'uploadForm':
			//case 'adminUsers': 
			//case 'adminTools':
			?>
        		<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<?php break; 
			case 'dashboard':
			case 'dataFromTxt':
			case 'dataFromID':
			case 'input':
			case 'output':

			case 'general':
			case 'starting':
			case 'upload':		
			case 'ws':
			case 'launch':
			case 'hdesk':
			case 'related':
			case 'refs':
			case 'ackn':
			case 'help':
			case 'method':
			case 'inputs':
			case 'outputs':
			case 'results':
			case 'tutorials':
			case 'references':
			case 'tools':
			case 'visualizers':
			?>
			<script src="assets/pages/scripts/cookie.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
			<?php break; 
			case 'index2': 
			case 'index': 
				if((dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace') || (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home')){ ?>
					<script src="assets/pages/scripts/cookie.js?v=<?php echo rand(); ?>" type="text/javascript"></script>
				<?php } ?>
			<?php break; ?>
		<?php } ?>


		<!-- GOOGLE ANALYTICS -->

		<script>

			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-92062634-1', 'auto');
			ga('send', 'pageview');

		</script>

		<!-- END GOOGLE ANALYTICS -->

    </body>

</html>

