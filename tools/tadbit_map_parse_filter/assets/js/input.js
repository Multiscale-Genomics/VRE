var baseURL = $("#base-url").val();

/*function changeArgDependency(dep, op) {

	$.each($('.field_dependency' + dep) , function() {
	
		$(this).hide();

	});

	$('.field_dependency' + dep + '_' + op).show();

	$('#arg_dependency' + dep).html($('#arg_dependency' + dep + '_' + op)[0].innerText);
	
}


var ComponentsBootstrapSwitch = function () {

	var initSwitchBlocks = function() {
		
		$('.form-block').hide();
		$('.form-block-header .tools').hide();
		$('.form-block .form-field-enabled').prop('disabled', true);
		$('.form-block .form-field-disabled').prop('disabled', true);

	}


	function enableBlock (id){
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

		/*$('.params_tadbit_inputs_mapping').change(function() {
				
			var selected = new Array();
        
        $('.params_tadbit_inputs_mapping option:selected').each(function() {
            selected.push($(this).val());
        });
        
        $('.params_tadbit_inputs_mapping option').each(function() {
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

	/*	$('.params_tadbit_inputs_segmentation').change(function() {
				
			var selected = new Array();
        
        $('.params_tadbit_inputs_segmentation option:selected').each(function() {
            selected.push($(this).val());
        });
        
        $('.params_tadbit_inputs_segmentation option').each(function() {
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


		/*$('#resolution').on('input', function() {

			if($(this).val() >= 100000) {
				//$('#gen_matr').parent().show();
				//$('#gen_matr').removeAttr('disabled', 'disabled');
				$('#keep_matrices option:eq(3)').prop('disabled', false);
			}else{
				//$('#gen_matr').parent().hide();
				//$('#gen_matr').attr('disabled', 'disabled');
				$('#keep_matrices option:eq(3)').prop('disabled', true);
			}

			$(".select2_tad3").select2({
				placeholder: "Select keep matrices clicking here",
				width: '100%'
			});


		});*/

		$('#filtering_filters').change(function() {

			var array_selecteds = $(this).val();

			if (array_selecteds !== null) {

				if(array_selecteds.indexOf("5") != -1) {
					$('#fg_min_dist_RE').show();
					$('#filtering_min_dist_RE').prop('disabled', false);
				} else {
					$('#fg_min_dist_RE').hide();
					$('#filtering_min_dist_RE').prop('disabled', true);
				}

				if(array_selecteds.indexOf("6") != -1) {
					$('#fg_min_fragment_size').show();
					$('#filtering_min_fragment_size').prop('disabled', false);
				} else {
					$('#fg_min_fragment_size').hide();
					$('#filtering_min_fragment_size').prop('disabled', true);
				}

				if(array_selecteds.indexOf("7") != -1) {
					$('#fg_max_fragment_size').show();
					$('#filtering_max_fragment_size').prop('disabled', false);
				} else {
					$('#fg_max_fragment_size').hide();
					$('#filtering_max_fragment_size').prop('disabled', true);
				}

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
               $('.warn-tool', $('#tool-input-form')).hide();
               $('.err-tool', $('#tool-input-form')).hide();
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

				// Hi-C Mapping
        $("#mapping_rest_enzyme").rules("add", {
					required: function(element){
						return $("#mapping_iterative_mapping").val() == false;
					}
				});

				$("#mapping_windows").rules("add", {
					regx: /^(1:[0-9]{2} ){0,}(1:[0-9]{2})$/,
					required: function(element){
						return $("#mapping_iterative_mapping").val() == true;
					},
					messages: {
						regx: "You must use the next format: '1:20 1:25 1:30'.",
					}
				});

				
				// Parsing mapped reads
				/*if($("#ref_genome").length) {
					$("#ref_genome").rules("add", {
						required:true
					});
				}

				if($("#ref_genome_fasta").length) {
					$("#ref_genome_fasta").rules("add", {
						required:true
					});
				}

				if($("#ref_genome_gem2").length) {
					$("#ref_genome_gem2").rules("add", {
						required:true
					});
				}*/

				$("#parsing_chromosomes").rules("add", {
					//required:true, 
					regx: /^(chr)?[A-Za-z]?[0-9]{0,3}[XVI]{0,3}(?:ito)?[A-Z-a-z]?$/,
					messages: {
						regx: "You must use the next format: chrX, 1, 2B, chrMito, Mito, chrXIV",
					}
				});


				// Filtering of artifactual reads
				/*$("#filters").rules("add", {
					required:true
				});*/

				$("#filtering_min_fragment_size").rules("add", {
					required:true
				});

				$("#filtering_max_fragment_size").rules("add", {
					required:true
				});

				$("#filtering_min_dist_RE").rules("add", {
					required:true
				});

		
				// Normalization by ICE
				/*$("#resolution").rules("add", {
					required:true
				});*/

				/*"#keep_matrices").rules("add", {
						required:true
					});*/

				/*$("#chromosome_names1").rules("add", {
					regx: /^([0-9a-zA-Z]+ ){0,}([0-9a-zA-Z]+)$/,
					messages: {
						regx: "Matrices, or sub-matrices to save. You can also input a list of chromosome names (e.g.: chr1 chr2 chrX)",
					}
				});*/


				/*$("#intra_chr_matr").rules("add", {
					regx: /^([0-9a-zA-Z]+ ){0,}([0-9a-zA-Z]+)$/,
					messages: {
						regx: "Matrices, or sub-matrices to save. You can also input a list of chromosome names (e.g.: chr1 chr2 chrX)",
					}
				});

				$("#inter_chr_matr").rules("add", {
					regx: /^([0-9a-zA-Z]+ ){0,}([0-9a-zA-Z]+)$/,
					messages: {
						regx: "Matrices, or sub-matrices to save. You can also input a list of chromosome names (e.g.: chr1 chr2 chrX)",
					}
				});

				$("#gen_matr").rules("add", {
					regx: /^([0-9a-zA-Z]+ ){0,}([0-9a-zA-Z]+)$/,
					messages: {
						regx: "Matrices, or sub-matrices to save. You can also input a list of chromosome names (e.g.: chr1 chr2 chrX)",
					}
				});*/


				
				// Segmentation
				/*$(".params_tadbit_inputs_segmentation").each(function() {
        	$(this).rules("add", { 
						required:true, 
						messages: {
							required: "You must select all the file types.",
						}
						require_from_group: [1, ".tadbit-segm-group"],
					});
        });*/
			
				/*if(($("#rich_a").length > 0) && ($("#rich_a").length > 0)) {
					$("#rich_a").rules("add", {
						require_from_group: [1, ".tadbit-segm-group"]
					});

					$("#rich_b").rules("add", {
						require_from_group: [1, ".tadbit-segm-group"]
					});
				}
		
				$("#callers").rules("add", {
					required:true
				});

				$("#chromosome_names2").rules("add", {
					//required:true,
					regx: /^(chr[0-9]+ ){0,}(chr[0-9]+)$/,
					messages: {
						regx: "You must use the next format: 'chr1 chr2 chrX'",
					}
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

var ComponentsTypeahead = function () {

    var handleTwitterTypeahead = function() {

			var substringMatcher = function(strs) {
				return function findMatches(q, cb) {
					var matches, substringRegex;

					// an array that will be populated with substring matches
					matches = [];

					// regex used to determine if a string contains the substring `q`
					substrRegex = new RegExp(q, 'i');

					// iterate through the pool of strings and for any string that
					// contains the substring `q`, add it to the `matches` array
					$.each(strs, function(i, str) {
						if (substrRegex.test(str)) {
							matches.push(str);
						}
					});

					cb(matches);
				};
			};

			var enzymes = $('#enum_mapping_rest_enzyme').val().split(",");

			$('#mapping_rest_enzyme').typeahead({
				hint: true,
				highlight: true,
				minLength: 1,
			},
			{
				name: 'enzymes',
				source: substringMatcher(enzymes),
				limit: 30
			}).on('typeahead:asyncrequest', function() {
				$('.Typeahead-spinner').show();
			})
			.on('typeahead:asynccancel typeahead:asyncreceive', function() {
				$('.Typeahead-spinner').hide();
			});
		
    }

    return {
        //main function to initiate the module
        init: function () {
            handleTwitterTypeahead();
        }
    };

}();

var Select2 = function () {

    var handleSelect2 = function() {
			//console.log($(".select2_tad1"));

			$("#filtering_filters").select2({
				placeholder: "Select one or more filters clicking here",
				width: '100%'
			});

			$(".select2_tad2").select2({
				placeholder: "Select one or more callers clicking here",
				width: '100%'
			});

			$(".select2_tad3").select2({
				placeholder: "Select keep matrices clicking here",
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

	//ComponentsBootstrapSwitch.init();
  ValidateForm.init();
	ComponentsTypeahead.init();
	Select2.init();

});
