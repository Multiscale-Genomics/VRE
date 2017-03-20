//
// JavaScript Functions to select/unselect Nucleotide Sequence Pieces,
// and/or hide/unhide pieces information.
//

var baseURL = $('#base-url').val();
var urlData = baseURL + 'tools/naflex/';


// Sequence Selected Nucleotide/BasePairStep/Tetramer.
var nucSelected = "";
var bpsSelected = "";
var tetSelected = "";
var stepSelected = "";

// Helical Parameter/J-coupling/NOE Selected
var helParameter = "";
var Jcoupling = "";

// CURVES functions

function unhideSections(selID,divID) {

    hideAll("HelicalParamsSections");
    hideAll("HelicalParamsSectionsTime");
    hideAll("HelicalParamsPlots");
    unselectAll("CurvesParams");
    hideAll("HelicalParamsPlotsTime");
    hideAll("CurvesPlots");
    hideAll("StatsTable");

	var div = document.getElementById("AlphaSelPlot");
	if (div) {
		//div.innerHTML = '';
		div.className='hidden';
    	}

    if (selID == "AVGSel") {
	document.getElementById("TIMESel").className = 'unselected';
    	hideAll("CurvesHelicalParamsSections");
    	hideAll("CurvesParams");
    	hideAll("CurvesPlots");
	unselectAllNucs();
	//nucSelected="";
    }

    if (selID == "TIMESel") {
	document.getElementById("AVGSel").className = 'unselected';
    	hideAll("CurvesParams");
    	hideAll("CurvesHelicalParamsSections");
    	hideAll("CurvesPlots");
	unselectAllNucs();
    }

    var item = document.getElementById(divID);
    if (item) {
        item.className=(item.className=='hidden')?'unhidden':'hidden';
    }
    var sel = document.getElementById(selID);
    if (sel) {
       sel.className=(sel.className=='selected')?'unselected':'selected';
    }


    if(divID == "backboneTorsions") {
	unselectAllNucs();
	selectBackbone();
    }

    if(divID == "BP_HelicalParms" || divID == "Axis") {
	unselectAllNucs();
	selectBasePairs();
    }

    if(divID == "BPS_HelicalParms") {
	unselectAllNucs();
	selectBasePairSteps();
    }

    if(divID == "Grooves") {
	unselectAllNucs();
	selectBasePairSteps(); // Grooves involve also base pair steps.
    }
}

function unhideSelPlots(selID,divID) {

    hideAll("HelicalParamsPlotsTime");
    unselectAll("HelicalParamsSectionsTime");
    helParameter = '';
    hideAll("StatsTable");

    var back = document.getElementById("bckTorsionsTimeSel");
    var bp = document.getElementById("BP_HelicalParmsTimeSel");
    var axis = document.getElementById("AxisTimeSel");
    var bps = document.getElementById("BPS_HelicalParmsTimeSel");
    var grooves = document.getElementById("GroovesTimeSel");


    if(nucSelected == "" && back.className=="selected") {
	//alert("You must select a Nucleotide first.\n");
		App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});	
    }
    else if (bpsSelected == "" && (bp.className=="selected" || axis.className=="selected") ) {
	//alert("You must select a Nucleotide Base Pair ( | ) first.\n");
		App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide Base Pair ( | ) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});	

    }
    else if (tetSelected == "" && ((bps.className=="selected") || (grooves.className=="selected")) ) {
	//alert("You must select a Nucleotide Tetramer ( X ) first.\n");
			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide Tetramer ( X ) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});	

    }
    else {
	var item = document.getElementById(divID);
	if (item) {
	        item.className=(item.className=='hidden')?'unhidden':'hidden';

		// Mean & Stdev
		id = item.id;
		var matches = id.match(/[\w-]+TimeSelPlot/g);
		if (matches != undefined) 
			var m = matches.toString().replace("TimeSelPlot","");

		helParameter = m;

		if(nucSelected){
			code = "curves."+m+"-"+nucSelected;
			//alert("Code: "+code);
		}
		else if(bpsSelected){
			code = "curves."+m+"-"+bpsSelected;
		}
		else if(tetSelected){
			code = "curves."+m+"-"+tetSelected;
		}
	

	        var mean = document.getElementById(code);
	       	if (mean) 
        	        mean.className=(mean.className=='hidden')?'unhidden':'hidden';
	}
	var sel = document.getElementById(selID);
	if (sel) {
	       sel.className=(sel.className=='selected')?'unselected':'selected';
	}
    }
}

function unhidePlots(selID,divID) {

    hideAll("HelicalParamsPlots");
    hideAll("BI-BII-graphic-div");
    hideAll("Alpha-Gamma-graphic-div");
    hideAll("Puckering-graphic-div");
    unselectAll("HelicalParamsSections");

    if(selID == "BISel")
	document.getElementById("BI-BII-graphic").className = "unhidden";
    if(selID == "AGSel")
	document.getElementById("Alpha-Gamma-graphic").className = "unhidden";
    if(selID == "EZSel")
	document.getElementById("Alpha-Gamma-graphic").className = "unhidden";
    if(selID == "PuckSel")
	document.getElementById("Puckering-graphic").className = "unhidden";

    var item = document.getElementById(divID);
    if (item) {
        item.className=(item.className=='hidden')?'unhidden':'hidden';
    }
    var sel = document.getElementById(selID);
    if (sel) {
       sel.className=(sel.className=='selected')?'unselected':'selected';
    }
}

function unselectAll(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
	    var fc = d.getElementsByTagName("a");
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
                                if(h.className=='selected')
                                        h.className='unselected';
                        }
                }
    }
}

function selectAll(divID) {

    var d = document.getElementById(divID);
    if (d)
    {
            var fc = d.getElementsByTagName("a");
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
                                if(h.className=='unselected')
                                        h.className='selected';
                        }
                }
    }
}

function selectBasePairSteps(){

    var d = document.getElementById("divSeq2");

    if (d)
    {
            var fc = d.getElementsByTagName("a");
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
                                var bck_id = h.id;
                                var matches = bck_id.match(/-[A-Z][A-Z][A-Z][A-Z]$/g);
                                for (m in matches){
                                        if(h.className=='unselected')
                                              h.className='selected';
                                }
                        }
                }
    }
}

function selectBasePairs(){

    var d = document.getElementById("divSeq2");

    if (d)
    {
            var fc = d.getElementsByTagName("a");
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
                                var bck_id = h.id;
                        	var matches = bck_id.match(/-[A-Z][A-Z]$/g);
				for (m in matches){
                	                if(h.className=='unselected')
                        	              h.className='selected';
				}
                        }
                }
    }
}

function selectBackbone(){

    var d = document.getElementById("divSeq2");

    if (d)
    {
            var fc = d.getElementsByTagName("a");
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];			
                        if(h) {
				var bck_id = h.id;
				var backbone = bck_id.substr(-1);
				if(backbone == "-"){
                                	if(h.className=='unselected')
                                  	      h.className='selected';
				}
                        }
                }
    }
}

