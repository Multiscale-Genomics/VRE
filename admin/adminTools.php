<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";

redirectToolDevOutside();

#
# find available tools - by user

$tools  = array();

$result = array(); 
/*if ($_SESSION['User']['Type'] == 0)
	$result = $GLOBALS['toolsCol']->find(array());
else
	$result = $GLOBALS['toolsCol']->find(array("owner.user"=>$_SESSION['User']['_id']));*/

switch($_SESSION['User']['Type']) {

	case 0: $result = $GLOBALS['toolsCol']->find(array());
					break;

	case 1: $GLOBALS['toolsCol']->find(array("_id" => array('$in' => $_SESSION['User']['ToolsDev'])));
					break;

	default: redirect($GLOBALS['URL']);

}

if ($_SESSION['User']['Type'] == 0){
	$result = $GLOBALS['toolsCol']->find(array());
}else{
        if ($_SESSION['User']['ToolsDev']){
            $result = $GLOBALS['toolsCol']->find(array("_id" => array('$in' => $_SESSION['User']['ToolsDev'])));
        }else{
            $_SESSION['errorData']['Error'][]="You have no tool ownership associated. Please, contact <a href=\"mailto:".$GLOBALS['helpdeskMail']."\">us</a>";
               $result = $GLOBALS['toolsCol']->find(array());
        }
}


foreach (array_values(iterator_to_array($result)) as $v){
	# get tool json
	$toolId = $v['_id'];
	$tools[$toolId]['json']  = $v;

	# get tool statistics
	$tools[$toolId]['stats'] = "";
	$stats_tool = get_statistics_tool($toolId);

	foreach ($stats_tool as $k => $v)
		$tools[$toolId]['stats'].="$k:&nbsp;$v<br/>";
}

$toolsList = $tools;

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
                                  <span>Admin</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Admin Tools</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Tool Administration
                            <small>configure & test</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="portlet light portlet-fit bordered">

                                    <div class="portlet-body">
                                        <input type="hidden" id="base-url" value="<?php echo $GLOBALS['BASEURL']; ?>" />

                                        <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                                            <thead>
                                                <tr>
                                                    <th>Tool Id</th>
                                                    <th>Configuration</th>
                                                    <th>Integration</th>
                                                    <th>Statistics</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
		<?php

		foreach($toolsList as $toolId => $v){
		?>
			<tr>
				<td><?php echo $toolId;?></td>
				<td>
					<a href="javascript:callShowToolJson('<?php echo $toolId;?>')">View</a>
				</td>
				<td>
					&#10003; Valid JSON: &nbsp; <a href="javascript:#">Check</a><br/>
					Check against schema, check queue and wrapper<br/>
					&#10003; Input form: &nbsp; <a href="javascript:#">Check</a><br/> 
					Check if exists. If so, check name and type matching for inputs and arguments<br/> 
					&times; Execution: &nbsp; <a href="javascript:#">Check</a><br/> 
					Open fake form and launch<br/> 
					&times; Custom visualizer <a href="javascript:#">Check</a><br/> 
					Has TAR or "tool_statistics"
				</td>
				<td><?php echo $v['stats'];?></td>
				<td>
					<a href="javascript:#">Disable</a><br/>
					<a href="javascript:#">Edit JSON</a><br/>
					<a href="javascript:#">Run Test</a>
				</td>
			</tr>
		<?php
		}
		?>

					    </tbody>
                                       </table>
				</div>


                                <!-- END EXAMPLE TABLE PORTLET-->
                            </div>
                        </div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

		<div class="modal fade bs-modal" id="modalAnalysis" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Execution Summary</h4>
                            </div>
							<div class="modal-body table-responsive"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>


<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
