var num_itp=1;
var groinput;
var space;
var tr;

//CF added the "if (part)" because some elements don't exist (yet?)
function disable() {
    value = document.getElementById('projType').value;
    if (value == "sim") {
        part = document.getElementById('pdbcode');
        if (part) part.disabled=false;
        part = document.getElementById('structure');
        if (part) part.disabled=false;
        part = document.getElementById('ffForm');
        if (part) part.disabled=true;
        part = document.getElementById('trajForm');
        if (part) part.disabled=true;
        part = document.getElementById('topForm');
        if (part) part.disabled=true;
        part = document.getElementById('coordinates');
        if (part) part.disabled=true;
        part = document.getElementById('top');
        if (part) part.disabled=true;
        part = document.getElementById('gro');
        if (part) part.disabled=true;
        part = document.getElementById('itp0');
        if (part) part.disabled=true;
	part = document.getElementById('projectDump');
	if (part) part.disabled=true;
    }
    else if (value == "anal") {
        part = document.getElementById('pdbcode');
        if (part)  part.disabled=true;
        part = document.getElementById('structure');
        if (part)  part.disabled=true;
        part = document.getElementById('ffForm');
        if (part) part.disabled=false;
        part = document.getElementById('trajForm');
        if (part) part.disabled=false;
        part = document.getElementById('topForm');
        if (part) part.disabled=false;
        part = document.getElementById('coordinates');
        if (part) part.disabled=false;
        part = document.getElementById('top');
        if (part) part.disabled=false;
        part = document.getElementById('gro');
        if (part) part.disabled=false;
        part = document.getElementById('itp0');
        if (part) part.disabled=false;
	part = document.getElementById('projectDump');
	if (part) part.disabled=true;
    }
    else if (value == "upload") {
        part = document.getElementById('pdbcode');
        if (part)  part.disabled=true;
        part = document.getElementById('structure');
        if (part)  part.disabled=true;
        part = document.getElementById('ffForm');
        if (part) part.disabled=true;
        part = document.getElementById('trajForm');
        if (part) part.disabled=true;
        part = document.getElementById('topForm');
        if (part) part.disabled=true;
        part = document.getElementById('coordinates');
        if (part) part.disabled=true;
        part = document.getElementById('top');
        if (part) part.disabled=true;
        part = document.getElementById('gro');
        if (part) part.disabled=true;
        part = document.getElementById('itp0');
        if (part) part.disabled=true;
        part = document.getElementById('projectDump');
        if (part) part.disabled=false;
    }
}

function removeAllOptions(selectbox) {
    var i;
    for(i=selectbox.options.length-1;i>=0;i--) {
        selectbox.remove(i);
    }
}

function addOption(selectbox,value,text) {
    var optn = document.createElement('option');
    optn.text = text;
    optn.value = value;

    try{	 // Standard compliant, doesn't work in IE.
        selectbox.add(optn,null);

    } catch(e) {	// IE case.
        selectbox.add(optn);
    }
}