function hideDiv(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
	d.className='hidden';
    }
}

function unselectDiv(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        d.className='unselected';
    }
}

function selectDiv(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        d.className='selected';
    }
}

function hideAll(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
            //var fc = d.childNodes;
	    var fc = d.getElementsByTagName('*');
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
                                if(h.className=='unhidden')
                                        h.className='hidden';
                        }
                }
    }
}

function unselectAllNucs() {

    var d = document.getElementById("divSeq2");
    if (d)
    {
    	    var fc = d.getElementsByTagName("a");
		for(var i = 0; i < fc.length; i++)
	        {
			var h = fc[i];
			if(h) {
				if(h.className=='selected')
					h.className='unselected';
			}
		}
    }
    bpsSelected = '';
    nucSelected = '';
    stepSelected = '';
    tetSelected = '';
}

function selectAllNucs() {

    var d = document.getElementById("divSeq2");
    if (d)
    {
    	    var fc = d.getElementsByTagName("a");
		for(var i = 0; i < fc.length; i++)
	        {
			var h = fc[i];
			if(h) {
				if(h.className=='unselected')
					h.className='selected';
			}
		}
    }
}

function selectTetramer(nuc,length) {

    if(nuc) {
	var arr = nuc.split("-");
	var num1 = arr[0];
	var num2 = num1*1 + 1;
	var tetArr = arr[1].split("");
	var letter1 = tetArr[0];
	var letter2 = tetArr[1];
	var letter3 = tetArr[2];
	var letter4 = tetArr[3];

	var code1 = num1+"-"+letter1;
	var code2 = num2+"-"+letter2;
	var num3 = length*2 - num1*1;
	var num4 = num3*1 + 1;
	var code3 = num3+"-"+letter3;
	var code4 = num4+"-"+letter4;

	var codeBPS1 = num1+"-"+letter1+letter4;
	var codeBPS2 = num2+"-"+letter2+letter3;

	var codeBPS1_bis = num1+"-"+letter1+":"+num4+"-"+letter4;
	var codeBPS2_bis = num2+"-"+letter2+":"+num3+"-"+letter3;
	var codeBP1 = num1+"-";
	var codeBP2 = num4+"-";

	selectId(code1);
	selectId(code2);
	selectId(code3);
	selectId(code4);
	selectId(codeBP1);
	selectId(codeBP2);
	selectId(codeBPS1);
	selectId(codeBPS2);
	selectId(codeBPS1_bis);
	selectId(codeBPS2_bis);
    }
}

function selectBaseStep(nuc) {

    if(nuc) {
	// 16-G:17-T
	var arr = nuc.split(":");
	var code1 = arr[0];
	var code2 = arr[1];
	
	selectId(code1);
	selectId(code2);
	selectId(nuc);
    }
}

function selectBasePairStep(nuc,length) {

    if(nuc) {
	var arr = nuc.split("-");
	var num = arr[0];
	var pairArr = arr[1].split("");
	var letter1 = pairArr[0];
	var letter2 = pairArr[1];

	var code1 = num+"-"+letter1;

	var num2 = length*2 - num + 1;
	var code2 = num2+"-"+letter2;

	selectId(code1);
	selectId(code2);
	selectId(nuc);
    }
}

function selectId (divID) {

    var nuc = document.getElementById(divID);
    if(nuc)
	nuc.className = 'selected';
}

function selectNucleotide(nucID,path) {

    var nuc = document.getElementById(nucID);
    if (nuc) {
        unselectAllNucs();

        if(nuc.className=='unselected'){
		nuc.className='selected';
		if(nuc.name != 'undefined') {
	        	if(nuc.name=='BPS'){
				bpsSelected = nuc.id;
				nucSelected = '';
				tetSelected = '';
			//	selectBasePairStep(nucID);
			}
	        	else if(nuc.name=='TET'){
				tetSelected = nuc.id;
				nucSelected = '';
				bpsSelected = '';
			}
			else {
				nucSelected = nuc.id;
				bpsSelected = '';
				tetSelected = '';
			}
		}
	}

	// Unhide Mean/Stdev Table
	var param = helParameter;
	var code = "curves."+param+"-"+nuc.id;
	hideAll("StatsTable");

	//alert("Code: "+code);
	var tab = document.getElementById(code);
	if (tab){
		tab.className='unhidden';
		var divID = param+"TimeSelPlot";
		var item = document.getElementById(divID);
		if (item) 
		        item.className='unhidden';
	}
	else{
		hideAll("HelicalParamsPlotsTime");
	}
  	  var d = document.getElementById("HelicalParamsPlotsTime");
	  if (d)
	  {
                var fc = d.childNodes;
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
				var divID = h.id;
				if(divID) {
        	                        var matches = divID.match(/Time_\w+/g);
					if (matches != undefined) {
						//alert("Matches: "+matches);
						var section = matches.toString().replace("Time_","");

				                var fc2 = h.childNodes;
				                for(var j = 0; j < fc2.length; j++)
				                {
				                        var h2 = fc2[j];
				                        if(h2) {
				                                var divID2 = h2.id;
				                                if(divID2) {
				        	                        var matches = divID2.match(/\w+TimeSelPlot/g);
									//alert("Matches2: "+matches);
									if (matches != undefined) {
										var m = matches.toString().replace("TimeSelPlot","");
										//alert("Matches M: "+m);
		
		// R Plot
		var txt = "<img border='1' width='900' src='"+urlData + path+"/"+section+"/"+nuc.id+"/"+m+".dat.png'>";

		// Download Buttons
		txt += '<table align="" border="0"><tr><td>';
	//	txt += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+section+'/'+nuc.id+'/'+m+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
		txt += '</td><td>';
		txt += '<a href="'+urlData+'getFile.php?fileloc='+path+'/'+section+'/'+nuc.id+'/'+m+'.dat&type=curves" style=""> <p align="right" class="btn blue">Download Raw Data</p></a>';
		txt += '</td></tr></table>';
		h2.innerHTML = txt;
									}
								}
							}
						}
					}
				}
                        }
                }
	  }
    }
}

function unhide(divID) {
    var item = document.getElementById(divID);
    if (item) {
        item.className=(item.className=='hidden')?'unhidden':'hidden';
    }
}

// STIFFNESS functions

