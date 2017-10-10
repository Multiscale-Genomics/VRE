// delete files / folders
var fileName = '';
var option = '';

var baseURL = $('#base-url').val();

function deleteFile(file){
  $('#modalDelete .modal-body').html('Are you sure you want to delete the selected file?');
  $('#modalDelete').modal({ show: 'true' });
  fileName = file;
  option = 'deleteSure';
}

function deleteFolder(folder){
  $('#modalDelete .modal-body').html('Are you sure you want to delete the selected folder and <strong>ALL</strong> its content?');
  $('#modalDelete').modal({ show: 'true' });
  fileName = folder;
  option = 'deleteDirOk';
}

function deleteAllFiles(){
  $('#modalDelete .modal-body').html('Are you sure you want to delete <strong>ALL</strong> the selected files?');
  $('#modalDelete').modal({ show: 'true' });
  option = 'deleteAll';
}

function downloadAllFiles(){
	option = 'downloadAll';
	var fn = "&";
	for(i in allFiles){
		if(allFiles[i].checked) {
			fn += 'fn[]=' + allFiles[i].fileId + '&';
		}
	}
	fn = fn.slice(0, -1);

	location.href= baseURL + "workspace/workspace.php?op=" + option + fn;

}

/*function viewFolderMeta(id, name){

	$.ajax({
		type: "POST",
		url: baseURL + "applib/getMetaWS.php",
		data: "id=" + id + "&type=0", 
		success: function(data) {
			$('#modalMeta .modal-header .modal-title').html(name.toUpperCase() + ' Job Info');
			$('#modalMeta .modal-body #meta-summary').html(data);
			$(".tooltips").tooltip();

			$('#modalMeta').modal({ show: 'true' });

		}
	});

}*/

function viewFileMeta(id, name, type){
	
	var txtID = '';
	/*if(type == 1) var txtID = 'File';
	else  var txtID = 'Job';*/

	$.ajax({
		type: "POST",
		url: baseURL + "applib/getMetaWS.php",
		data: "id=" + id + "&type=" + type, 
		success: function(data) {
			$('#modalMeta .modal-header .modal-title').html(name.toUpperCase() + ' ' + txtID + ' Info');
			$('#modalMeta .modal-body #meta-summary').html(data);
			$(".tooltips").tooltip();

			$('#modalMeta').modal({ show: 'true' });

		}
	});

}

// Open modal with analysis parameters
callShowSHfile = function(tool, sh) {

	$('#modalAnalysis').modal('show');
	$('#modalAnalysis .modal-body').html('Loading data...');

	$.ajax({
		type: "POST",
		url: baseURL + "applib/showSHfile.php",
		data: "fn=" + sh + "&tool=" + tool, 
		success: function(data) {
			$('#modalAnalysis .modal-body').html(data);
		}
	});

}

toggleVis = function(layer) {
	$('#' + layer).slideToggle();
}


runTool = function(tool) {
	var query = "";
	for(i in allFiles){
		if(allFiles[i].checked) {
			query += 'fn[]=' + allFiles[i].fileId + '&';
		}
	} 
	query = query.slice(0, -1);
	location.href = baseURL + "tools/" + tool + "/input.php?" + query;
}

runVisualizer = function(tool, user) {
	var query = "user=" + user + "&";
	for(i in allFiles){
		if(allFiles[i].checked) {
			query += 'fn[]=' + allFiles[i].fileId + '&';
		}
	} 
	query = query.slice(0, -1);

	var target = (tool != 'tadkit' ? 'childWindow': '_blank');

	window.open(baseURL + "visualizers/" + tool + "/?" + query, target);

}
	
viewResults = function(project, tool) {
		
	App.blockUI({
				boxed: true,
		message: 'Creating tool output, this operation may take a while, please don\'t close the tab...'
			});

	$.ajax({
		type: "POST",
		url: "/applib/loadOutput.php",
		data:"project=" + project + "&tool=" + tool,
		success: function(data) {
			//d = data.replace(/(\r\n|\n|\r|\t)/gm,"");
			/*if(d == '1'){
				setTimeout(function(){ location.href = '/'; }, 1000);	
			}else{
				App.unblockUI();
			}*/
			if(data == '1'){
				setTimeout(function(){ location.href = 'tools/' + tool + '/output.php?project=' + project; }, 500);	
			}		
		}
	});

};

editAllFiles = function() {
	var query = "";
	for(i in allFiles){
		if(allFiles[i].checked) {
			query += 'fn[]=' + allFiles[i].fileId + '&';
		}
	} 
	query = query.slice(0, -1);
	location.href = baseURL + "getdata/uploadForm2.php?" + query;
}

$(document).ready(function() {

	$('#modalDelete').find('.modal-footer .btn-modal-del').on('click', function(){
		$('#modalDelete').find('.modal-footer .btn-modal-del').prop('disabled', true);
		$('#modalDelete').find('.modal-footer .btn-modal-del').html('Deleting...');

		if((option == 'deleteSure') || (option == 'deleteDirOk')) {
			var fn = "&fn=" + fileName;
		} else if(option == 'deleteAll'){
			var fn = "&";
			for(i in allFiles){
				if(allFiles[i].checked) {
					fn += 'fn[]=' + allFiles[i].fileId + '&';
				}
			}
			fn = fn.slice(0, -1);
		}

		$.ajax({
			type: "GET",
			url: baseURL + "workspace/workspace.php",
			data: "op=" + option + fn, 
			success: function(data) {
				$('#modalDelete').modal('toggle');	
				location.href= baseURL + "workspace/";
			}
		});

	});

});
