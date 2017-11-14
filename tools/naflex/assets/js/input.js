var baseURL = $("#base-url").val();

var ValidateForm = function() {

		$('.params_naflex_inputs').change(function() {
				
			var selected = new Array();
        
        $('.params_naflex_inputs option:selected').each(function() {
            selected.push($(this).val());
        });
        
        $('.params_naflex_inputs option').each(function() {
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

        $('#naflex-form').validate({
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
                $('.err-nd', $('#naflex-form')).show();
                $('.warn-nd', $('#naflex-form')).hide();
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
            		$('button[type="submit"]', $('#naflex-form')).prop('disabled', true);
                /*$('.warn-nd', $('#naflex-form')).hide();
                $('.err-nd', $('#naflex-form')).hide();
                var data = $('#naflex-form').serialize();
								data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
                //console.log(data);
                location.href = baseURL + "tools/compute.php?" + data;*/
              	var data = $('#naflex-form').serialize();
              	data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
								location.href = baseURL + "applib/launchTool.php?" + data;

              	//console.log(data);
								//console.log(arr_exclusive);

            }
        });

        // rules by ID instead of NAME
        $("#operations").rules("add", {
					required:true, 
					messages: {
						required:"You must select at least one operation."
					}
				});

				$(".params_naflex_inputs").each(function() {
        	$(this).rules("add", { 
						required:true, 
						messages: {
							required: "You must select all the file types.",
						}
					});
        });

        $('#naflex-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#naflex-form').validate().form()) {
                    $('#naflex-form').submit(); //form validation success, call ajax form submit
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

	$(".select2naf").select2({
	  placeholder: "Select one or more operations clicking here",
		width: '100%'
	});


	$('.select2naf').on('change', function() {
		if($(this).find('option:selected').length > 0) {
			$(this).parent().removeClass('has-error');
			$(this).parent().find('.help-block').hide();
		}
	});

   ValidateForm.init();
});