function selectNucleotideStiffness(nucID,path) {

    hideAll("BPParamsPlots");
    hideAll("StatsTable");

    var nuc = document.getElementById(nucID);
    if (nuc) {
        unselectAllNucs();

        if(nuc.className=='unselected'){
		nuc.className='selected';
		if(nuc.name != 'undefined') {
	        	if(nuc.name=='BPS'){
				bpsSelected = nuc.id;
				tetSelected = '';
				nucSelected = '';
			//	selectBasePairStep(nucID);
			}
	        	else if(nuc.name=='TET'){
				tetSelected = nuc.id;
				bpsSelected = '';
				nucSelected = '';
			}
			else {
				nucSelected = nuc.id;
				tetSelected = '';
				bpsSelected = '';
			}
		}
	}

	// Unhide Mean/Stdev Table
	var param = helParameter;
	var code = "stiffness."+param+"-"+nuc.id;
	hideAll("StatsTable");

	var tab = document.getElementById(code);
	if (tab){
		tab.className='unhidden';
		var divID = param+"TimeSelPlot";
		var item = document.getElementById(divID);
		if (item) 
		        item.className='unhidden';
	}
	else{
		hideAll("HelicalParamsPlotsTime");
	}

	// Unhide Stiffness Table
	var table = "stiffness."+nuc.id;
	var d = document.getElementById(table);
	if (d)
	{
		//d.className=(d.className=='hidden')?'unhidden':'hidden';
		d.className = 'unhidden';
	}

	var d = document.getElementById("HelicalParamsPlotsTime");
	if (d)
	{
                var fc = d.childNodes;
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
				var divID = h.id;
				if(divID) {
        	                        var matches = divID.match(/Time_\w+/g);
					if (matches != undefined) {
						//alert("Matches: "+matches);
						var section = matches.toString().replace("Time_","");

				                var fc2 = h.childNodes;
				                for(var j = 0; j < fc2.length; j++)
				                {
				                        var h2 = fc2[j];
				                        if(h2) {
				                                var divID2 = h2.id;
				                                if(divID2) {
				        	                        var matches = divID2.match(/\w+TimeSelPlot/g);
									//alert("Matches2: "+matches);
									if (matches != undefined) {
										var m = matches.toString().replace("TimeSelPlot","");
										//alert("Matches M: "+m);
	// R Plot
	var txt = "<img border='1' width='900' src='"+urlData +path+"/FORCE_CTES/"+nuc.id+"/"+m+".dat.png'>";

	// Download Buttons
	txt += '<table align="" border="0"><tr><td>';
	//txt += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/FORCE_CTES/'+nuc.id+'/'+m+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
	txt += '</td><td>';
	txt += '<a href="'+urlData+'getFile.php?fileloc='+path+'/FORCE_CTES/'+nuc.id+'/'+m+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
	txt += '</td></tr></table>';
	h2.innerHTML = txt;
									}
								}
							}
						}
					}
				}
                        }
                }
	}
    }
}


function unhideStiffnessAvgPlots(selID,divID) {

    hideAll("StiffnessParams");
    hideAll("BPS_Stiffness");
    unselectAll("HelicalParams");
    unselectAllNucs();
    tetSelected = '';
    helParameter = '';

    var sel = document.getElementById("TIMESel");
    if (sel) 
           sel.className='unselected';

        var item = document.getElementById(divID);
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
        }
        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }
}

function unhideStiffnessTimePlots(selID,divID) {

    hideAll("StiffnessParams");
    hideDiv("HelicalParams");
    hideDiv("BPS_HelicalParms");
    unselectAll("BPS_HelicalParmsTime");
    unselectAllNucs();

    var sel = document.getElementById("AVGSel");
    if (sel) 
           sel.className='unselected';

        var item = document.getElementById(divID);
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
        }
        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }
}

function unhideStiffnessBPPlots(selID,divID) {

    hideAll("BPS_Stiffness"); 
    hideAll("HelicalParamsPlots");
    hideAll("HelicalParamsPlotsTime");

    var sel = document.getElementById("BPS_HelicalParmsSel");
    if (sel) {
           sel.className='unselected';
    }

    if (tetSelected == "") {
	//alert("You must select a Nucleotide Tetramer ( X ) first.\n");
		App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide Tetramer ( X ) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});	
    

    }
    else {
	var item = document.getElementById(divID);
	if (item) 
	        item.className=(item.className=='hidden')?'unhidden':'hidden';
	
	var sel = document.getElementById(selID);
	if (sel) { 
		sel.className=(sel.className=='selected')?'unselected':'selected';
		if(sel.className == 'unselected')
			unselectAllNucs();
	}
    }
}

function unhideStiffnessHPPlots(selID,divID) {

    unselectAll("BPS_HelicalParms");
    unselectDiv("StiffnessBPSel");
    hideAll("StiffnessParams");
    unselectAllNucs();

	var item = document.getElementById(divID);
	if (item) 
	        item.className=(item.className=='hidden')?'unhidden':'hidden';
	
	var sel = document.getElementById(selID);
	if (sel) { 
		sel.className=(sel.className=='selected')?'unselected':'selected';
		if(sel.className == 'unselected')
			unselectAllNucs();
	}
}

function unhideSelStiffnessPlots(selID,divID) {

    hideAll("HelicalParamsPlotsTime");
    hideAll("StatsTable");
    unselectAll("BPS_HelicalParmsTime");
    helParameter = '';

    if (tetSelected == "") {
        //alert("You must select a Nucleotide Tetramer ( X ) first.\n");
			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide Tetramer ( X ) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});	


    }
    else {
        var item = document.getElementById(divID);
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
        }
        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }

	var m = divID.replace("TimeSelPlot","");
	helParameter = m;

	var code = "stiffness."+m+"-"+tetSelected;

	var mean = document.getElementById(code);
	if (mean){
		mean.className=(mean.className=='hidden')?'unhidden':'hidden';
	}
    }
}

function selectStiffnessTetramer(nuc,length) {

	//hideAll("StiffnessTimeSel");
	//hideAll("BPS_Stiffness");
        //hideAll("HelicalParamsPlotsTime");
        hideAll("StiffnessParams");
	selectTetramer(nuc,length);
	var sel = document.getElementById("AVGSel");
	if (sel) 
		sel.className='selected';
	var sel = document.getElementById("StiffnessHPSel");
	if (sel) 
		sel.className='selected';
	var sel = document.getElementById("TIMESel");
	if (sel) 
		sel.className='unselected';
	var sel = document.getElementById("BPS_HelicalParmsSel");
	if (sel) 
		sel.className='unselected';

	var arr = nuc.split("-");
	var num1 = arr[0];
	var div = document.getElementById("stiffness."+num1);
	if(div)
		div.className='unhidden';
	var div = document.getElementById("BPParamsPlots");
	if(div)
		div.className='unhidden';
}

// NMR functions

