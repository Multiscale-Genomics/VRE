var baseURL = $("#base-url").val();

$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

    var handleForm = function() {

        $('#minimizedStruct-form').validate({
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
                $('.err-nd', $('#minimizedStruct-form')).show();
                $('.warn-nd', $('#minimizedStruct-form')).hide();
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
									$('button[type="submit"]', $('#minimizedStruct-form')).prop('disabled', true);
							/*if(activeBlocks.length == 0) {
                  $('.warn-nd', $('#dnadyn-form')).show();
                  $('.err-nd', $('#dnadyn-form')).hide();
                }else{*/
                  $('.warn-nd', $('#minimizedStruct-form')).hide();
                  $('.err-nd', $('#minimizedStruct-form')).hide();
                  var data = $('#minimizedStruct-form').serialize();
                  data = data.replace(/%5B/g,"[");
                  data = data.replace(/%5D/g,"]");
									data = data.replace(/%3A/g,":");
		  						location.href = baseURL + "applib/launchTool.php?" + data;
                	//console.log(data);
               // }

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
