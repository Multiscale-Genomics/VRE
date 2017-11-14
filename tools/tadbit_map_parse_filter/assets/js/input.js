var baseURL = $("#base-url").val();

function changeArgDependency(dep, op) {

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

}();



$.validator.addMethod("regx", function(value, element, regexpr) { 
		if(!value) return true;
    return regexpr.test(value);
});

var ValidateForm = function() {

		$('.params_tadbit_inputs_mapping').change(function() {
				
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

		});

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



		$('#filters').change(function() {

			var array_selecteds = $(this).val();

			if (array_selecteds !== null) {

				if(array_selecteds.indexOf("5") != -1) {
					$('#fg_min_dist_RE').show();
					$('#min_dist_RE').prop('disabled', false);
				} else {
					$('#fg_min_dist_RE').hide();
					$('#min_dist_RE').prop('disabled', true);
				}

				if(array_selecteds.indexOf("6") != -1) {
					$('#fg_min_fragment_size').show();
					$('#min_fragment_size').prop('disabled', false);
				} else {
					$('#fg_min_fragment_size').hide();
					$('#min_fragment_size').prop('disabled', true);
				}

				if(array_selecteds.indexOf("7") != -1) {
					$('#fg_max_fragment_size').show();
					$('#max_fragment_size').prop('disabled', false);
				} else {
					$('#fg_max_fragment_size').hide();
					$('#max_fragment_size').prop('disabled', true);
				}

			}

		});

    var handleForm = function() {

        $('#tadbit_map_parse_filter-form').validate({
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
                $('.err-nd', $('#tadbit_map_parse_filter-form')).show();
                $('.warn-nd', $('#tadbit_map_parse_filter-form')).hide();
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
								$('button[type="submit"]', $('#tadbit_map_parse_filter-form')).prop('disabled', true);
               $('.warn-nd', $('#tadbit_map_parse_filter-form')).hide();
               $('.err-nd', $('#tadbit_map_parse_filter-form')).hide();
                var data = $('#tadbit_map_parse_filter-form').serialize();
								data = data.replace(/%5B/g,"[");
                data = data.replace(/%5D/g,"]");
								data = data.replace(/%3A/g,":");
                location.href = baseURL + "applib/launchTool.php?" + data;
                //console.log(data);

            }
        });

        // rules by ID instead of NAME

				// Hi-C Mapping
				$(".params_tadbit_inputs_mapping").each(function() {
        	$(this).rules("add", { 
						required:true, 
						messages: {
							required: "You must select all the file types.",
						}
					});
        });

				if($("#map_refgenome").length) {
					$("#map_refgenome").rules("add", {
						required:true
					});
				}

				if($("#ref_genome_gem1").length) {
					$("#ref_genome_gem1").rules("add", {
						required:true
					});
				}

        $("#rest_enzyme").rules("add", {
					//require_from_group: [1, ".tadbit-map-group"]
					required: function(element){
						return $("#iterative_mapping").val() == false;
					}
				});

				$("#windows").rules("add", {
					regx: /^(1:[0-9]{2} ){0,}(1:[0-9]{2})$/,
					required: function(element){
						return $("#iterative_mapping").val() == true;
					},
					//require_from_group: [1, ".tadbit-map-group"],
					messages: {
						regx: "You must use the next format: '1:20 1:25 1:30'.",
					}
				});

				
				// Parsing mapped reads
				if($("#ref_genome").length) {
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
				}

				$("#chromosomes").rules("add", {
					//required:true, 
					regx: /^(chr)?[A-Za-z]?[0-9]{0,3}[XVI]{0,3}(?:ito)?[A-Z-a-z]?$/,
					messages: {
						regx: "You must use the next format: chrX, 1, 2B, chrMito, Mito, chrXIV",
					}
				});


				// Filtering of artifactual reads
				$("#filters").rules("add", {
					required:true
				});

				$("#min_fragment_size").rules("add", {
					required:true
				});

				$("#max_fragment_size").rules("add", {
					required:true
				});

				$("#min_dist_RE").rules("add", {
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



        $('#tadbit_map_parse_filter-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#tadbit_map_parse_filter-form').validate().form()) {
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

			var enzymes = ["SpoDI","BssHII","EciI","BssMI","AsuNHI","BsrFI","AgsI","AxyI","DpnII","BstSFI","SgrBI","AlwI","SelI","MluI",
			"CseI","NgoMIV","SanDI","CciI","BmeDI","BstHHI","BspLU11I","HpaII","Cfr9I","TspMI","MreI","BclI","MlyI","BspT107I","AhlI","BsaWI",
			"SacI","XspI","KroI","MwoI","BfaI","DrdI","EcoT38I","Cgl13032I","BmgBI","PspN4I","PauI","BglI","SalI","Asp718I","BstOI","PspEI",
			"MspI","UbaF14I","VneI","PspPI","BstH2I","BisI","BstPI","Cdi630V","BanII","MslI","Bsu15I","SgrTI","Nli3877I","EsaSSI","Mva1269I",
			"BmtI","Bsp68I","PspXI","LweI","DsaI","PmaCI","AsiGI","BsaBI","SnaBI","CciNI","Sfr274I","StsI","SmiI","PteI","Ksp22I","BshTI",
			"PabI","BstEII","BmgT120I","TspRI","TstI","BssT1I","TscAI","NcoI","MspA1I","CjeP659IV","PsyI","BtgI","BseJI","BshFI","ClaI",
			"Bsp19I","MauBI","Bse1I","RflFIII","CviJI","AspS9I","BsaI","BmcAI","R2_BceSIV","AbsI","AsuII","FauNDI","BsrBI","DraII","BstMWI",
			"CdiI","Eco24I","AlwNI","Bce83I","XmnI","Eco47III","AclWI","Eco91I","UbaPI","DraI","RpaBI","DraRI","Hpy166II","BthCI","Bst2UI",
			"BseXI","XmaIII","AluBI","Cgl13032II","Vha464I","SdeAI","PsrI","Hpy99I","StyD4I","BstACI","BstXI","PspGI","Psp03I","AcvI","BstDEI",
			"BsiHKAI","GluI","CjuII","BsoBI","Sth132I","AcoI","RruI","BlpI","Esp3I","PshAI","XmaI","BstF5I","CjeFV","BpvUI","BseSI","AdeI",
			"RceI","BtsIMutI","BstENI","BssECI","CjePI","FalI","BssAI","EgeI","Ama87I","BstDSI","SauI","BstV2I","SwaI","AarI","AjnI","RseI",
			"AvaI","PvuI","BspOI","BseAI","DpnI","CspCI","AspLEI","PflFI","BpuEI","Hin6I","PalAI","WviI","Van91I","Zsp2I","AssI","DseDI",
			"Bst1107I","Bme1390I","BveI","BstAUI","UnbI","Psp5II","Bpu14I","NgoAVIII","FaeI","TaqI","BsiYI","BscGI","EaeI","SexAI","Eco52I",
			"BsrI","BspT104I","AseI","BfiI","BbvII","KpnI","Sau96I","SimI","BstNI","FnuDII","HaeII","SspDI","BstSNI","SsiI","AclI","EcoO65I",
			"ApoI","ApaBI","AlwFI","HpyCH4IV","MlsI","NdeI","HapII","PpiI","PinAI","Cfr13I","MboII","AflII","BmrFI","BspGI","SdaI","EcoT14I",
			"TseI","BmsI","TspEI","BglII","TasI","AjuI","AloI","PspPPI","SetI","Tsp4CI","AcsI","BstNSI","BmeRI","BseX3I","FspBI","SchI","RsaNI",
			"VpaK11AI","SmlI","NaeI","BseBI","PfoI","Bpu10I","AccII","BshNI","Hin4II","Rsr2I","SacII","Acc65I","BspQI","CjuI","BmeT110I","BtuMI",
			"MseI","AvrII","MaeI","XapI","TaaI","Aor51HI","PscI","Bsp1407I","Bst2BI","NruI","MvaI","BaeI","RdeGBIII","Sno506I","MjaIV","Hpy99XIV",
			"BtsCI","ChaI","PasI","Ple19I","TseFI","SmiMI","Hin1II","BssKI","Sse8387I","PciI","Bsh1236I","MalI","Pfl1108I","Bse118I","MssI",
			"Asi256I","AvaIII","CpoI","Eco130I","SlaI","BcgI","CspI","BdaI","BsaHI","TaiI","BsnI","CstMI","UbaF13I","SfoI","FspAI","MspJI","BfmI",
			"BsuI","NarI","BmiI","BsePI","Eco47I","BstMCI","TaqII","Bsu36I","NspBII","Bme18I","BoxI","RsaI","MaeII","HincII","BssNAI","BstV1I",
			"AflIII","HpyF10VI","Bsp13I","Bst4CI","MabI","BsiSI","AsuHPI","BtrI","BspCNI","BinI","BsgI","XbaI","RdeGBI","ArsI","Lsp1109I","BsrSI",
			"Sau3AI","StrI","Cfr10I","BstC8I","BfuAI","TfiI","Hpy99XIII","TspGWI","AjiI","PmlI","RlaI","CjeFIII","BbvI","Psp124BI","Bsp119I","MboI",
			"GauT27I","GlaI","Bsp24I","TssI","HgaI","MvnI","BanI","PspOMII","BstX2I","AluI","BaeGI","SduI","NmeDI","ZraI","SgsI","BseGI","Hpy188III",
			"PenI","Eco72I","Bse21I","RsrII","Sfr303I","BspMI","BcnI","BstSCI","MluCI","UbaF12I","BpuMI","SgeI","AciI","Mph1103I","ScrFI","EcoRII",
			"Alw21I","MscI","HaeI","BseYI","CfoI","CchII","Hpy8I","HgiCI","DrdII","Bse3DI","PsuI","Bso31I","CviQI","AccB7I","PaeI","BstKTI","BmrI",
			"SseBI","Hpy188I","AccBSI","SmaI","PleI","BasI","EcoNI","NheI","UcoMSI","BplI","BccI","Ppu21I","SstI","SmoI","FaqI","FspEI","BsiEI",
			"BspACI","StuI","AspA2I","BcoDI","Bsp1720I","BcuI","AspBHI","BspTI","Bbr7I","Bsc4I","BbrPI","BsiWI","SphI","HpyAV","Mly113I","GsuI",
			"NciI","PlaDI","SfeI","Ppu10I","BseLI","FriOI","MaeIII","AasI","FspI","CviAII","PvuII","Eco53kI","EheI","MfeI","MflI","EsaBC3I","BsrDI",
			"CauII","BssSI","NhaXI","ErhI","SspD5I","XagI","BshVI","FokI","Sse232I","Hin1I","RpaTI","ApaLI","Alw26I","AsuC2I","ApeKI","AbaSI","SpeI",
			"SgrDI","GsaI","Eco31I","SciI","HinfI","BciVI","HinP1I","Eam1105I","BsuRI","TsoI","MvrI","BspNCI","BscAI","PpsI","BstPAI","BsiHKCI","XmiI",
			"Eam1104I","BceAI","Ecl136II","XmaJI","SfaAI","MspCI","HspAI","RgaI","HphI","Psp1406I","Fsp4HI","Kzo9I","Acc36I","Csp6I","BsmAI","DraIII",
			"EcoO109I","Hpy178III","Acc16I","BetI","BseMII","MluNI","MspR9I","BtsI","SapI","PpuMI","SgfI","EcoRV","PsiI","AatII","BfuI","MroXI","EcoRI",
			"BsmFI","TauI","XhoI","ZrmI","VpaK11BI","BspD6I","BseCI","DinI","FmuI","Bsp143I","Bsp1286I","BspPI","SstE37I","BstFNI","AfiI","RpaB5I",
			"BcefI","SplI","PluTI","BslFI","Bsa29I","CfrI","MnlI","McrI","PfeI","AccIII","CaiI","Bpu1102I","MunI","Tru1I","EagI","Aor13HI","BspLI",
			"AscI","AhdI","NlaIII","SbfI","EclXI","Eco105I","NsbI","MkaDII","PstI","LpnI","Bse8I","Jma19592I","FauI","SfcI","BspEI","LguI","VspI",
			"BsmI","BstZI","AceIII","PciSI","Alw44I","MaqI","SfiI","CdpI","Bst6I","Hsp92I","BpiI","BstUI","XceI","CchIII","PspLI","BlnI","Tth111II",
			"BspFNI","TsuI","CjeNIII","BstZ17I","HgiJII","Msp20I","KasI","UbaF9I","Bbv12I","BseMI","Eco57MI","SdeOSI","HaeIII","BsmBI","Kpn2I",
			"Cfr42I","McaTI","XcmI","SatI","BsbI","BciT130I","LpnPI","BstAPI","NdeII","NlaCI","Ksp632I","AccI","Hin4I","SspI","HpyCH4III","BsrGI",
			"FinI","AfeI","SrfI","Tth111I","CspAI","FbaI","AsuI","Eco32I","KflI","SgrAI","SecI","UbaF11I","CjeNII","NsiI","AquII","BseDI","AquIV",
			"KspAI","BstMBI","GdiII","YkrI","Eco81I","BspHI","BstYI","PspOMI","BauI","AanI","EcoHI","PmeI","ApyPI","FseI","BstMAI","BstSLI","ApaI",
			"BlsI","SaqAI","PshBI","EcoT22I","SfuI","Eco88I","BseRI","FblI","MmeI","BmgI","ScaI","CviRI","TspDTI","AlfI","BmuI","PcsI","RpaI","Eco57I",
			"AgeI","BtgZI","BarI","AhaIII","PctI","FaiI","Eco147I","BpmI","NspV","EarI","PspPRI","CviKI_1","CjeI","PstNI","BsiI","AcuI","OliI","BfuCI",
			"BfrI","NspI","PacI","PdmI","AccB1I","BstBI","BalI","BssNI","HpyCH4V","CsiI","PceI","NlaIV","AquIII","HpySE526I","XhoII","Sse9I","KspI","HauII",
			"Tru9I","Sth302II","Hsp92II","MhlI","BbsI","MstI","BstBAI","DdeI","PssI","AfaI","Bsp120I","NotI","BsaXI","MroI","DriI","MroNI","AoxI","HgiAI",
			"MbiI","Asp700I","HindIII","AcyI","EcoICRI","FatI","BamHI","BslI","AvaII","BspDI","PaeR7I","Psp6I","BstAFI","BfoI","SnaI","SfaNI","RigI","TatI",
			"HpaI","BsaJI","BbvCI","RdeGBII","PspCI","Fnu4HI","Cac8I","HpyF3I","Tsp45I","EspI","Pfl23II","Bsh1285I","Sse8647I","RleAI","BspMII","StyI",
			"PflMI","HhaI","HindII","AsiSI","NmuCI","HgiEII","BseNI","PagI","PdiI","AleI","NmeAIII","BsaAI"];

			$('#rest_enzyme').typeahead({
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

			$(".select2_tad1").select2({
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
