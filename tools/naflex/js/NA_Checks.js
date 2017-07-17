//
// JavaScript Functions to check/uncheck NMR Observables
//

function checkAll(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
            var fc = d.getElementsByTagName("input");
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
				h.checked = (h.checked)?false:true;
                        }
                }
    }
}

function uncheckAll(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
            var fc = d.getElementsByTagName("input");
                for(var i = 0; i < fc.length; i++)
                {
                        var h = fc[i];
                        if(h) {
				h.checked = false;
                        }
                }
    }
}

function checkSugar(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

	for(var i = 0; i < fc.length; i++){

		var h = fc[i];

		var name = h.id;
		var matches = name.match(/^checkSugar_\w+/g);

		if (matches != undefined) {

                       	if(h) {
				h.checked = (h.checked)?false:true;
       	                }
                }
	}
    }
}

function checkBase(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

        for(var i = 0; i < fc.length; i++){

                var h = fc[i];

                var name = h.id;
                var matches = name.match(/^checkBase_\w+/g);

                if (matches != undefined) {

                        if(h) {
                                h.checked = (h.checked)?false:true;
                        }
                }
        }
    }
}

function checkStep(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

        for(var i = 0; i < fc.length; i++){

                var h = fc[i];

                var name = h.id;
                var matches = name.match(/^checkStep_\w+/g);

                if (matches != undefined) {

                        if(h) {
                                h.checked = (h.checked)?false:true;
                        }
                }
        }
    }
}

function checkProton(divID,prot) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

        for(var i = 0; i < fc.length; i++){

                var h = fc[i];

                var name = h.id;

		var regex = new RegExp( "_" + prot + "-\\w+", "g");
//alert("Regex: "+ regex);
                var matches = name.match(regex);

                if (matches != undefined) {

                        if(h) {
                                h.checked = (h.checked)?false:true;
                        }
                }
        }
    }
}

function checkPyrimidines(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

        for(var i = 0; i < fc.length; i++){

                var h = fc[i];

                var name = h.id;
                var matches = name.match(/^checkPyr_\w+/g);

                if (matches != undefined) {

			if(h) {
				h.checked = (h.checked)?false:true;
			}
                }       
        }
    }
}

function checkPurines(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

        for(var i = 0; i < fc.length; i++){

                var h = fc[i];

                var name = h.id;
                var matches = name.match(/^checkPur_\w+/g);

                if (matches != undefined) {

			if(h) {
				h.checked = (h.checked)?false:true;
			}
                }       
        }
    }
}

function checkStepPurines(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

        for(var i = 0; i < fc.length; i++){

                var h = fc[i];

                var name = h.id;
                var matches = name.match(/^checkStepPur_\w+/g);

                if (matches != undefined) {

			if(h) {
				h.checked = (h.checked)?false:true;
			}
                }   
        }
    }
}

function checkStepPyrimidines(divID) {
    var d = document.getElementById(divID);
    if (d)
    {
        var fc = d.getElementsByTagName("input");

        for(var i = 0; i < fc.length; i++){

                var h = fc[i];

                var name = h.id;
                var matches = name.match(/^checkStepPyr_\w+/g);

                if (matches != undefined) {

			if(h) {
				h.checked = (h.checked)?false:true;
			}
                }   
        }
    }
}

function intPlotExt() {
    var d = document.getElementById("IntensitiesPlot");
    if (d)
    {
        var src = d.innerHTML;
        var ch = document.getElementById("intProtonPairs").checked;
        if(ch){
                src = src.replace(/intMat.out/g,"intMat.ext.out");
                d.innerHTML = src.replace(/intMat.png/g,"intMat.ext.png");

                var input = d.getElementsByTagName('input');
                for (i = 0; i < input.length; i++)
                {
                        if (input[i].id == 'intProtonPairs')
                        {
                                input[i].checked = true;
                        }
                }
        }
        else{
                src = src.replace(/intMat.ext.out/g,"intMat.out");
                d.innerHTML = src.replace(/intMat.ext.png/g,"intMat.png");

                var input = d.getElementsByTagName('input');
                for (i = 0; i < input.length; i++)
                {
                        if (input[i].id == 'intProtonPairs')
                        {
                                input[i].checked = false;
                        }
                }
        }
    }
}

