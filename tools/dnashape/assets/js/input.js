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

        $('#dnashape-form').validate({
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
                $('.err-nd', $('#dnashape-form')).show();
                $('.warn-nd', $('#dnashape-form')).hide();
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
               error.insertAfter(element);
            },

            submitHandler: function(form) {

							$('.warn-nd', $('#dnashape-form')).hide();
							$('.err-nd', $('#dnashape-form')).hide();
							var data = $('#dnashape-form').serialize();
							data = data.replace(/%5B/g,"[");
							data = data.replace(/%5D/g,"]");
							data = data.replace(/%3A/g,":");
							location.href = baseURL + "applib/launchTool.php?" + data;
						//	console.log(data);

            }
        });

        // rules by ID instead of NAME
				$("#max_results").rules("add", { required:true });

				$("#dimer_orientation").rules("add", { required:true });

				$("#dimer_spacing").rules("add", { required:true });



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
