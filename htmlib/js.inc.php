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
	case 'index':
	case 'index2':
	case 'dashboard':
	case 'uploadForm':
	case 'uploadForm2':
	case 'editFile':
	?>
	<script src="/htmlib/globals.js.inc.php"></script>
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
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home'){ ?>	
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
			case 'repositoryList': ?>
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
			case 'uploadForm': ?>
			<script src="assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>	
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
			<?php break; 
			case 'dataFromTxt': ?>
			<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
			<?php break; 
			case 'uploadForm2':
			case 'editFile': ?>
			  <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			  <script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
			<?php break; 
			case 'dataFromID': ?>
			  <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
			  <script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
			<?php break;
			case 'adminUsers': ?>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<?php break;
			case 'adminTools': ?>
			<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        	<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
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
				<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/pydockdna'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="tools/pydockdna/assets/js/input.js" type="text/javascript"></script>
				<script src="assets/pages/scripts/ngl-home.js" type="text/javascript"></script>	
				<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/nucldynwf'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
				<script src="tools/nucldynwf/assets/js/input.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/naflex'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
				<script src="tools/naflex/assets/js/input.js" type="text/javascript"></script>
				<script src="assets/pages/scripts/ngl-home.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/tadbit'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
				<script src="tools/tadbit/assets/js/input.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/dnadyn'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
				<script src="tools/dnadyn/assets/js/input.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/chromatindyn'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
				<script src="tools/chromatindyn/assets/js/input.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/minimizedStruct'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="tools/minimizedStruct/assets/js/input.js" type="text/javascript"></script>
				<script src="assets/pages/scripts/ngl-home.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/dnashape'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="tools/dnashape/assets/js/input.js" type="text/javascript"></script>
				<?php } elseif (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/pdiview'){ ?>
				<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="tools/pdiview/assets/js/input.js" type="text/javascript"></script>
				<script src="assets/pages/scripts/ngl-home.js" type="text/javascript"></script>
				<?php } ?>

			<?php break;
			case 'output': ?>
				<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'tools/pydockdna'){ ?>
				<script src="assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="assets/global/scripts/datatable.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
				<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<script src="tools/pydockdna/assets/js/output.js" type="text/javascript"></script>
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
				<?php } ?>
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
			<script src="assets/pages/scripts/resetPassword.js" type="text/javascript"></script>	
			<?php break; 
			case 'index2': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>		
			<script src="assets/pages/scripts/datatables-page.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/components-knob-dials.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/run-tools.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/ngl-home.js" type="text/javascript"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/0.7.24/webcomponents-lite.min.js"></script>
    	<link rel="import" href="visualizers/tadkit/tadkit-viewer/dist/tadkit-viewer.html">
			<script src="assets/pages/scripts/tadkit-home.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/actions-home.js" type="text/javascript"></script>
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home'){ ?>		
            <script src="assets/pages/scripts/portfolio.js" type="text/javascript"></script>
            <?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'helpdesk'){ ?>
            <script src="assets/pages/scripts/helpdesk.js" type="text/javascript"></script>
			<?php } else { ?>
			<script src="assets/pages/scripts/login.js" type="text/javascript"></script>
			<?php } ?>
			<?php break; 
			case 'index': ?>
			<?php if(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace'){ ?>		
			<script src="assets/pages/scripts/datatables-page.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/components-knob-dials.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/run-tools.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/ngl-home.js" type="text/javascript"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/0.7.24/webcomponents-lite.min.js"></script>
    	<link rel="import" href="visualizers/tadkit/tadkit-viewer/dist/tadkit-viewer.html">
			<script src="assets/pages/scripts/tadkit-home.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/actions-home.js" type="text/javascript"></script>
			<?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home'){ ?>		
            <script src="assets/pages/scripts/portfolio.js" type="text/javascript"></script>
            <?php } elseif(dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'helpdesk'){ ?>
            <script src="assets/pages/scripts/helpdesk.js" type="text/javascript"></script>
			<?php } else { ?>
			<script src="assets/pages/scripts/login.js" type="text/javascript"></script>
			<?php } ?>
			<?php break; 
			case 'lockScreen': ?>	
			<script src="assets/pages/scripts/lock.js" type="text/javascript"></script>	
			<?php break; 
			case 'usrProfile': ?>
			<script src="assets/pages/scripts/profile.js" type="text/javascript"></script>
			<?php break; 
			case 'repositoryList': ?>
			<script src="assets/pages/scripts/table-repository.js" type="text/javascript"></script>	
			<?php break;
			case 'uploadForm': ?>	
			<script src="assets/pages/scripts/form-dropzone.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/form-down-remotefile.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/form-validateinput.js" type="text/javascript"></script>
			<?php break;
			case 'editFile': 
			?>	
			<script src="assets/pages/scripts/form-validatefiles.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/get-taxon-id.js" type="text/javascript"></script>
			<?php break;
			case 'uploadForm2': 
			?>	
			<script src="assets/pages/scripts/form-validatefiles.js" type="text/javascript"></script>
			<script src="assets/pages/scripts/get-taxon-id.js" type="text/javascript"></script>
			<?php break;
			case 'dataFromTxt':
			?>	
			<script src="assets/pages/scripts/form-validateinput.js" type="text/javascript"></script>
			<?php break;
			case 'dataFromID':
			?>
			<script src="assets/pages/scripts/pdb-typeahead.js" type="text/javascript"></script>	
			<script src="assets/pages/scripts/form-validateinput.js" type="text/javascript"></script>
			<?php break;
			case 'adminUsers': ?>
			<script src="assets/pages/scripts/table-datatables-editable.js" type="text/javascript"></script>	
			<?php break; 
			case 'adminTools': ?>
			<script src="assets/pages/scripts/adminTools.js" type="text/javascript"></script>	
			<?php break; 
			case 'dashboard': ?>
			<script src="assets/pages/scripts/dashboard.js" type="text/javascript"></script>	
			<?php break;?>
		<?php } ?>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'index2': 				
			case 'index': 
			case 'home': 
			case 'help1': 
			case 'repositoryList':
			case 'experiment':
			case 'usrProfile':
			case 'uploadForm':
			case 'uploadForm2': 
			case 'editFile': 
			case 'adminUsers': 
			case 'newUser': 
			case 'editUser':
			case 'adminTools':
			case 'dashboard':
			case 'dataFromTxt':
			case 'dataFromID':
			case 'input':
			case 'output': 
			case 'loading_output':
			?>
			<script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
			<script src="assets/layouts/layout/scripts/main.js" type="text/javascript"></script>
			<?php break; ?>
		<?php } ?>
		<!-- END THEME LAYOUT SCRIPTS -->
		<?php
		switch(pathinfo($_SERVER['PHP_SELF'])['filename']){
			case 'home': 
			case 'help1': 
			case 'repositoryList':
			//case 'experiment':
			//case 'usrProfile':
			//case 'uploadForm':
			case 'adminUsers': 
			case 'adminTools':?>
        		<script src="assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
			<?php break; 
			case 'dashboard':
			case 'dataFromTxt':
			case 'dataFromID':
			case 'input':
			case 'output': ?>
			<script src="assets/pages/scripts/cookie.js" type="text/javascript"></script>
			<?php break; 
			case 'index2': 
			case 'index': 
				if((dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'workspace') || (dirname($_SERVER['PHP_SELF']) == $GLOBALS['BASEURL'].'home')){ ?>
					<script src="assets/pages/scripts/cookie.js" type="text/javascript"></script>
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

