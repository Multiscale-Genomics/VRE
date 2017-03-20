var baseURL = $("#base-url").val();

/*var arr_exclusive = [];

$.validator.addMethod("exclusive", function(value, element, params) {
	var index = arr_exclusive.indexOf(value);
	if(index == -1) {
		arr_exclusive.push(value);
	} else { 
		arr_exclusive.splice(index, 1);	
		//var i = arr_exclusive.indexOf(value);
    //if (i > -1) arr_exclusive.splice(i, 1);	
		console.log(index);
	}
	return arr_exclusive.length === params;
});*/

var ValidateForm = function() {

		$('.params_pydock_inputs').change(function() {
				
			var selected = new Array();
        
        $('.params_pydock_inputs option:selected').each(function() {
            selected.push($(this).val());
        });
        
        $('.params_pydock_inputs option').each(function() {
            if (!$(this).is(':selected') && $(this).val() != '') {
                var shouldDisable = false;
                for (var i = 0; i < selected.length; i++) {
                    if (selected[i] == $(this).val()) shouldDisable = true;
                }
                
                $(this).removeAttr('disabled', 'disabled');

                if (shouldDisable) $(this).attr('disabled', 'disabled');
                
            }
        });

		});

    var handleForm = function() {

        $('#pdna-docking').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: [],
            rules: {
                project: {
                    required: true,
                    nowhitespace: true
                }
            },
						messages: {
							project: {
								required: "The project name is mandatory."
							}
						},


            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.err-nd', $('#pdna-docking')).show();
                $('.warn-nd', $('#pdna-docking')).hide();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label, e) {
                $(e).parent().removeClass('has-error');
                $(e).parent().parent().parent().removeClass('has-error');
            },

            errorPlacement: function(error, element) {
               error.insertAfter(element);
            },

            submitHandler: function(form) {
                $('.warn-nd', $('#pdna-docking')).hide();
                $('.err-nd', $('#pdna-docking')).hide();
                var data = $('#pdna-docking').serialize();
								data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
                location.href = baseURL + "applib/launchTool.php?" + data;
								/*var data = $('#pdna-docking').serialize();
                console.log(data);*/

            }
        });

        // rules by ID instead of NAME
        /*$("#params_nuclr_width").rules("add", {required:true});
        $("#params_nuclr_minoverlap").rules("add", {required:true});*/

				$(".params_pydock_inputs").each(function() {
        	$(this).rules("add", { 
						required:true, 
						//exclusive:$(".params_pydock_inputs").length,
						messages: {
							required: "You must select all the file types.",
							//exclusive: "All the files must have a different file type."
						}
					});
        });


        $('#pdna-docking input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#pdna-docking').validate().form()) {
                    $('#pdna-docking').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            handleForm();
        }

    };

}();

jQuery(document).ready(function() {
   ValidateForm.init();
});
