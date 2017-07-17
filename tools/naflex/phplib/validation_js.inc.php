<?php
/*
 * validation_js.inc.php
 */
?>
<script  type="text/javascript">

var error=false;
var numPairs = 1;
var numPairsRemoved = 0;

function setInnerForm() {
	idOperacio = getValue('project','idOperacio');
	inForm=document.getElementById("innerForm");
	if(idOperacio == "XX"){
		inForm.innerHTML='';
	}
	else{
		inForm.innerHTML=opsHTML[idOperacio];
	}
}	

function setHelpForm() {
	idOperacio = getValue('project','idOperacio');
	var helpForm=document.getElementById("helpForm");
	if(idOperacio == "XX"){
		helpForm.innerHTML='';
	}
	else{
		helpForm.innerHTML=helpHTML[idOperacio];
	}
}	

var tooltip=function(){
	var id = 'tt';
	var top = 3;
	var left = 3;
	var maxw = 800;
	var speed = 10;
	var timer = 20;
	var endalpha = 95;
	var alpha = 0;
	var tt,t,c,b,h;
	var ie = document.all ? true : false;
	return{
		show:function(v,w){
 			if(tt == null){
				tt = document.createElement('div');
				tt.setAttribute('id',id);
				t = document.createElement('div');
				t.setAttribute('id',id + 'top');
				c = document.createElement('div');
				c.setAttribute('id',id + 'cont');
				b = document.createElement('div');
				b.setAttribute('id',id + 'bot');
				tt.appendChild(t);
				tt.appendChild(c);
				tt.appendChild(b);
				document.body.appendChild(tt);
				tt.style.opacity = 0;
				tt.style.filter = 'alpha(opacity=0)';
				document.onmousemove = this.pos;
			}
			tt.style.display = 'block';
			if(v.match(/Nucleic Structure/g) || v.match(/ DNA/g) ){
				c.innerHTML = v + "<br><br><small>Click for more information</small>";
			}
			else{
				c.innerHTML = v + "<br><br><small>Click for more information (bypass to MDWeb server help)</small>";
			}
			tt.style.width = w ? w + 'px' : 'auto';
			if(!w && ie){
				t.style.display = 'none';
				b.style.display = 'none';
				tt.style.width = tt.offsetWidth;
				t.style.display = 'block';
				b.style.display = 'block';
			}
			if(tt.offsetWidth > maxw){tt.style.width = maxw + 'px'}
			h = parseInt(tt.offsetHeight) + top;
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){tooltip.fade(1)},timer);
		},
		pos:function(e){
			var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
			var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
			tt.style.top = (u - h) + 'px';
			tt.style.left = (l + left) + 'px';
		},
		fade:function(d){
			var a = alpha;
			if((a != endalpha && d == 1) || (a != 0 && d == -1)){
				var i = speed;
				if(endalpha - a < speed && d == 1){
					i = endalpha - a;
				}else if(alpha < speed && d == -1){
					i = a;
				}
				alpha = a + (i * d);
				tt.style.opacity = alpha * .01;
				tt.style.filter = 'alpha(opacity=' + alpha + ')';
			}else{
				clearInterval(tt.timer);
				if(d == -1){tt.style.display = 'none'}
			}
		},
		hide:function(){
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){tooltip.fade(-1)},timer);
		}
	};
}();

function validate(box) {    
    if (box.checked) {
        document.project.myfilechooser.disabled=false;
        error=true;
    }
    else {
        error=false;
        document.project.myfilechooser.disabled=true;
        document.getElementById("chooser").innerHTML = document.getElementById("chooser").innerHTML;
    }
}

function cg_analysis(box) {    
	if(box.id == "local_checkbox"){
		document.project.global_checkbox.checked = false;
	        document.project.segment1.disabled=false;
        	document.project.segment2.disabled=false;
        	document.project.offset.disabled=false;
	}
	else{
		document.project.local_checkbox.checked = false;
        	document.project.segment1.disabled=true;
	        document.project.segment2.disabled=true;
	        document.project.offset.disabled=true;
	}
}

function codename(c) {
	if(c == "User Defined Mask"){
		document.project.mask.disabled=false;
	}
	else{
		document.project.mask.disabled=true;
	}
}

function distances(c) {
	var v = document.project.NA_ops.options[c].value;
        var div = document.getElementById("distParams");
        if(v == "Distances"){
        	if (div) {
                	div.className='unhidden';
        	}
        }
        else{
        	if (div) {
	                div.className='hidden';
        	}
        }
}

function noeParams(c) {
        var v = document.project.NA_ops.options[c].value;
        var div = document.getElementById("NOEintensitiesParams");
        if(v == "Nmr_NOEs"){
                if (div) {
                        div.className='unhidden';
                }
        }
        else{
                if (div) {
                        div.className='hidden';
                }
        }
}