function selectTrajTop() {
    form = document.getElementById('thisform');
    removeAllOptions(form.trajForm);
    removeAllOptions(form.topForm);

    // Warning, IE doesn't accept writing innerHTML in tbody tags.
    // So we need to build the new structure with DOM to append it.

    // Building new Row.
    tr1 = document.createElement("tr");
    tr1.setAttribute("id","toptx");
    td1 = document.createElement("td");
    td2 = document.createElement("td");
    td1.innerHTML = "Topology File";
    td2.innerHTML = "<input type='file' id='top' name='topology' size='40'/>";
    tr1.appendChild(td1);
    tr1.appendChild(td2);
    
    if (document.getElementById('ffForm').value == 'namd'){

	// Getting tbody, removing innerHTML and adding new HTML (DOM).
	var taula = document.getElementById('variable');

	if ( taula.hasChildNodes() )
	{
	    while ( taula.childNodes.length >= 1 )
	    {
	        taula.removeChild( taula.firstChild );
	    } 
	}

	taula.appendChild(tr1);

        addOption(form.trajForm,'crd', 'CRD');
        addOption(form.trajForm,'dcd', 'DCD');
        addOption(form.trajForm,'binpos', 'BINPOS');
        addOption(form.trajForm,'netcdf', 'NetCDF');
        addOption(form.trajForm,'pcazip', 'PCAZip');
        addOption(form.topForm,'psf', 'NAMD PSF');
        addOption(form.topForm,'amber', 'PRMTOP');
    }
    else if (document.getElementById('ffForm').value == 'gromacs'){

	// Building new Row (grotx).
	tr2 = document.createElement("tr");
	tr2.setAttribute("id","grotx");
	td1 = document.createElement("td");
	td2 = document.createElement("td");
	td1.innerHTML = "GRO File";
	td2.innerHTML = "<input type='file' id='gro' name='gro' size='40'/>";
	p1 = document.createElement("p");
	p1.innerHTML = "<?php echo formError('nogro')?>";
	tr2.appendChild(td1);
	tr2.appendChild(td2);

        // Building new Row (itpx).
        tr3 = document.createElement("tr");
        tr3.setAttribute("id","itpx");
        td1 = document.createElement("td");
        td2 = document.createElement("td");
        td1.innerHTML = "ITP File";
        td2.innerHTML = "<input type='file' id='itp0' name='itp0' size='40'/>";
        tr3.appendChild(td1);
        tr3.appendChild(td2);

        // Getting tbody, removing innerHTML and adding new HTML (DOM).
        var taula = document.getElementById('variable');
        for(var i = taula.rows.length - 1; i >= 0; i--){
                taula.deleteRow(i);
        }
        taula.appendChild(tr1);
	taula.appendChild(p1);
        taula.appendChild(tr2);
        taula.appendChild(tr3);

        tr4 = document.createElement("tr");
        tr4.setAttribute("id","additp");
        tr4.appendChild(td = document.createElement("td"));
        td.innerHTML = "<a href='javascript:addFileInput(num_itp++)'><u><p style='font-size:x-small;'><u>Add another ITP file</u></p></u></a>";
        taula.appendChild(tr4);

	space=document.createElement("div");
	space.setAttribute("id", "space");
	space.innerHTML="<br/><br/>";
        taula.appendChild(space);

        addOption(form.trajForm,'xtc', 'XTC');
        addOption(form.topForm,'gromacs', 'GROMACS TOP');
    }
}


function addFileInput(num) {
    var container = document.getElementById("variable");
    var d = document.createElement("tr");
    d.setAttribute("id","itp"+num);
    d.appendChild(td1 = document.createElement("td"));
    d.appendChild(td2 = document.createElement("td"));
    d.appendChild(td3 = document.createElement("td"));
    td1.innerHTML = "ITP File";
    td2.innerHTML = "<input type='file' name='itpfile"+num+"' size='40' />";
    td3.innerHTML = "<a href='javascript:removeFileInput("+num+")'><text style='font-size:x-small;'><u>Remove</u></text></a>";
    container.appendChild(d);


	try{	// Standard compliant, doesn't work in IE.
	    additp = document.getElementById("additp");
	    container.removeChild(additp);
	    container.removeChild(space);
	    container.appendChild(additp);
	    container.appendChild(space);
	}
	catch(e){	// IE case.
		container.removeChild(additp);
		container.removeChild(space);
		tr4 = document.createElement("tr");
	        tr4.setAttribute("id","additp");
        	tr4.appendChild(td = document.createElement("td"));
	        td.innerHTML = "<a href='javascript:addFileInput(num_itp++)'><u><p style='font-size:x-small;'><u>Add another ITP file</u></p></u></a>";
        	container.appendChild(tr4);

	        space=document.createElement("div");
        	space.setAttribute("id", "space");
        	space.innerHTML="<br/><br/>";
	        container.appendChild(space);
	}

}

function removeFileInput(num) {
    var child = document.getElementById("itp"+num);
    var father = document.getElementById("variable");
    father.removeChild(child);
}
