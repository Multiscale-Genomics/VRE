var baseURL = $("#base-url").val();

var ValidateForm = function() {

		$('.params_wgbs_inputs').change(function() {

			var selected = new Array();
        
        $('.params_wgbs_inputs option:selected').each(function() {
            selected.push($(this).val());
        });
        
        $('.params_wgbs_inputs option').each(function() {
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

        $('#process-wgbs').validate({
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
                $('.err-nd', $('#process-wgbs')).show();
                $('.warn-nd', $('#process-wgbs')).hide();
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
            		$('button[type="submit"]', $('#process-wgbs')).prop('disabled', true);
                $('.warn-nd', $('#process-wgbs')).hide();
                $('.err-nd', $('#process-wgbs')).hide();
                var data = $('#process-wgbs').serialize();
								data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
								var data = $('#process-wgbs').serialize();
                //console.log(data);
                location.href = baseURL + "applib/launchTool.php?" + data;

            }
        });

        // rules by ID instead of NAME
        $("#aligner").rules("add", {required:true});
        $("#aligner_path").rules("add", {required:true});
        $("#bss_path").rules("add", {required:true});

				$(".params_wgbs_inputs").each(function() {
        	$(this).rules("add", { 
						required:true, 
						messages: {
							required: "You must select all the file types.",
						}
					});
        });

        $('#process-wgbs').keypress(function(e) {
            if (e.which == 13) {
                if ($('#process-wgbs').validate().form()) {
                    $('#process-wgbs').submit(); //form validation success, call ajax form submit
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