function selectNucleotideNew(analysis,nucID,path) {

    var donttouchimage = 0;
    var nuc = document.getElementById(nucID);
    if (nuc) {
        unselectAllNucs();

        if(nuc.className=='unselected'){
		nuc.className='selected';
		if(nuc.name != 'undefined') {
	        	if(nuc.name=='BPS'){
				bpsSelected = nuc.id;
				tetSelected = '';
				nucSelected = '';
				stepSelected = '';
			//	selectBasePairStep(nucID);
			}
	        	else if(nuc.name=='TET'){
				tetSelected = nuc.id;
				bpsSelected = '';
				nucSelected = '';
				stepSelected = '';
			}
	        	else if(nuc.name=='BS'){
				stepSelected = nuc.id;
				tetSelected = '';
				nucSelected = '';
				bpsSelected = '';
			}
			else {
				nucSelected = nuc.id;
				tetSelected = '';
				bpsSelected = '';
				stepSelected = '';
			}
		}
	}

	var nucid = nuc.id;
	var arr = nucid.split("-");
	var num = arr[0];
	var nucOneLetter = arr[1];
	var nucOneLetter2;
	var num2;
	var numPair;
	var reves;

	// If coming from Base Step (17-C:18-G)
	var bpLetter = nucid.match(/:/);
	if (bpLetter != undefined){
		var arrNOL = nucid.split(":");
		var arrNOL1 = arrNOL[1].split("-");
		nucOneLetter = arrNOL1[1];
		var arrNOL2 = arrNOL[0].split("-");
		nucOneLetter2 = arrNOL2[1];
		num2 = arrNOL2[0];
		numPair = arrNOL1[0];
		
		reves = nucOneLetter2.match(/[TUC]/);

		var at = nucOneLetter2.match(/[ATU]/);
		if(at != undefined){
			var d = document.getElementById("AT_N1H3");
			if(d)
				d.className="curvesText";
			var d = document.getElementById("AT_H61O4");
			if(d)
				d.className="curvesText";

			var d = document.getElementById("CG_O6H41");
			if(d)
				d.className="hidden";
			var d = document.getElementById("CG_H1N3");
			if(d)
				d.className="hidden";
			var d = document.getElementById("CG_H21O2");
			if(d)
				d.className="hidden";
			Jcoupling = "AT";
		}

		var cg = nucOneLetter2.match(/[CG]/);
		if(cg != undefined){
			var d = document.getElementById("AT_N1H3");
			if(d)
				d.className="hidden";
			var d = document.getElementById("AT_H61O4");
			if(d)
				d.className="hidden";

			var d = document.getElementById("CG_O6H41");
			if(d)
				d.className="curvesText";
			var d = document.getElementById("CG_H1N3");
			if(d)
				d.className="curvesText";
			var d = document.getElementById("CG_H21O2");
			if(d)
				d.className="curvesText";
			Jcoupling = "CG";
		}
	}

	if(analysis == 'nmrNOE' || analysis == 'HBs'){
		if(nucOneLetter == 'C' || nucOneLetter == 'U'){
			var d = document.getElementById("H5-H6");
			if(d)
				d.className="curvesText_hidden unhidden_curves";
		}
		else{
			var d = document.getElementById("H5-H6");
			if(d)
				d.className="curvesText_hidden hidden";

			if(Jcoupling == 'Nuc_H5-Nuc_H6' ){

				// Changing Ribose Image 
				hideAll("Rib_images");
				hideAll("HelicalParamsPlotsTime");
	    			hideAll("StatsTable");

				var d = document.getElementById("Rib");
				if(d)
					d.className="unhidden";
				donttouchimage = 1;
				//return;
			}
		}

		var mat = Jcoupling.match(/Step/);
		// If previous plot was for Base Step and the current one is for single nucleotide (or the other way around), reset visualization.
		if (((bpLetter == undefined) && (mat != undefined)) || ((bpLetter != undefined) && (mat == undefined)) || ((tetSelected != '') && (bpLetter == undefined)) ) {
			// Changing Ribose Image 
				hideAll("Rib_images");
				hideAll("HelicalParamsPlotsTime");
	    			hideAll("StatsTable");

				var d = document.getElementById("Rib");
				if(d)
					d.className="unhidden";

				unselectAll("Proton_Pairs_Params");
				//return;
				donttouchimage = 1;
		}
	}

	if(analysis == 'nmrJ' || analysis == 'nmrNOE' || analysis == 'HBs')
	    hideAll("StatsTable");

	if(analysis == 'stiffness'){
	    hideAll("BPParamsPlots");

		var table = "stiffness."+nuc.id;
		var d = document.getElementById(table);
		if (d)
			d.className = 'unhidden';
	}

	var path2 = nucid;
	var d = document.getElementById("HelicalParamsPlotsTime");
	if (d)
	{
                var fc = d.childNodes;
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
				var divID = h.id;
				if(divID) {
					var matches = divID.match(/Time_\w+/g);
					if (matches != undefined) {
						//alert("Matches: "+matches);
						var section = matches.toString().replace("Time_","");
						if(analysis=='stiffness'){
							section = "FORCE_CTES";
							path2 = "FORCE_CTES/"+nuc.id;
						}
						if(analysis=='nmrJ' || analysis=='nmrNOE'){
							section = '';
							path2 = num;
						}
						if(analysis=='HBs'){
							section = '';
							path2 = num2;
						}

		                var fc2 = h.childNodes;
		                for(var j = 0; j < fc2.length; j++)
		                {
	                        var h2 = fc2[j];
	                        if(h2) {
                                var divID2 = h2.id;
                                if(divID2) {
        	                        var matches = divID2.match(/[\w-]+TimeSelPlot/g);
					if (matches != undefined) {
						if(analysis=='HBs'){
							var m = matches.toString().replace("TimeSelPlot","");
							if(reves != undefined){
								m = m.replace(/Nuc/,numPair);
								m = m.replace(/Nuc/,num2);
							}
							else{
								m = m.replace(/Nuc/,num2);
								m = m.replace(/Nuc/,numPair);
							}
						}
						else{

							var m = matches.toString().replace("TimeSelPlot","");
        			                        var matches = m.match(/Nuc_/g);
							if (matches != undefined) {
								m = m.toString().replace(/Nuc/,path2);
								matches = divID2.match(/Step/g);
								if(matches != undefined){
									m = m.replace("-Step","");
									num2 = parseInt(path2) + 1;
									m = m.replace(/Nuc/,num2);
								}
								else{
									m = m.replace(/Nuc/,path2);
								}
								if(nucOneLetter == 'G' || nucOneLetter == 'A' ){
									m = m.toString().replace("H6","H8");
								}
							}
						}
	// R Plot
	var txt = "<img border='1' width='900' src='"+urlData +path+"/"+section+"/"+path2+"/"+m+".dat.png'>";

	// Download Buttons
	txt += '<table align="" border="0"><tr><td>';
	//txt += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+section+'/'+path2+'/'+m+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
	txt += '</td><td>';
	txt += '<a href="'+urlData +'getFile.php?fileloc='+path+'/'+section+'/'+path2+'/'+m+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
	txt += '</td></tr></table>';
	h2.innerHTML = txt;
									
									}
								}
							}
						}
					}
				}
                        }
                }
	}
	if( analysis == 'nmrJ' || (analysis == 'nmrNOE' && (donttouchimage == 0)) ){

		var code = "nmrJ."+Jcoupling+"-"+num;
		if(analysis == 'nmrNOE' && bpLetter == undefined){
			code = code.replace(/Nuc/g,num);
		}
		else if (bpLetter != undefined){
			code = code.replace(/-Step/,"");
			code = code.replace(/Nuc/,num);
			code = code.replace(/Nuc/,num2);
		}

		var matchesH6 = Jcoupling.match(/H6/);
		if((nucOneLetter == 'G' || nucOneLetter == 'A') && matchesH6 != undefined ){
			code = code.toString().replace("H6","H8");
		}

		var mean = document.getElementById(code);
		if (mean){
			mean.className='unhidden';
			var item = document.getElementById(helParameter);
			if (item) 
			        item.className='unhidden';
			var param = helParameter;
			var matches = param.match(/Step/);
			if(matches != undefined){
				param = param.replace("StepTimeSelPlot","StepSel");
			}
			else{
				param = param.replace("TimeSelPlot","Sel");
			}
			var item = document.getElementById(param);
			if (item)
				item.className = 'selected';
		}
		else{
			hideAll("Rib_images");
			hideAll("HelicalParamsPlotsTime");
	    		hideAll("StatsTable");

			var d = document.getElementById("Rib");
			if(d)
				d.className="unhidden";
			donttouchimage = 1;
		}
	}
	if(analysis == 'HBs' || (analysis == 'nmrNOE' && donttouchimage == 0)){

	    if(Jcoupling != undefined){
		if(Jcoupling != ""){
		// Changing Ribose Image 
		hideAll("Rib_images");

		var step = Jcoupling.match(/Step/);
		var matchesH6 = Jcoupling.match(/H6/);
		if (step != undefined || analysis == 'HBs'){
			var image = Jcoupling+"SelRib";

			if((nucOneLetter == 'G' || nucOneLetter == 'A') && matchesH6 != undefined ) 
				image = image.toString().replace("H6","H8");
			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';
		}
		else if((nucOneLetter == 'G' || nucOneLetter == 'A') && matchesH6 != undefined ){

			var image = nucOneLetter+"_"+Jcoupling+"SelRib";
			image = image.toString().replace("H6","H8");
			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';

		}
		else if(matchesH6 != undefined){

			var image = nucOneLetter+"_"+Jcoupling+"SelRib";
			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';
		}
		else {
			var image = Jcoupling+"SelRib";

			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';
		}
		}
	    }
	}
    }
}

