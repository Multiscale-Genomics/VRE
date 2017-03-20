<!-- jQuery JSmol load -->
function loadJMol(){
  $("#insertJmol").html(Jmol.getAppletHtml("jmol",Info));
  //document.getElementById("insertJmol").innerHTML = Jmol.getAppletHtml("jmol",Info);
};

<!-- jmolCheckBox: Called by the checking.php and its used to change the view of the jmol  -->
function jmolCheckBox(text){
	//alert("jmolCheckBox( text: "+text+")");
	if (text == 'animation'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"frame all; display all;");
		}
		else{
			Jmol.script(jmol,anim);
		}
	}
        if (text == 'toggleselection'){
                if (document.getElementById(text).checked == true){
                        Jmol.script(jmol,"set display selected");
                }
                else{
                        Jmol.script(jmol,"set display off");
                }
        }
	if (text == 'togglehbonds'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"calculate HBONDS {*} {*}; hbonds on; color hbonds magenta;");
		}
		else{
			Jmol.script(jmol,"hbonds off");
		}
	}
	if (text == 'togglespin'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"spin on; ");
		}
		else{
			Jmol.script(jmol,"spin off");
		}
	}
	if (text == 'toggleasym'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"set unitcell on; ");
		}
		else{
			Jmol.script(jmol,"set unitcell off;");
		}
	}
	if (text == 'antialias'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"set antialiasDisplay on;");
		}
		else{
			Jmol.script(jmol,"set antialiasDisplay off;");
		}
	}
	if (text == 'backgroundWhite'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"background black;");
		}
		else{
			Jmol.script(jmol,"background white;");
		}
	}
	if (text == 'toggledisulfide'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"select {_S and connected({_S})} ; color yellow ; ssbonds on; set ssbonds backbone;");
		}
		else{
			Jmol.script(jmol,"ssbonds off;");
		}
	}
	if (text == 'ligands'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"select ligands; spacefill 1.0; color cpk;");
		}
		else{
			Jmol.script(jmol,"select ligands; spacefill 0.5;");
		}
	}
	if (text == 'waters'){
		if (document.getElementById(text).checked == true){
			var styleindex= document.getElementById('jmolStyle').selectedIndex;
			var style= document.getElementById('jmolStyle').options[styleindex].value;
			//alert(style);
			if(style == 'wireframe'){
				Jmol.script(jmol,"select water; spacefill off; cartoon off; backbone off; wireframe 0.01; color cpk;");
			}else{
				Jmol.script(jmol,"select water; spacefill 1.0; color cpk;");
			}
		}
		else{
			Jmol.script(jmol,"restrict not water;");
		}
	}
	if (text == 'ions'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"select ions or CLA or SOD; spacefill 1.0; color cpk;");
		}
		else{
			Jmol.script(jmol,"restrict not \(ions or CLA or SOD\);");
		}
	}
	if (text == 'dna'){
		if (document.getElementById(text).checked == true){
			Jmol.script(jmol,"select dna; spacefill 1.0; color cpk;");
		}
		else{
			Jmol.script(jmol,"select dna; spacefill 0;");
		}
	}
}

<!-- jmolDropdownMenu: Called by the checking.php and its used to change the view of the jmol  -->
function jmolDropdownMenu(text){
	//alert("jmolDropdownMenu( text: "+text+")");
	if (text == 'cartoon'){ Jmol.script(jmol,cartoon);}
	if (text == 'backbone'){ Jmol.script(jmol,backbone);}
	if (text == 'cpk'){ Jmol.script(jmol,cpk);}
	if (text == 'ballandstick'){ Jmol.script(jmol,ballandstick);}
	if (text == 'wireframe'){ Jmol.script(jmol,wireframe);}
	if (text == 'ligands'){ Jmol.script(jmol,ligands);}
	if (text == 'ligandsandpocket'){ Jmol.script(jmol,ligandsandpocket);}
	if (text == 'secondaryStructure'){ Jmol.script(jmol,secondaryStructure);}
	if (text == 'byChain'){ Jmol.script(jmol,byChain);}
	if (text == 'rainbow'){ Jmol.script(jmol,rainbow);}
	if (text == 'byElement'){ Jmol.script(jmol,byElement);}
	if (text == 'byAminoAcid'){ Jmol.script(jmol,byAminoAcid);}
	if (text == 'hidrophobicity'){ Jmol.script(jmol,hidrophobicity);}
	if (text == 'none'){ Jmol.script(jmol,'isosurface off;');}
	if (text == 'solventAccessible'){ Jmol.script(jmol,'isosurface sasurface 1.2');}
	if (text == 'solventExcluded'){ Jmol.script(jmol,'isosurface solvent 1.2');}
	if (text == 'cavities'){ Jmol.script(jmol,'isosurface cavity 1.2 10');}
}

<!-- jmolDropdownMenu: Called by the checking.php and its used to change the view of the jmol  -->
function resetJmolButton(){
	document.getElementById('jmolStyle').options[0].selected = true;
	document.getElementById('jmolColor').options[0].selected = true;
	document.getElementById('jmolSurface').options[0].selected = true;
	document.getElementById('waters').checked = false;
	document.getElementById('ligands').checked = false;
	document.getElementById('dna').checked = false;
}

<!-- executeJmolScriptInput: Called by the checking.php and its used to launch the jmol console commands  -->
function executeJmolScriptInput(){
	//alert("executeJmolScriptInput");
	var command ="";
	command = document.getElementById('jmolScriptInput').value;
	Jmol.script(jmol,command);
	document.getElementById('jmolScriptInput').value = "";
}