function codename2(c) {
	if(c == "No restraints"){
		document.project.restr.disabled=true;
	}
	else{
		document.project.restr.disabled=false;
	}
}

function WLC_charge(c) {
	if(c == "auto"){
		document.project.charge.disabled=true;
	}
	else{
		document.project.charge.disabled=false;
	}
}

function gmxWaterType(ff) {
        if ( (ff > 0  && ff < 8) || ff == 16){ // AMBERff, PARMBSC0
		document.project.gmxWater.options.length=0;
		document.project.gmxWater.options[0]=new Option("TIP3P: TIP 3-point", "1", true, false);
		document.project.gmxWater.options[1]=new Option("TIP4P: TIP 4-point", "3", false, false);
		document.project.gmxWater.options[2]=new Option("TIP4P-Ew: TIP 4-point optimized with Ewald", "4", false, false);
		document.project.gmxWater.options[3]=new Option("SPC: Simple Point Charge", "6", false, false);
		document.project.gmxWater.options[4]=new Option("SPC/E: Extended Simple Point Charge", "7", false, false);
        }
        else if(ff == 8 || ff == 9){ // CHARMMff27/36
		document.project.gmxWater.options.length=0;
                document.project.gmxWater.options[0]=new Option("TIP3P: TIP 3-point", "1", true, false);
                document.project.gmxWater.options[1]=new Option("TIPS3P: TIP 3-point with LJ on H's (note: twice as slow in GROMACS)", "2", false, false);
                document.project.gmxWater.options[2]=new Option("TIP4P: TIP 4-point", "3", false, false);
                document.project.gmxWater.options[3]=new Option("SPC: Simple Point Charge", "6", false, false);
                document.project.gmxWater.options[4]=new Option("SPC/E: Extended Simple Point Charge", "7", false, false);
        }
        else if(ff > 9 && ff <15){ // GROMOSff
		document.project.gmxWater.options.length=0;
		document.project.gmxWater.options[0]=new Option("SPC: Simple Point Charge", "6", true, false);
		document.project.gmxWater.options[1]=new Option("SPC/E: Extended Simple Point Charge", "7", false, false);
	}
        else if(ff == 15){ // OPLS-AAff
		document.project.gmxWater.options.length=0;
                document.project.gmxWater.options[0]=new Option("TIP4P: TIP 4-point", "3", true, false);
                document.project.gmxWater.options[1]=new Option("TIP3P: TIP 3-point", "1", false, false);
                document.project.gmxWater.options[2]=new Option("TIP5P: TIP 5-point", "5", false, false);
                document.project.gmxWater.options[3]=new Option("SPC: Simple Point Charge", "6", false, false);
                document.project.gmxWater.options[4]=new Option("SPC/E: Extended Simple Point Charge", "7", false, false);
        }
	else if(ff == 17 || ff == 18){ // ENCADff
		document.project.gmxWater.options.length=0;
		document.project.gmxWater.options[0]=new Option("F3C: Flexible three-centered water model", "8", true, false);
	}
	else if(ff == 19 || ff == 20){ // GROMACSff
		document.project.gmxWater.options.length=0;
                document.project.gmxWater.options[0]=new Option("SPC: Simple Point Charge", "6", true, false);
                document.project.gmxWater.options[1]=new Option("SPC/E: Extended Simple Point Charge", "7", false, false);
                document.project.gmxWater.options[2]=new Option("TIP3P: TIP 3-point", "1", false, false);
                document.project.gmxWater.options[3]=new Option("TIP4P: TIP 4-point", "3", false, false);
	}
}

function nma(c) {
	if(c == 1){ // Kovacs Algorithm
		document.project.cutoff.disabled=true;
		document.project.fcte.disabled=false;
	}
	else if(c == 2){ // Mixed Algorithm
		document.project.cutoff.disabled=true;
		document.project.fcte.disabled=true;
	}
	else{ // Linear Algorithm
		document.project.cutoff.disabled=false;
		document.project.fcte.disabled=false;
	}
}

function configCode(box) {
    if (box.checked) {
		document.project.timestep.disabled=true;
		document.project.temperature.disabled=true;
		document.project.time.disabled=true;
		document.project.steps.disabled=true;
	}
	else{
		document.project.timestep.disabled=false;
		document.project.temperature.disabled=false;
		document.project.time.disabled=false;
		document.project.steps.disabled=false;
	}
}

function isNumeric(text) {
   var ValidChars = ".0123456789";
   var IsNumber=true;
   var Char;
   var dots=0;

   if (text.length <= 0)
       IsNumber = false;
   if (text.charAt(0) == '-')
       text = text.substr(1);

   for (var i = 0; i < text.length && IsNumber == true; i++) {
      Char = text.charAt(i);
      if (ValidChars.indexOf(Char) == -1) {
         IsNumber = false;
      } else if (ValidChars.indexOf(Char) == 0) {
          dots++;
          if (dots > 1)
              IsNumber = false;
      }
   }
   return IsNumber;
}