function unhideSelHBsPlots(selID,divID) {

    hideAll("HelicalParamsPlotsTime");
    hideAll("StatsTable");
    unselectAll("Proton_Pairs_Params");
    helParameter = '';

    if (bpsSelected == "") {
        //alert("You must select a Base Pair (|) first.\n");
			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Base Pair (|) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});	

    }
    else {
	var nucOneLetter = '';
        var item = document.getElementById(divID);
	var num;
	var code;
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
		helParameter = divID;
		id = item.id;
		// J-coupling Mean & Stdev
		var matches = id.match(/[\w-]+TimeSelPlot/g);
		if (matches != undefined){ 
			var m = matches.toString().replace("TimeSelPlot","");
		}
        }
        var sel = document.getElementById(selID);
        if (sel) 
               sel.className=(sel.className=='selected')?'unselected':'selected';

	var bpLetter = bpsSelected.match(/:/);
	if (bpLetter != undefined){
		var arrNOL = bpsSelected.split(":");
		var arrNOL1 = arrNOL[0].split("-");
		var arrNOL2 = arrNOL[1].split("-");
		num = arrNOL1[0];
		num2 = arrNOL2[0];
		nucOneLetter = arrNOL1[1];
		nucOneLetter2 = arrNOL2[1];
		
		var reves = nucOneLetter.match(/[TUC]/);

		code = "nmrJ."+m+"-"+num;
		if(reves != undefined){

			code = code.replace(/Nuc/,num2);
			code = code.replace(/Nuc/,num);
			var arrM = code.replace(/nmrJ./,'').split("-");
			code = "nmrJ."+arrM[1]+"-"+arrM[0]+"-"+arrM[2];
		}
		else{
			code = code.replace(/Nuc/,num);
			code = code.replace(/Nuc/,num2);
		}
	}

        var mean = document.getElementById(code);
       	if (mean) 
                mean.className=(mean.className=='hidden')?'unhidden':'hidden';

    }
}

function unhideSelStackingPlots(selID,divID,length) {

    hideAll("HelicalParamsPlotsTime");
    hideAll("StatsTable");
    unselectAll("Proton_Pairs_Params");

    if (selID =="Nuc-Nuc_HBTimeSel" && bpsSelected == "") {
        //alert("You must select a Base Pair (|) first.\n");
			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Base Pair (|) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});
	
    }
    else if (selID =="Nuc-Nuc_StackingTimeSel" && tetSelected == "") {
       // alert("You must select a Nucleotide Tetramer ( X ) first.\n");

			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide Tetramer ( X ) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});
    }
    else {
	if(bpsSelected){

		// Unhide R Histogram
        	var item = document.getElementById(divID);
	        if (item) {
        	        item.className=(item.className=='hidden')?'unhidden':'hidden';
			helParameter = divID;
			id = item.id;
        	}

		// Button Selection (Highlight)
	        var sel = document.getElementById(selID);
        	if (sel) 
	               sel.className=(sel.className=='selected')?'unselected':'selected';

		// Unhide Mean/Stdev Table
		var code = "hb."+bpsSelected;
        	var mean = document.getElementById(code);
	       	if (mean) 
        	        mean.className=(mean.className=='hidden')?'unhidden':'hidden';
	}
	else if(tetSelected){

		// Button Selection (Highlight)
	        var sel = document.getElementById(selID);
        	if (sel) 
	               sel.className=(sel.className=='selected')?'unselected':'selected';

		var code1 = "Nuc1-Nuc2_StackingTimeSelPlot";
		var code2 = "Nuc3-Nuc4_StackingTimeSelPlot";
		var code3 = "Nuc1-Nuc3_StackingTimeSelPlot";
		var code4 = "Nuc2-Nuc4_StackingTimeSelPlot";

		// Unhide R Histograms

		// Stacking 1 X-X, 1st strand
        	var item = document.getElementById(code1);
	        if (item) 
        	        item.className=(item.className=='hidden')?'unhidden':'hidden';

		// Stacking 2 X-X, 2nd strand
        	var item = document.getElementById(code2);
	        if (item) 
        	        item.className=(item.className=='hidden')?'unhidden':'hidden';

		// Stacking 3 X    
		//             \ 
		//              X
        	var item = document.getElementById(code3);
	        if (item) 
        	        item.className=(item.className=='hidden')?'unhidden':'hidden';

		// Stacking 4   X
		//             /
		//            X 
        	var item = document.getElementById(code4);
	        if (item) 
        	        item.className=(item.className=='hidden')?'unhidden':'hidden';

		// Unhide Mean/Stdev Table
		var arr = tetSelected.split("-");
		var num1 = arr[0];
		var num2 = num1*1 + 1;
		var num3 = length*2 - num1*1;
		var num4 = num3*1 + 1;

		var tcode = "nmrJ."+num1+"-"+num2;
        	var mean = document.getElementById(tcode);
	       	if (mean) 
        	        mean.className=(mean.className=='hidden')?'unhidden':'hidden';

		var tcode2 = "nmrJ."+num3+"-"+num4;
        	var mean = document.getElementById(tcode2);
	       	if (mean) 
        	        mean.className=(mean.className=='hidden')?'unhidden':'hidden';

		var tcode3 = "nmrJ."+num1+"-"+num3;
        	var mean = document.getElementById(tcode3);
	       	if (mean) 
        	        mean.className=(mean.className=='hidden')?'unhidden':'hidden';

		var tcode4 = "nmrJ."+num2+"-"+num4;
        	var mean = document.getElementById(tcode4);
	       	if (mean) 
        	        mean.className=(mean.className=='hidden')?'unhidden':'hidden';

	}
    }
}

