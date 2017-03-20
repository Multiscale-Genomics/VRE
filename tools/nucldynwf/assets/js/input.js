var activeBlocks = [];
var mainBlock = 1;
var subordinateBlocks = [3,4,5,6];
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
      $('.warn-nd', $('#nucleosome-dynamics')).hide();
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
              $('#nucd-roundp input').prop('disabled', true);
              $('#lab-nucd-reads').css('display', 'block');
              $('#lab-nucd-roundp').hide();
            }else{
              $('#nucd-roundp').show();
              $('#nucd-roundp input').prop('disabled', false);
              $('#nucd-reads').hide();
              $('#nucd-reads input').prop('disabled', true);
              $('#lab-nucd-roundp').show();
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

		$('.params_nucdyn_inputs').change(function() {
				
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

		});


    var handleForm = function() {

        $('#nucleosome-dynamics').validate({
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

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.err-nd', $('#nucleosome-dynamics')).show();
                $('.warn-nd', $('#nucleosome-dynamics')).hide();
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
                //return true;
							error.insertAfter(element);
            },
            submitHandler: function(form) {
                if(activeBlocks.length == 0) {
                  $('.warn-nd', $('#nucleosome-dynamics')).show();
                  $('.err-nd', $('#nucleosome-dynamics')).hide();
                }else{
                  $('.warn-nd', $('#nucleosome-dynamics')).hide();
                  $('.err-nd', $('#nucleosome-dynamics')).hide();
                  var data = $('#nucleosome-dynamics').serialize();
                  data = data.replace(/%5B/g,"[");
                  data = data.replace(/%5D/g,"]");
		  //console.log($("#params_nuclr_width").val());
                  //console.log(data);
                  location.href = "/applib/launchTool.php?" + data;
                }
            }
        });

        // rules by ID instead of NAME
        // nucleR
				$(".params_nuclr_inputs").each(function() {
        	$(this).rules("add", { 
						required:true, 
						messages: {
							required: "You must select all the file types.",
						}
					});
        });

        $("#params_nuclr_width").rules("add", {required:true, range: [1, 150]});
        $("#params_nuclr_minoverlap").rules("add", {required:true, range: [1, $("#params_nuclr_width").val()]});
        $("#params_nuclr_dyad_len").rules("add", {required:true});
        $("#params_nuclr_thperc").rules("add", {required:true});
        $("#params_nuclr_thval").rules("add", {required:true});
        $("#params_nuclr_hthresh").rules("add", {required:true});
        $("#params_nuclr_wthresh").rules("add", {required:true});
        $("#params_nuclr_pcKeepComp").rules("add", {required:true});
        // nucleosome Dynamics
        /*$(".params_nucdyn_inputs").each(function() {
        	$(this).rules("add", {required:true});
        });*/
				$(".params_nucdyn_inputs").each(function() {
        	$(this).rules("add", { 
						required:true, 
						messages: {
							required: "You must select all the file types.",
						}
					});
        });

        $("#params_nucdyn_range").rules("add", {required:true});
        $("#params_nucdyn_maxdiff").rules("add", {required:true});
        $("#params_nucdyn_maxlen").rules("add", {required:true});
        $("#params_nucdyn_rpow").rules("add", {required:true});
        $("#params_nucdyn_rsize").rules("add", {required:true});
        $("#params_nucdyn_smag").rules("add", {required:true});
        $("#params_nucdyn_shiftmn").rules("add", {required:true});
        $("#params_nucdyn_shiftth").rules("add", {required:true});
        $("#params_nucdyn_indelmn").rules("add", {required:true});
        $("#params_nucdyn_indeth").rules("add", {required:true});
        // Nucleosome-free regions
        $("#params_nfr_minw").rules("add", {required:true});
        $("#params_nfr_threshold").rules("add", {required:true});
        // Nucleosome phasing
        $("#params_perio_perio").rules("add", {required:true});
        // TSS classification
        $("#params_txstart_win").rules("add", {required:true});
        $("#params_txstart_opent").rules("add", {required:true});
        // Stiffness
        $("#params_gausfit_range").rules("add", {required:true});


        $('#nucleosome-dynamics input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#nucleosome-dynamics').validate().form()) {
                    $('#nucleosome-dynamics').submit(); //form validation success, call ajax form submit
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

			$("#params_nuclr_width").bind('keyup mouseup', function() { 
				$("#params_nuclr_width").val($(this).val()); 
				$("#params_nuclr_minoverlap").rules("add", { range: [1, $(this).val()] });
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
