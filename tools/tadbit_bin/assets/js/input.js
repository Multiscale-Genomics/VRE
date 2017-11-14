var baseURL = $("#base-url").val();

$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

    var handleForm = function() {

        $('#tadbit_bin-form').validate({
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
                $('.err-nd', $('#tadbit_bin-form')).show();
                $('.warn-nd', $('#tadbit_bin-form')).hide();
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
								$('button[type="submit"]', $('#tadbit_bin-form')).prop('disabled', true);
               	$('.warn-nd', $('#tadbit_bin-form')).hide();
               	$('.err-nd', $('#tadbit_bin-form')).hide();
                var data = $('#tadbit_bin-form').serialize();
								data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
								data = data.replace(/%3A/g,":");
                location.href = baseURL + "applib/launchTool.php?" + data;
                //console.log(data);

            }
        });

        // rules by ID instead of NAME

				// Normalization by ICE
				$("#resolution").rules("add", {
					required:true
				});

				$("#coord1").rules("add", {
					//regx: /^(chr)[A-Za-z0-9]{0,}:[0-9]{1,}-[0-9]{1,}$/,
					regx: /^((chr)[A-Za-z0-9]{0,}|(chr)[A-Za-z0-9]{0,}:[0-9]{1,}-[0-9]{1,})$/,
					//required:true,
					messages: {
						regx: "You must use the next format: chr3:110000000-120000000",
					}
				});



        $('#tadbit_bin-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#tadbit_bin-form').validate().form()) {
                    //$('#tadbit_map_parse_filter-form').submit(); //form validation success, call ajax form submit
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
