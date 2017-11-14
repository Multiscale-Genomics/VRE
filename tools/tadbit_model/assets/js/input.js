var baseURL = $("#base-url").val();

var ComponentsBootstrapSwitch = function () {

	var initSwitchBlocks = function() {
		
		$('.form-block').hide();
		$('.form-block-header .tools').hide();
		$('.form-block .form-field-enabled').prop('disabled', true);
		$('.form-block .form-field-disabled').prop('disabled', true);

	}


	function enableBlock (id){
		$('#switch-block' + id).bootstrapSwitch('state', true);
		$('#form-block-header' + id + ' .tools').show();
		$('#form-block-header' + id + ' .tools').html('<a href="javascript:;" class="collapse"></a>');
		if($('#form-block' + id).css('display') == 'block') {
			$('#form-block-header' + id + ' .tools a').removeClass('collapse');
			$('#form-block-header' + id + ' .tools a').addClass('expand');
		}else{
			$('#form-block-header' + id + ' .tools a').addClass('collapse');
			$('#form-block-header' + id + ' .tools a').removeClass('expand');
		}
		$('#form-block' + id).slideDown();
		$('#form-block' + id + ' .form-field-enabled').prop('disabled', false);
		if($('#form-block' + id + ' .form-field-disabled').parent().css('display') == 'block')
			$('#form-block' + id + ' .form-field-disabled').prop('disabled', false);
	}

	function disableBlock (id){
		$('#switch-block' + id).bootstrapSwitch('state', false);
		$('#form-block-header' + id + ' .tools').hide();
		$('#form-block' + id).slideUp();
		$('#form-block' + id + ' .form-field-enabled').prop('disabled', true);
		$('#form-block' + id + ' .form-field-disabled').prop('disabled', true);
	}


	var handleBootstrapSwitch = function() {
		
		enableBlock(2);
		
		// generic block switches
		$('.switch-block').on('switchChange.bootstrapSwitch', function (event, state) {
				var id = parseInt($(this).attr('id').substring(12,14));
				if(state == true) {
					if (id == 2) {
						enableBlock(2);
						disableBlock(3);
					}else{
						enableBlock(3);
						disableBlock(2);
					}
				}else{
					if (id == 2) {
						enableBlock(3);
						disableBlock(2);
					}else{
						enableBlock(2);
						disableBlock(3);
					}
				}
		});

	}

	return {
		//main function to initiate the module
		init: function () {
			initSwitchBlocks();
			handleBootstrapSwitch();
		}
  };



}();

$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

    var handleForm = function() {

        $('#tadbit_model-form').validate({
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
                $('.err-nd', $('#tadbit_model-form')).show();
                $('.warn-nd', $('#tadbit_model-form')).hide();
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
								$('button[type="submit"]', $('#tadbit_model-form')).prop('disabled', true);
               $('.warn-nd', $('#tadbit_model-form')).hide();
               $('.err-nd', $('#tadbit_model-form')).hide();
                var data = $('#tadbit_model-form').serialize();
								data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
								data = data.replace(/%3A/g,":");
                location.href = baseURL + "applib/launchTool.php?" + data;
                //console.log(data);

            }
        });

        // rules by ID instead of NAME

				$("#resolution").rules("add", {
					required:true
				});

				$("#gen_pos_chrom_name").rules("add", {
					required:true
				});

				$("#gen_pos_begin").rules("add", {
					required:true
				});

				$("#gen_pos_end").rules("add", {
					required:true
				});


				$("#num_mod_comp1").rules("add", {
					required:true
				});

				$("#num_mod_keep1").rules("add", {
					required:true
				});

				$("#max_dist1").rules("add", {
					required:true
				});

				$("#upper_bound1").rules("add", {
					required:true
				});

				$("#lower_bound1").rules("add", {
					required:true
				});

				$("#cutoff1").rules("add", {
					required:true
				});


				$("#num_mod_comp2").rules("add", {
					required:true
				});

				$("#num_mod_keep2").rules("add", {
					required:true
				});

				$("#max_dist2").rules("add", {
					required:true
				});

				$("#upper_bound2").rules("add", {
					required:true
				});

				$("#lower_bound2").rules("add", {
					required:true
				});

				$("#cutoff2").rules("add", {
					required:true
				});




        $('#tadbit_model-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#tadbit_model-form').validate().form()) {
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

	ComponentsBootstrapSwitch.init();
  ValidateForm.init();

});
