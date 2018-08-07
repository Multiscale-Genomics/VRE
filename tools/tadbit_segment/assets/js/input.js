var baseURL = $("#base-url").val();

$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

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
            		error.insertAfter($(element).parent().find("span.select2"));
							} else {
								error.insertAfter(element);
							}
            },
            submitHandler: function(form) {
								$('button[type="submit"]', $('#tool-input-form')).prop('disabled', true);
            		$('button[type="submit"]', $('#tool-input-form')).html('<i class="fa fa-spinner fa-pulse fa-spin"></i> Launching tool, please don\'t close the tab.');              
            		$('.warn-tool', $('#tool-input-form')).hide();
               $('.err-tool', $('#tool-input-form')).hide();
                var data = $('#tool-input-form').serialize();
                var data = $('#tool-input-form').serialize();
								data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
								data = data.replace(/%3A/g,":");
                location.href = baseURL + "applib/launchTool.php?" + data;
                //console.log(data);

            }
        });

        // rules by ID instead of NAME
        $(".field_required").each(function() {
        	$(this).rules("add", { 
						required:true 
					});
        });

				// Normalization by ICE
				/*$("#resolution").rules("add", {
					required:true
				});*/

				$("#chromosome_names").rules("add", {
					//required:true,
					regx: /^(chr[0-9a-zA-Z]+ ){0,}(chr[0-9a-zA-Z]+)$/,
					messages: {
						regx: "You must use the next format: 'chr1 chr2 chrX'",
					}
				});

				/*$("#callers").rules("add", {
					required:true
				});*/

        $('#tool-input-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#tool-input-form').validate().form()) {
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

var Select2 = function () {

    var handleSelect2 = function() {

			$("#callers").select2({
				placeholder: "Select one or more callers clicking here",
				width: '100%'
			});

		}

		return {
        //main function to initiate the module
        init: function () {
            handleSelect2();
        }
    };

}();


jQuery(document).ready(function() {

  ValidateForm.init();
  Select2.init();

});
