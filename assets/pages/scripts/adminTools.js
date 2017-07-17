var baseURL = $('#base-url').val();

// Open modal with tool config JSON 

callShowToolJson = function(tool) {
	$('#modalAnalysis').modal('show');
	$('#modalAnalysis .modal-body').html('Loading data...');

	$.ajax({
		type: "POST",
		url: baseURL + "applib/showToolJson.php",
		data: "tool=" + tool, 
		success: function(data) {
			$('#modalAnalysis .modal-body').html(data);
		}
	});

}