function unhideSelNmrPlots(selID,divID) {

    hideAll("HelicalParamsPlotsTime");
    hideAll("StatsTable");
    unselectAll("Proton_Pairs_Params");
    helParameter = '';

    var matchesStep = selID.match(/Step/);

    if (nucSelected == "" && matchesStep == undefined) {
        //alert("You must select a Nucleotide first.\n");
			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});

    }
    else if (stepSelected == "" && matchesStep != undefined){
       // alert("You must select a Nucleotide Pair Step (-) first.\n");
			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide Pair Step (-) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});

    }
    else {
	var nucOneLetter = '';
        var item = document.getElementById(divID);
	var num;
	var code;
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
		helParameter = divID;
		id = item.id;
		// J-coupling Mean & Stdev
		var matches = id.match(/[\w-]+TimeSelPlot/g);
		if (matches != undefined) 
			var m = matches.toString().replace("TimeSelPlot","");

		// If coming from Base Step (17-C:18-G)
		if(matchesStep != undefined){
			var bpLetter = stepSelected.match(/:/);
			if (bpLetter != undefined){
				var arrNOL = stepSelected.split(":");
				var arrNOL1 = arrNOL[0].split("-");
				var arrNOL2 = arrNOL[1].split("-");
				num = arrNOL1[0];
				num2 = arrNOL2[0];
				nucOneLetter = arrNOL2[1];
	
				code = "nmrJ."+m+"-"+num;
				code = code.replace(/-Step/,"");
				code = code.replace(/Nuc/,num);
				code = code.replace(/Nuc/,num2);
	
			}
		}
		else{
			var arr = nucSelected.split("-");
			num = arr[0];
			nucOneLetter = arr[1];
			code = "nmrJ."+m+"-"+num;
			code = code.replace(/Nuc/g,num);
		}


        }
        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }

	Jcoupling = m;
	//var code = "nmrJ."+m+"-"+num;
	//code = code.replace(/Nuc/g,num);
	
	if(Jcoupling != '' && nucOneLetter != ''){
		var matchesH6 = Jcoupling.match(/H6/);

		// Changing Ribose Image
		hideAll("Rib_images");

		if( matchesStep == undefined && (nucOneLetter == 'G' || nucOneLetter == 'A') && matchesH6 != undefined ){
			var image = nucOneLetter+"_"+Jcoupling+"SelRib";
			image = image.toString().replace("H6","H8");
			code = code.toString().replace("H6","H8");
			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';
		}
		else if(matchesStep == undefined && matchesH6 != undefined){
			var image = nucOneLetter+"_"+Jcoupling+"SelRib";
			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';
		}
		else if(matchesStep != undefined && (nucOneLetter == 'G' || nucOneLetter == 'A') && matchesH6 != undefined){
			var image = Jcoupling+"SelRib";
			image = image.toString().replace("H6","H8");
			code = code.toString().replace("H6","H8");
			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';
		}
		else{
			var image = Jcoupling+"SelRib";

			var item = document.getElementById(image);
			if (item)
				item.className='unhidden';
		}
	}

        var mean = document.getElementById(code);
       	if (mean) 
                mean.className=(mean.className=='hidden')?'unhidden':'hidden';

    }
}

function unhideSelNmrALL(selID,divID) {

	hideAll("HelicalParamsPlots");
	hideAll("StatsTable");
	unselectAll("Proton_Pairs_Avg");

        var item = document.getElementById(divID);
        if (item) 
                item.className=(item.className=='hidden')?'unhidden':'hidden';

        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }
	Jcoupling = '';

        // Changing Ribose Image
        hideAll("Rib_Avg_images");

        var item = document.getElementById("Rib_Avg");
        if (item)
                item.className=(item.className=='hidden')?'unhidden':'hidden';
}

function unhideSelNmrAVGPlots(selID,divID) {

        hideAll("HelicalParamsPlots");
	hideAll("StatsTable");
	unselectAll("Proton_Pairs_Avg");

        var item = document.getElementById(divID);
        if (item)
                item.className=(item.className=='hidden')?'unhidden':'hidden';

        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }
        Jcoupling = '';

        // Changing Ribose Image
        hideAll("Rib_Avg_images");

        var item = document.getElementById(selID+"Rib");
        if (item)
                item.className=(item.className=='hidden')?'unhidden':'hidden';

}

function unhideNmrIntensities(selID,divID) {

    hideAll("StatsTable");
    hideAll("HelicalParamsPlotsTime");
    hideAll("HelicalParamsPlots");
    hideAll("Time_Params");
    hideAll("Rib_Avg_images");
    hideAll("Avg_Params");
    hideAll("Rib_images");
    unselectAll("Proton_Pairs_Avg");
    unselectAll("Proton_Pairs_Params");
    unselectAllNucs();

    var item = document.getElementById("Rib_Avg");
    if (item)
        item.className="unhidden";

    var sel = document.getElementById("TIMESel");
    if (sel)
           sel.className='unselected';

    var sel = document.getElementById("AVGSel");
    if (sel)
           sel.className='unselected';

        var item = document.getElementById(divID);
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
        }
        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }

}

function unhideNmrAvgPlots(selID,divID) {

    hideAll("StatsTable");
    hideAll("HelicalParamsPlotsTime");
    hideAll("HelicalParamsPlots");
    hideAll("Time_Params");
    hideAll("Rib_Avg_images");
    hideDiv("IntensitiesPlot");
    unselectAll("Proton_Pairs_Avg");
    unselectAll("Proton_Pairs_Params");
    unselectAllNucs();

    var item = document.getElementById("Rib_Avg");
    if (item)
	item.className="unhidden";
	//item.className=(item.className=='hidden')?'unhidden':'hidden';

    var sel = document.getElementById("INTSel");
    if (sel)
           sel.className='unselected';

    var sel = document.getElementById("TIMESel");
    if (sel)
           sel.className='unselected';

        var item = document.getElementById(divID);
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
        }
        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }
}

