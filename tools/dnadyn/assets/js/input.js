var baseURL = $("#base-url").val();

$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

		$('#operations').change(function() {

			var array_selecteds = $(this).val();

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

			}

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
                $('.err-tool', $('#tool-input-form')).show();
                $('.warn-tool', $('#tool-input-form')).hide();
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
									$('button[type="submit"]', $('#tool-input-form')).prop('disabled', true);
            		$('button[type="submit"]', $('#tool-input-form')).html('<i class="fa fa-spinner fa-pulse fa-spin"></i> Launching tool, please don\'t close the tab.');
							/*if(activeBlocks.length == 0) {
                  $('.warn-nd', $('#dnadyn-form')).show();
                  $('.err-nd', $('#dnadyn-form')).hide();
                }else{*/
                  $('.warn-nd', $('#tool-input-form')).hide();
                  $('.err-nd', $('#tool-input-form')).hide();
                  var data = $('#tool-input-form').serialize();
                  data = data.replace(/%5B/g,"[");
                  data = data.replace(/%5D/g,"]");
									data = data.replace(/%3A/g,":");
		  						location.href = baseURL + "applib/launchTool.php?" + data;
                	//console.log(data);
               // }

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
        
				/*$("#operations").rules("add", {
					required:true
				});


				// Create Trajectory
				$("#numStruct").rules("add", {
					required:true
				});*/


		}

    return {
        //main function to initiate the module
        init: function() {
            handleForm();
        }

    };

}();

var Select2Init = function() {

	var handleSelect2 = function() {

		$("#operations").select2({
			placeholder: "Select one or more operations clicking here",
			width: '100%'
		});


		$('#operations').on('change', function() {
			if($(this).find('option:selected').length > 0) {
				$(this).parent().removeClass('has-error');
				$(this).parent().find('.help-block').hide();
			}
		});

	}

	return {
        //main function to initiate the module
        init: function() {
            handleSelect2();
        }

    };


}();


jQuery(document).ready(function() {

	//ComponentsBootstrapSwitch.init();
	Select2Init.init();
  ValidateForm.init();

});
