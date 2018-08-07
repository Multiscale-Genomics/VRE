var activeBlocks = [];
var mainBlock = 1;
var subordinateBlocks = [2,3,4,5,6];
var autoMainBlock = false;

var ComponentsBootstrapSwitch = function () {

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

    function checkSubordinateBlock (currId, mainId, subBlocks, actBlocks) {
      if(subBlocks.indexOf(currId) != -1) {
        // it's a subordinate block
        if(actBlocks.indexOf(mainId) == -1) {
          // the main block is disabled
          return false;
        }else{
          // the main block is enabled
          return true;
        }
      } else {
        //it isn't a subordinate block
        return true;
      }
    }

    function enableBlock (id){
      activeBlocks.push(id);
      $('.warn-tool', $('#tool-input-form')).hide();
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
      removeItemFromArray(activeBlocks, id);
      $('#form-block-header' + id + ' .tools').hide();
      $('#form-block' + id).slideUp();
      $('#form-block' + id + ' .form-field-enabled').prop('disabled', true);
      $('#form-block' + id + ' .form-field-disabled').prop('disabled', true);
    }

    var handleBootstrapSwitch = function() {

        // generic block switches
        $('.switch-block').on('switchChange.bootstrapSwitch', function (event, state) {
            var id = parseInt($(this).attr('id').substring(12,14));
            if(state == true) {
              if(!checkSubordinateBlock(id, mainBlock, subordinateBlocks, activeBlocks)) {
                $('#myModal1').modal('show');
                $("#switch-block" + mainBlock).bootstrapSwitch("state", true);
              }
              enableBlock(id);
            }else{
              disableBlock(id);
            }
        });

        // NucleR Background Level
        $('#switch-bglevel').on('switchChange.bootstrapSwitch', function (event, state) {
        	console.log($(this).data());
            var fgroup = $(this).parent().parent().parent().parent().parent();
            if(fgroup.hasClass('has-error')) fgroup.removeClass('has-error');
            if(state == true) {
			  $('#switch-bglevel').bootstrapSwitch("labelText", "Percentage");
              $('#nucr-absval').show();
              $('#nucr-absval input').prop('disabled', false);
              $('#nucr-perc').hide();
              $('#nucr-perc input').prop('disabled', true);
              $('#lab-nucr-absval').css('display', 'block');
              $('#lab-nucr-perc').hide();
            }else{
			  $('#switch-bglevel').bootstrapSwitch("labelText", "Abs. Value");
              $('#nucr-perc').show();
              $('#nucr-perc input').prop('disabled', false);
              $('#nucr-absval').hide();
              $('#nucr-absval input').prop('disabled', true);
              $('#lab-nucr-perc').show();
              $('#lab-nucr-absval').hide();
            }
        });

        // Nucleosome Dynamics Equal size
        $('#switch-eqsize').on('switchChange.bootstrapSwitch', function (event, state) {
            var fgroup = $(this).parent().parent().parent().parent().parent();
            if(fgroup.hasClass('has-error')) fgroup.removeClass('has-error');
            if(state == true) {
              $('#nucd-reads').show();
              $('#nucd-reads input').prop('disabled', false);
              $('#nucd-roundp').hide();
              //$('#nucd-roundp input').prop('disabled', true);
              $('#lab-nucd-reads').css('display', 'block');
              //$('#lab-nucd-roundp').hide();
            }else{
              $('#nucd-roundp').show();
             // $('#nucd-roundp input').prop('disabled', false);
              $('#nucd-reads').hide();
              $('#nucd-reads input').prop('disabled', true);
              //$('#lab-nucd-roundp').show();
              $('#lab-nucd-reads').hide();
            }
        });

        // Nucleosome Dynamics Combined
        $('#switch-combin').on('switchChange.bootstrapSwitch', function (event, state) {
            var fgroup = $(this).parent().parent().parent().parent().parent();
            if(fgroup.hasClass('has-error')) fgroup.removeClass('has-error');
            if(state == true) {
              $('#nucd-samem').show();
              $('#nucd-samemf').hide();
              $('#nucd-samem input').prop('disabled', false);
              $('#lab-nucd-samem').css('display', 'block');
            }else{
              $('#nucd-samem').hide();
              $('#nucd-samemf').show();
              $('#nucd-samem input').prop('disabled', true);
              $('#lab-nucd-samem').css('display', 'none');
            }
        });

    }

    return {
        //main function to initiate the module
        init: function () {
            handleBootstrapSwitch();
        }
    };

}();