function loadAjax(url){

        var req = false;
        var value;

	// Mozilla, Safari etc
	if (window.XMLHttpRequest) {
            req = new XMLHttpRequest();
	}

	// IE
	else if (window.ActiveXObject) {
            try {
                req = new ActiveXObject("Msxml2.XMLHTTP")
            }
            catch (e) {
                try {
                    req = new ActiveXObject("Microsoft.XMLHTTP")
		}
		catch (e){}
            }
	}
	else
            return false

	req.open('GET', url, false);
	req.send(null);
        value = req.responseText;
        if (isNumeric(value))
            return value;
}

function checkParams(error, arg1, arg2) {
    if (error) {
        alert("Error! Check parameters");
    }
    else {
        var f=document.project;
        var name;
        var value;
        var articleName;
        var min=0;
        var max;
        f.getElementsByTagName('input');

        var index = document.project.idOperacio.selectedIndex;
        var op = document.project.idOperacio.options[index].value;

        for(var i=0;i<f.length;i++){
            if (!f[i].getAttribute('type')){
                name=f[i].getAttribute('name');
                value=f[i].value;
                if (value && isNumeric(value)) {
                    articleName = name.substring(4,name.length-1);
                    min=loadAjax('checkParams.php?op='+op+'&articleName='+articleName+'&maxmin=0');
                    max=loadAjax('checkParams.php?op='+op+'&articleName='+articleName+'&maxmin=1');
                    if (min && (value < min)) {
                        alert ("Error: "+articleName+" < "+min);
                        error = true;
                    }
                    else if (max && (value > max)) {
                        alert ("Error: "+articleName+" > "+max);
                        error = true;
                    }
                }
            }
        }

        var checkRun = document.project.conf;
        if(checkRun && checkRun.checked){
                if ( !confirm("Warning: Only return configuration files (without running simulation) is checked. This operation will not run the MD simulation, just will prepare all the needed files to run it locally. Are you sure?")) {
                        error = true;
                }
        }

        if (!error) {
            submitAs(arg1, arg2);
        }
    }
}

function selectRes(resid, totalRes) {
    for (var i=1;i<=totalRes;i++) {
        current=document.getElementById("res_"+i);
        current.style.backgroundColor=null;
        current.style.color=null;
        if (i==resid) {
            current.style.backgroundColor="red";
            current.style.color="white";
            document.getElementById("resid").value = i;
        }
    }
}

function addAtomPair() {

    if((numPairs-numPairsRemoved) >= 10){
	alert("Sorry, only 10 atom pairs are allowed.");
	return;
    }
    var container = document.getElementById("distParamsTable");
    var d = document.createElement("tr");
    numPairs++;
    d.setAttribute("id","atomPair"+numPairs);
    d.appendChild(td1 = document.createElement("td"));
    d.appendChild(td2 = document.createElement("td"));
    d.appendChild(td3 = document.createElement("td"));
    d.appendChild(td4 = document.createElement("td"));
    td1.innerHTML = "<b><i>Atom Pair "+numPairs+": </i></b>";
    td2.innerHTML = "Atom 1: <input name='prm[atomDistances][atom1_"+numPairs+"]' size='20' />";
    td3.innerHTML = "Atom 2: <input name='prm[atomDistances][atom2_"+numPairs+"]' size='20' />";
    td4.innerHTML = "<a href='javascript:removeAtomPair("+numPairs+")'><text style='font-size:x-small;'><u>Remove</u></text></a>";
    container.appendChild(d);

/*
        try{    // Standard compliant, doesn't work in IE.
            additp = document.getElementById("additp");
            container.removeChild(additp);
            container.removeChild(space);
            container.appendChild(additp);
            container.appendChild(space);
        }
        catch(e){       // IE case.
                container.removeChild(additp);
                container.removeChild(space);
                tr4 = document.createElement("tr");
                tr4.setAttribute("id","additp");
                tr4.appendChild(td = document.createElement("td"));
                td.innerHTML = "<a href='javascript:addFileInput(numPairs++)'><u><p style='font-size:x-small;'><u>Add another ITP file</u></p></u></a>";
                container.appendChild(tr4);

                space=document.createElement("div");
                space.setAttribute("id", "space");
                space.innerHTML="<br/><br/>";
                container.appendChild(space);
        }
*/
}

function removeAtomPair(num) {
    var child = document.getElementById("atomPair"+num);
    var father = document.getElementById("distParamsTable");
    father.removeChild(child);
    numPairsRemoved++;
}


</script>