function unhideNmrTimePlots(selID,divID) {

    hideAll("StatsTable");
    hideAll("HelicalParamsPlotsTime");
    hideAll("HelicalParamsPlots");
    hideAll("Avg_Params");
    hideAll("Rib_images");
    hideDiv("IntensitiesPlot");
    unselectAll("Proton_Pairs_Avg");
    unselectAll("Proton_Pairs_Params");
    unselectAllNucs();
    showAllHBs();

    var item = document.getElementById("Rib");
    if (item)
	//item.className=(item.className=='hidden')?'unhidden':'hidden';
	item.className="unhidden";

    var sel = document.getElementById("INTSel");
    if (sel)
           sel.className='unselected';

    var sel = document.getElementById("AVGSel");
    if (sel)
           sel.className='unselected';

        var item = document.getElementById(divID);
        if (item) {
                item.className=(item.className=='hidden')?'unhidden':'hidden';
        }
        var sel = document.getElementById(selID);
        if (sel) {
               sel.className=(sel.className=='selected')?'unselected':'selected';
        }
}

function showAllHBs(){

	var d = document.getElementById("AT_N1H3");
	if(d)
		d.className="curvesText";
	var d = document.getElementById("AT_H61O4");
	if(d)
		d.className="curvesText";
	var d = document.getElementById("CG_O6H41");
	if(d)
		d.className="curvesText";
	var d = document.getElementById("CG_H1N3");
	if(d)
		d.className="curvesText";
	var d = document.getElementById("CG_H21O2");
	if(d)
		d.className="curvesText";
}


function selectDist(atPair){

	unselectAll('AtomPairs');
	selectDiv('dist'+atPair+'Sel');
	hideAll('AtomPairsPlots');
	hideAll('StatsTable');
	unhide(atPair+'Plot');
	unhide('table.'+atPair);

}

// Stacking-Specific Nucleotide Selection
function selectNucleotideStacking(nucID,path,length) {

    hideStatsStacking();
    hideAll("Time_HBs");

    var arr = nucID.split("-");
    var num1 = arr[0];
    var num2 = num1*1 + 1;
    var num3 = length*2 - num1*1;
    var num4 = num3*1 + 1;

    var code1 = num1+"-"+num2+"_Stacking";
    var code2 = num3+"-"+num4+"_Stacking";
    var code3 = num1+"-"+num3+"_Stacking";
    var code4 = num2+"-"+num4+"_Stacking";

    // Modifying HTML to include new plots

    // R Plot
    var txt1 = "<img border='1' width='900' src='"+urlData +path+"/"+num1+"/"+code1+".dat.png'>";
    var txt2 = "<img border='1' width='900' src='"+urlData +path+"/"+num3+"/"+code2+".dat.png'>";
    var txt3 = "<img border='1' width='900' src='"+urlData +path+"/"+num1+"/"+code3+".dat.png'>";
    var txt4 = "<img border='1' width='900' src='"+urlData +path+"/"+num2+"/"+code4+".dat.png'>";

    // Download Buttons
    txt1 += '<table align="" border="0"><tr><td>';
    //txt1 += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+num1+'/'+code1+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
    txt1 += '</td><td>';
    txt1 += '<a href="'+urlData+'getFile.php?fileloc='+path+'/'+num1+'/'+code1+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
    txt1 += '</td></tr></table>';

    txt2 += '<table align="" border="0"><tr><td>';
    //txt2 += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+num3+'/'+code2+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
    txt2 += '</td><td>';
    txt2 += '<a href="'+urlData+'getFile.php?fileloc='+path+'/'+num3+'/'+code2+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
    txt2 += '</td></tr></table>';

    txt3 += '<table align="" border="0"><tr><td>';
    //txt3 += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+num1+'/'+code3+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
    txt3 += '</td><td>';
    txt3 += '<a href="'+urlData+'getFile.php?fileloc='+path+'/'+num1+'/'+code3+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
    txt3 += '</td></tr></table>';

    txt4 += '<table align="" border="0"><tr><td>';
    //txt4 += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+num2+'/'+code4+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
    txt4 += '</td><td>';
    txt4 += '<a href="'+urlData+'getFile.php?fileloc='+path+'/'+num2+'/'+code4+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
    txt4 += '</td></tr></table>';

    h = document.getElementById('Nuc1-Nuc2_StackingTimeSelPlot');
    h.innerHTML = txt1;

    h = document.getElementById('Nuc3-Nuc4_StackingTimeSelPlot');
    h.innerHTML = txt2;

    h = document.getElementById('Nuc1-Nuc3_StackingTimeSelPlot');
    h.innerHTML = txt3;

    h = document.getElementById('Nuc2-Nuc4_StackingTimeSelPlot');
    h.innerHTML = txt4;

    // Changing Mean/Stdev Table if we already had a tetramer selected
    var time = document.getElementById('Nuc-Nuc_StackingTimeSel');
    if(tetSelected && time.className=='selected'){
	var tcode = "nmrJ."+num1+"-"+num2;
	var mean = document.getElementById(tcode);
	if (mean) 
		mean.className=(mean.className=='hidden')?'unhidden':'hidden';

	var tcode = "nmrJ."+num3+"-"+num4;
	var mean = document.getElementById(tcode);
	if (mean) 
		mean.className=(mean.className=='hidden')?'unhidden':'hidden';

	var tcode = "nmrJ."+num1+"-"+num3;
	var mean = document.getElementById(tcode);
	if (mean) 
		mean.className=(mean.className=='hidden')?'unhidden':'hidden';

	var tcode = "nmrJ."+num2+"-"+num4;
	var mean = document.getElementById(tcode);
	if (mean) 
		mean.className=(mean.className=='hidden')?'unhidden':'hidden';
    }
    else{
	unselectAll("Time_Params");
    }

    // Unselect All nucleotides in sequence graph
    var nuc = document.getElementById(nucID);
    if (nuc) 
        unselectAllNucs();

    // Select Specific Nucleotide Tetramer
    var tet = document.getElementById(nucID);
    if (tet)
        tet.className=(tet.className=='selected')?'unselected':'selected';

    // Saving Tetramer selected
    tetSelected = nucID;

}

// 
function hideStatsStacking () {

	var d = document.getElementById("Time_Stacking");
	if (d)
	{
		var fc = d.childNodes;
		for(var i = 0; i < fc.length; i++)
		{
			var h = fc[i];
			if(h) {
				var divID = h.id;
				if(divID) {
					var matches = divID.match(/^nmrJ/g);
					if (matches != undefined) {
						//alert("Matches: "+ divID);
						if(h.className=='unhidden')
							h.className='hidden';
					}
				}
			}
		}
	}
}

