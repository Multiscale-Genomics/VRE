var baseURL = $("#base-url").val();
//var activeBlocks = [1, 2];

/*var ComponentsBootstrapSwitch = function () {

	var initSwitchBlocks = function() {
		
		disableBlock(2);

	}

	function removeItemFromArray(arr) {
		var what, a = arguments, L = a.length, ax;
		while (L > 1 && arr.length) {
				what = a[--L];
				while ((ax= arr.indexOf(what)) !== -1) {
						arr.splice(ax, 1);
				}
		}
		return arr;
	}

	function enableBlock (id) {
		activeBlocks.push(id);

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

	function disableBlock (id) {
		removeItemFromArray(activeBlocks, id);

		$('#form-block-header' + id + ' .tools').hide();
		$('#form-block' + id).slideUp();
		$('#form-block' + id + ' .form-field-enabled').prop('disabled', true);
		$('#form-block' + id + ' .form-field-disabled').prop('disabled', true);
	}


	var handleBootstrapSwitch = function() {

		$('.switch-block').on('switchChange.bootstrapSwitch', function (event, state) {
				var id = parseInt($(this).attr('id').substring(12,14));
				if(state == true) {
					enableBlock(id);
				}else{
					disableBlock(id);
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



}();*/



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

        $('#dnadyn-form').validate({
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
                $('.err-nd', $('#dnadyn-form')).show();
                $('.warn-nd', $('#dnadyn-form')).hide();
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
									$('button[type="submit"]', $('#dnadyn-form')).prop('disabled', true);
							/*if(activeBlocks.length == 0) {
                  $('.warn-nd', $('#dnadyn-form')).show();
                  $('.err-nd', $('#dnadyn-form')).hide();
                }else{*/
                  $('.warn-nd', $('#dnadyn-form')).hide();
                  $('.err-nd', $('#dnadyn-form')).hide();
                  var data = $('#dnadyn-form').serialize();
                  data = data.replace(/%5B/g,"[");
                  data = data.replace(/%5D/g,"]");
									data = data.replace(/%3A/g,":");
		  						location.href = baseURL + "applib/launchTool.php?" + data;
                	//console.log(data);
               // }

            }
        });

        // rules by ID instead of NAME
				$("#operations").rules("add", {
					required:true
				});


				// Create Trajectory
				$("#numStruct").rules("add", {
					required:true
				});


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

		$(".select2dnadyn").select2({
			placeholder: "Select one or more operations clicking here",
			width: '100%'
		});


		$('.select2dnadyn').on('change', function() {
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
