var baseURL = $('#base-url').val();

var current_block = 0;
var feedback_from_file = [];

function showValidation(op) {
  var block = op.value;
  $("#summary" + current_block).fadeOut(300);
  $("#alert-message" + current_block).fadeOut(300);
  $('#feedback-file' + current_block).fadeOut(300);
  $("#formInputs" + current_block).fadeOut(300, function(){
  	$("#formInputs" + block).fadeIn(300);
  	$("#summary" + block).fadeIn(300);
  	$("#alert-message" + block).fadeIn(300);
	if(feedback_from_file.indexOf(parseInt(block)) != -1)$('#feedback-file' + block).fadeIn(300);
  	current_block = block;
  });

}

function formBlockState(state, block, input_type, id){
	if(state == 'disabled'){
		$(block + id).fadeOut();
	 	$(block + id + " " + input_type).prop('disabled', true);
	}else{
		$(block + id).fadeIn();
	 	$(block + id + " " + input_type).prop('disabled', false);
	}
}

function customfromFormat(op, id){

  var format = op;

  if (format == "BAM"){
	formBlockState('enabled', '#pairedTR', 'input', id);
	formBlockState('enabled', '#sortedTR', 'input', id);
    if($("#sortedTR" + id + " input[type='radio']:checked").val() == 'unsorted') $("#sortInfo" + id).fadeIn();
  }else{
    formBlockState('disabled', '#pairedTR', 'input', id);
	formBlockState('disabled', '#sortedTR', 'input', id);
    $("#sortInfo" + id).fadeOut();
  }

  if (format == "UNK" || format == "FASTA" /*|| format == "FASTQ"*/ || format == "TXT" || format == "PDB" || format == "DCD" || format == "GRO" || format == "JSON"){
	formBlockState('disabled', '#refGenomeTR', 'select', id);
  }else{
	formBlockState('enabled', '#refGenomeTR', 'select', id);
  }

  /*if (format == "WIG" || format == "BEDGRAPH"){
    $("#formatInfo" + id).fadeIn();
  }else{
  	$("#formatInfo" + id).fadeOut();
  }*/

}

function showHideSortInfo(op, id){

  if(op == 1){
    	$("#sortInfo" + id).fadeIn();
	}else{
		$("#sortInfo" + id).fadeOut();
	}

}

function checkIfAllValidated(){
	$('#myModal1').modal('show');
}


//var titleFeedbackBox = ['ERROR', 'SUMMARY', 'SUCCESS', 'INFO'];
var titleFeedbackBox = ['ERROR', 'SUCCESS', 'SUMMARY' , 'INFO'];
//var stateLabel = ['ERROR', 'READY', 'VALIDATED', 'PROCESSING'];
var stateLabel = ['ERROR', 'VALIDATED', 'READY' , 'PROCESSING'];

function showProcessValidation(obj, id) {
	
	feedback_from_file.push(id);
	
	// show message
	$('#feedback-file' + id + ' h4').html(titleFeedbackBox[obj.state]);	
	$('#feedback-file' + id + ' span').html(obj.msg);
	for(var k in fileMessageColor) $('#feedback-file' + id).removeClass(fileMessageColor[k]);
	$('#feedback-file' + id).addClass(fileMessageColor[obj.state]);
	$('#feedback-file' + id).show();

	// change state
	for(var k in fileStateColor) $('#file' + id  + '-state').removeClass(fileStateColor[k]);
	$('#file' + id  + '-state').addClass(fileStateColor[obj.state]);
	$('#file' + id  + '-state').html(stateLabel[obj.state]);

	
	switch(obj.state){
		case 0: // enable send button
				$('#formInputs' + id + ' .disable-form').fadeOut();
				$('#formInputs' + id + ' .btn-send-data').html(
					'<input type="button" class="btn green snd-metadata-btn" value="SEND METADATA" onclick="sendMetadata(' + id  + ', 1);" style="position:relative;z-index:20;" >'	
				);
				break;
		case 2: // change send metadata button for validate button
				$('#formInputs' + id + ' .btn-send-data').html(
					'<input type="button" class="btn green val-metadata-btn" value="VALIDATE METADATA" onclick="sendMetadata(' + id  + ', 2);" style="position:relative;z-index:20;" >'	
				);
				$('#formInputs' + id + ' .disable-form').fadeIn();
				break;
		case 1: // disable all buttons and form
				$('#formInputs' + id + ' .val-metadata-btn').fadeOut(200);
				$('#formInputs' + id + ' .snd-metadata-btn').fadeOut(200);
				$('#formInputs' + id + ' .disable-form').fadeIn();
				totalNumBlocks --;
				break;
		case 3: // disable all buttons and form
				$('#formInputs' + id + ' .val-metadata-btn').fadeOut(200);
				$('#formInputs' + id + ' .disable-form').fadeIn();
				totalNumBlocks --;
				break;

	}

	if(totalNumBlocks == 0) {
		$('#bottom-no-validated-files').hide();
		$('#bottom-validated-files').show();
	}

}

function sendMetadata(id, op) {
	
	// mandatory field ref genome not completed
	if(($('#refGenomeTR' + id + ' select').val() == '') && !$('#refGenomeTR' + id + ' select').prop('disabled')) {
		
		$('#refGenomeTR' + id + ' .warn-ref-gen').show();
		$('#refGenomeTR' + id + ' select').css('border-color', '#e73d4a');		
	
	}else{
		
		// clean error messages
		$('#refGenomeTR' + id + ' .warn-ref-gen').hide();
		$('#refGenomeTR' + id + ' select').css('border-color', '#c2cad8');	
	
		// disable send / validate button
		if(op == 1) {
			$('#formInputs' + id + ' .snd-metadata-btn').prop('disabled', true);
			$('#formInputs' + id + ' .snd-metadata-btn').val('SENDING METADATA...');
		}else{
			$('#formInputs' + id + ' .val-metadata-btn').prop('disabled', true);
			$('#formInputs' + id + ' .val-metadata-btn').val('VALIDATING METADATA...');
		}

		// generate query
		data = $('#uploadFiles #formInputs' + id + ' input, #uploadFiles #formInputs' + id + ' select, #uploadFiles #formInputs' + id + ' textarea').serialize() + '&op=' + op;
		var re1 = new RegExp("paired" + id, "g");
		data = data.replace(re1, 'paired');
		var re2 = new RegExp("sorted" + id, "g");
		data = data.replace(re2, 'sorted');

		$.ajax({
			type: "POST",
			url: baseURL + "applib/processValidation.php",
			data: data, 
			success: function(data) {
				d = data.replace(/(\r\n|\n|\r|\t)/gm,"");
				var json = JSON.parse(d);
				showProcessValidation(json, id);
			}
		});

	}
	
}

var totalNumBlocks = 0;

jQuery(document).ready(function() {
	// force to load first form properly
	$('#formInputs0').fadeIn();
	$('.formInputs').each(function(index) {
		var val = $(this).find('.formatSelector option:selected').val();
		customfromFormat(val, index);
		totalNumBlocks++;
	});

	$('#myModal1').on('click', '.btn-modal-ok', function(e) {
		location.href = baseURL + 'workspace/';
	});

});