// Stacking-Specific Nucleotide Selection (HBs)
function selectNucleotideStackingHB(nucID,path) {

    hideAll("Time_Stacking");
    hideAll("StatsTable");
    hideStatsStacking();

    // Histogram R Plot
    var arr = nucID.split("-");
    var num1 = arr[0];
    var code1 = nucID+"_HB";

    var Rcode = "Nuc-Nuc_HBTimeSelPlot";
    var plot = document.getElementById(Rcode);
    if (plot) {
	var txt = "<img border='1' width='900' src='"+urlData +path+"/"+num1+"/"+code1+".dat.png'>";

	// Download Buttons
	txt += '<table align="" border="0"><tr><td>';
	//txt += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+num1+'/'+code1+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
	txt += '</td><td>';
	txt += '<a href="'+urlData+'getFile.php?fileloc='+path+'/'+num1+'/'+code1+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
	txt += '</td></tr></table>';

	plot.innerHTML = txt;
    }

    // Changing Mean/Stdev Table if we already had a tetramer selected
    var time = document.getElementById('Nuc-Nuc_HBTimeSel');
    if(bpsSelected && time.className == 'selected'){

	// Table Mean/Stdev
	var code = "hb."+nucID;
	var mean = document.getElementById(code);
	if (mean) 
		mean.className=(mean.className=='hidden')?'unhidden':'hidden';
    }
    else {
	unselectAll("Time_Params");
    }

    // Unselect All nucleotides in sequence graph
    unselectAllNucs();

    // Hide Stacking Tables
    hideStatsStacking();

    // Saving Nuc Pair selected
    bpsSelected = nucID;
}

function unhideSelMontecarloPlots(selID,divID,length) {

    hideAll("HelicalParamsPlotsTime");
    hideAll("StatsTable");
    unselectAll("Proton_Pairs_Params");

    if (tetSelected == "") {
        //alert("You must select a Nucleotide Tetramer ( X ) first.\n");
			App.alert({ 
				container: $('#alert_container').val(), // alerts parent container 
				place: 'append', // append or prepent in container 
				type: 'warning', // alert's type 
				message: 'You must select a Nucleotide Tetramer ( X ) first.', // alert's message
        close: true, // make alert closable 
				reset: true, // close all previouse alerts first 
				focus: true, // auto scroll to the alert after shown 
				closeInSeconds: 10, // auto close after defined seconds
        icon: 'exclamation-triangle' // put icon class before the message 
			});

    }
    else {

	// Button Selection (Highlight)
        var sel = document.getElementById(selID);
       	if (sel) 
               sel.className=(sel.className=='selected')?'unselected':'selected';

	// Unhide R Histograms
       	var item = document.getElementById(divID);
        if (item) 
       	        item.className=(item.className=='hidden')?'unhidden':'hidden';

	// Unhide Mean/Stdev Table
	var arr = tetSelected.split("-");
	var num1 = arr[0];
	var num2 = num1*1 + 1;
	var num3 = length*2 - num1*1;
	var num4 = num3*1 + 1;
	var tetArr = arr[1].split("");
	var letter1 = tetArr[0];
	var letter4 = tetArr[3];

	var helicalParm = divID.replace("TimeSelPlot","");

	var tcode = "stats"+num1+"-"+letter1+"-"+letter4+"."+helicalParm;
       	var mean = document.getElementById(tcode);
       	if (mean) 
       	        mean.className=(mean.className=='hidden')?'unhidden':'hidden';

	helParameter = helicalParm;
    }
}

// Montecarlo-Specific Nucleotide Selection
function selectNucleotideMontecarlo(nucID,path) {

    hideAll("Time_Stacking");
    hideAll("StatsTable");
    hideStatsStacking();

    // Histogram R Plot
    var arr = nucID.split("-");
    var num1 = arr[0];
    var code1 = nucID+"_HB";

    var tetArr = arr[1].split("");
    var letter1 = tetArr[0];
    var letter4 = tetArr[3];

    var code = num1+"-"+letter1+"-"+letter4;

    var d = document.getElementById("HelicalParamsPlotsTime");
    if (d) {
	var fc = d.childNodes;
	for(var i = 0; i < fc.length; i++) {
		var plot = fc[i];
		if(plot) {
			if(plot.tagName == 'DIV'){
				var helicalParm = plot.id;
				helicalParm = helicalParm.replace("TimeSelPlot","");

				var txt = "<img border='1' width='900' src='"+urlData +path+"/"+code+"."+helicalParm+".dat.png'>";

				// Download Buttons
				txt += '<table align="" border="0"><tr><td>';
				//txt += '<p align="right" class="curvesDatText" onClick="window.open(\''+path+'/'+code+'.'+helicalParm+'.dat.png\',\'\', \'_blank,resize=1,width=800,height=400\');">Open in New Window</p><br/>';
				txt += '</td><td>';
				txt += '<a href="'+urlData+'getFile.php?fileloc='+path+'/'+code+'.'+helicalParm+'.dat&type=curves"> <p align="right" class="btn blue" style="">Download Raw Data</p></a>';
				txt += '</td></tr></table>';

				plot.innerHTML = txt;
			}
		}
	}
    }

    // Changing Mean/Stdev Table if we already had a tetramer selected
    var time = document.getElementById('TIMESel');
    if(tetSelected && time.className == 'selected'){

    // Table Mean/Stdev
    var codeStats = "stats"+code+"."+helParameter;
    var mean = document.getElementById(codeStats);
    if (mean) 
	mean.className=(mean.className=='hidden')?'unhidden':'hidden';
    }
    else {
	unselectAll("Time_Params");
    }

    // Unselect All nucleotides in sequence graph
    unselectAllNucs();

    // Selecting Tetramer
    var sel = document.getElementById(nucID);
    if (sel) 
	sel.className=(sel.className=='selected')?'unselected':'selected';

    // Hide Stacking Tables
    hideStatsStacking();

    // Saving Nuc Pair selected
    tetSelected = nucID;
}

function selectTetramerMontecarlo(nuc,length,min) {

    if(nuc) {
        var arr = nuc.split("-");
        var num1 = arr[0];
        var num2 = num1*1 + 1;
        var tetArr = arr[1].split("");
        var letter1 = tetArr[0];
        var letter2 = tetArr[1];
        var letter3 = tetArr[2];
        var letter4 = tetArr[3];

        var code1 = num1+"-"+letter1;
        var code2 = num2+"-"+letter2;
	var minInt = parseInt(min,10);
        var num3 = minInt + length*2 - (num1 - minInt) -2;
        var num4 = num3*1 + 1;
        var code3 = num3+"-"+letter3;
        var code4 = num4+"-"+letter4;

        var codeBPS1 = num1+"-"+letter1+letter4;
        var codeBPS2 = num2+"-"+letter2+letter3;

        var codeBPS1_bis = num1+"-"+letter1+":"+num4+"-"+letter4;
        var codeBPS2_bis = num2+"-"+letter2+":"+num3+"-"+letter3;
        var codeBP1 = num1+"-";
        var codeBP2 = num4+"-";

        selectId(code1);
        selectId(code2);
        selectId(code3);
        selectId(code4);
        selectId(codeBP1);
        selectId(codeBP2);
        selectId(codeBPS1);
        selectId(codeBPS2);
        selectId(codeBPS1_bis);
        selectId(codeBPS2_bis);
    }
}

function openIMG(path){
  
	var content = '<img src="' + path + '">';

  $('#modalImages .modal-body').html(content);
  $('#modalImages').modal({ show: 'true' });
}



