var baseURL = $("#base-url").val();

$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

    var handleForm = function() {

        $('#pdiview-form').validate({
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
                $('.err-nd', $('#pdiview-form')).show();
                $('.warn-nd', $('#pdiview-form')).hide();
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
							$('button[type="submit"]', $('#pdiview-form')).prop('disabled', true);
							$('.warn-nd', $('#pdiview-form')).hide();
							$('.err-nd', $('#pdiview-form')).hide();
							var data = $('#pdiview-form').serialize();
							data = data.replace(/%5B/g,"[");
							data = data.replace(/%5D/g,"]");
							data = data.replace(/%3A/g,":");
							location.href = baseURL + "applib/launchTool.php?" + data;
							//console.log(data);

            }
        });

        // rules by ID instead of NAME
				$("#chains").rules("add", { required:true });


				$("#chains").rules("add", {
						required:true, 
						regx: /^([A-Z](,)){0,}[A-Z]$/,
						messages: {
							regx: "You must use the next format: A,B,C (uppercase and sepparated by commas)",
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
