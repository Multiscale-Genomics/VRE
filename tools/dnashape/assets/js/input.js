var baseURL = $("#base-url").val();

$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

		$('#dimer').change(function() {

			if($(this).val() == "on") {

				$('.dimer_group').show();
				$('#dimer_orientation').prop('disabled', false);
				$('#dimer_spacing').prop('disabled', false);

			}else{

				$('.dimer_group').hide();
				$('#dimer_orientation').prop('disabled', true);
				$('#dimer_spacing').prop('disabled', true);

			}

		/*	var array_selecteds = $(this).val();

			if (array_selecteds !== null) {

				if(array_selecteds.indexOf("createTrajectory") != -1) {
					$('#fg_numStruct').show();
					$('#numStruct').prop('disabled', false);
				} else {
					$('#fg_numStruct').hide();
					$('#numStruct').prop('disabled', true);
				}
				
			}else{
				
				$('#fg_numStruct').hide();
				$('#numStruct').prop('disabled', true);

			}*/

		});


    var handleForm = function() {

        $('#tool-input-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: [],
            rules: {
                project: {
                    required: true,
                    nowhitespace: true
                },
								execution: {
                    required: true,
                    nowhitespace: true
                }
            },
						messages: {
							project: {
								required: "Please select in which project you will execute this tool."
							},
							execution: {
								required: "The execution name is mandatory."
							}
						},


            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.err-tool', $('#dnashape-form')).show();
                $('.warn-tool', $('#dnashape-form')).hide();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label, e) {
                $(e).parent().removeClass('has-error');
                $(e).parent().parent().removeClass('has-error');
                $(e).parent().parent().parent().removeClass('has-error');
            },

            errorPlacement: function(error, element) {
            	if($(element).hasClass("select2-hidden-accessible")) {
            		console.log($(element).parent());
            		error.insertAfter($(element).parent().find("span.select2"));
							} else {
								error.insertAfter(element);
							}
						},


            submitHandler: function(form) {
							$('button[type="submit"]', $('#dnashape-form')).prop('disabled', true);
							$('.warn-nd', $('#tool-input-form')).hide();
							$('.err-nd', $('#tool-input-form')).hide();
							var data = $('#tool-input-form').serialize();
							data = data.replace(/%5B/g,"[");
							data = data.replace(/%5D/g,"]");
							data = data.replace(/%3A/g,":");
							location.href = baseURL + "applib/launchTool.php?" + data;
						//	console.log(data);

            }
        });

        // rules by ID instead of NAME
        $(".field_required").each(function() {
        	$(this).rules("add", { 
						required:true, 
						/*messages: {
							required: "You must select all the file types.",
						}*/
					});
        });
				/*$("#max_results").rules("add", { required:true });

				$("#dimer_orientation").rules("add", { required:true });

				$("#dimer_spacing").rules("add", { required:true });*/



				// Create Trajectory
				

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