var ValidateForm = function() {

		/*$('.params_nucdyn_inputs').change(function() {
				
			var selected = new Array();
        
        $('.params_nucdyn_inputs option:selected').each(function() {
            selected.push($(this).val());
        });
        
        $('.params_nucdyn_inputs option').each(function() {
            if (!$(this).is(':selected') && $(this).val() != '') {
                var shouldDisable = false;
                for (var i = 0; i < selected.length; i++) {
                    if (selected[i] == $(this).val()) shouldDisable = true;
                }
                
                $(this).removeAttr('disabled', 'disabled');

                if (shouldDisable) $(this).attr('disabled', 'disabled');
                
            }
        });

		});*/


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
                $('.err-blocks', $('#tool-input-form')).hide();
                $('.warn-tool', $('#tool-input-form')).hide();
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
              if($(element).hasClass("select2-hidden-accessible")) {
            		//console.log($(element).parent());
            		error.insertAfter($(element).parent().find("span.select2"));
							} else {
								error.insertAfter(element);
							}
            },
            submitHandler: function(form) {

                if(activeBlocks.length == 0) {
                  $('.warn-tool', $('#tool-input-form')).show();
                  $('.err-tool', $('#tool-input-form')).hide();
                  $('.err-blocks', $('#tool-input-form')).hide();
                }else{

									if(activeBlocks.indexOf(mainBlock) != -1) {

										$('button[type="submit"]', $('#tool-input-form')).prop('disabled', true);
										$('button[type="submit"]', $('#tool-input-form')).html('<i class="fa fa-spinner fa-pulse fa-spin"></i> Launching tool, please don\'t close the tab.');
										$('.warn-tool', $('#tool-input-form')).hide();
										$('.err-tool', $('#tool-input-form')).hide();
										$('.err-blocks', $('#tool-input-form')).hide();
										var data = $('#tool-input-form').serialize();
										data = data.replace(/%5B/g,"[");
										data = data.replace(/%5D/g,"]");
										//console.log(data);
										location.href = "/applib/launchTool.php?" + data;

									} else {

										$('.err-blocks', $('#tool-input-form')).show();

									}

                }
            }
        });

        // rules by ID instead of NAME
				$(".field_required").each(function() {
        	$(this).rules("add", { 
						required:true 
					});
        });
	
        // nucleR
				/*$(".params_nuclr_inputs").each(function() {
        	$(this).rules("add", { 
						required:true, 
						messages: {
							required: "You must select all the file types.",
						}
					});
        });*/

        //$("#nucleR_width").rules("add", {required:true, range: [1, 150]});
        $("#nucleR_minoverlap").rules("add", {required:true, range: [1, $("#nucleR_width").val()]});
        /*$("#nucleR_dyad_length").rules("add", {required:true});
        $("#nucleR_thresholdPercentage").rules("add", {required:true});
        $("#nucleR_thresholdValue").rules("add", {required:true});
        $("#nucleR_hthresh").rules("add", {required:true});
        $("#nucleR_wthresh").rules("add", {required:true});
        $("#nucleR_pcKeepComp").rules("add", {required:true});*/
        // nucleosome Dynamics
				/*$(".params_input").each(function() {
        	$(this).rules("add", { 
						required:true, 
					});
        });*/

				//if($('#numInputs').val() > 1) {
				/*$("#nucDyn_range").rules("add", {required:true});
				$("#nucDyn_maxDiff").rules("add", {required:true});
				$("#nucDyn_maxLen").rules("add", {required:true});*/
				//$("#params_nucdyn_rpow").rules("add", {required:true});
				//$("#nucDyn_readSize").rules("add", {required:true});
				//$("#params_nucdyn_smag").rules("add", {required:true});
				/*$("#nucDyn_shift_min_nreads").rules("add", {required:true});
				$("#nucDyn_shift_threshold").rules("add", {required:true});
				$("#nucDyn_indel_min_nreads").rules("add", {required:true});
				$("#nucDyn_indel_threshold").rules("add", {required:true});*/
				//}
        // Nucleosome-free regions
        /*$("#NFR_minwidth").rules("add", {required:true});
        $("#NFR_threshold").rules("add", {required:true});*/
        // Nucleosome phasing
        //$("#periodicity_periodicity").rules("add", {required:true});
        // TSS classification
        /*$("#txstart_window").rules("add", {required:true});
        $("#txstart_open_thresh").rules("add", {required:true});*/
        // Stiffness
        //$("#gausfitting_range").rules("add", {required:true});


        $('#tool-input-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#tool-input-form').validate().form()) {
                    $('#tool-input-form').submit(); //form validation success, call ajax form submit
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

var InitForm = function() {

    var handleInitForm = function() {

      $('.form-block').hide();
      $('.form-block-header .tools').hide();
      $('.form-block .form-field-enabled').prop('disabled', true);
      $('.form-block .form-field-disabled').prop('disabled', true);

			$("#nucleR_width").bind('keyup mouseup', function() { 
				$("#nucleR_width").val($(this).val()); 
				$("#nucleR_minoverlap").rules("add", { range: [1, $(this).val()] });
			});

			//$("#params_nuclr_width").val(147);
    }

    return {
        //main function to initiate the module
        init: function() {
            handleInitForm();
        }

    };

}();

var InitSelect2 = function() {

	var handleInitSelect2 = function() {

		$("#params_nuclr_mnase").select2({
				placeholder: "Select files",
				width: '100%'
		});

		$('#params_nuclr_mnase').on('change', function() {
			if($(this).find('option:selected').length > 0) {
				$(this).parent().removeClass('has-error');
				$(this).parent().find('.help-block').hide();
			}
		});


	}

	return { 
		init: function() {
        handleInitSelect2();
    }
  };

}();

jQuery(document).ready(function() {

   InitForm.init();
   ComponentsBootstrapSwitch.init();
   ValidateForm.init();
   InitSelect2.init();

});
