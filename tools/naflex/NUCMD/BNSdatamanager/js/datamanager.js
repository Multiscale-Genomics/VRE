                function toggleVis(a) {
                    ob  = document.getElementById(a);
                    if (ob.style.visibility == 'hidden') {
                        ob.style.visibility = 'visible';
                        //ob.style.display = 'inline';
                        ob.style.display = 'table-row';
                    } else {
                        ob.style.visibility = 'hidden';
                        ob.style.display = 'none';
                    }
                }
                function toggleVisLink(a,link) {
                    ob  = document.getElementById(a);
                    //link= document.getElementById(linkId);
                    if (ob.style.visibility == 'hidden') {
                        ob.style.visibility = 'visible';
                        ob.style.display = 'table-row';
			link.innerHTML="(-)";
                    } else {
                        ob.style.visibility = 'hidden';
                        ob.style.display = 'none';
			link.innerHTML="(+)";
                    }
		}
                function ClipBoard() {
                    holdtext.innerText = copytext.innerText;
                    Copied = holdtext.createTextRange();
                    Copied.execCommand("Copy");
                }

                function validateUpload(form, max) {
                    input = document.getElementById('fn');
                    for (idx = 0; idx < input.files.length; ++idx) {
                        file = input.files[idx];
                        if (file && file.size > max) {
                            alert("Not enough space to upload " + file.name + " (" + file.size + "b). Free space is only " + max + "b.");
                            form.fn.value = null;
                            return FALSE;
                        }
                    }
                    form.op.value = 'uploadFile';
                    form.submit();
                }

		function addRow(tableID) {
		    var table = document.getElementById(tableID);
		    var rowCount = table.tBodies[0].rows.length;
	 	    if(rowCount < 10){                            // limit number of files
			var row = table.insertRow(rowCount);
			var colCount = table.tBodies[0].rows[rowCount-1].cells.length;
			for(var i=0; i<colCount; i++) {
				var newcell = row.insertCell(i);
				newcell.innerHTML = table.tBodies[0].rows[rowCount-1].cells[i].innerHTML;
			}
		    }else{
			 alert("Maximum number of files per deposition is 10");
		    }
		}
		function deleteRow(tableID,r) {
 		   var i = r.parentNode.parentNode.rowIndex;
		   var table = document.getElementById(tableID);
		   var rowCount = table.rows.length;
		   if (rowCount > 2){
		  	document.getElementById(tableID).deleteRow(i);
		   }else{
			alert("Al least one file should be selected");
		   }
		}
                function disableFromRadio(radio,radioVal,targetID){
                    ob  = document.getElementById(targetID);
                    inputs = ob.getElementsByTagName('input');
                    links  = ob.getElementsByTagName('a');
                    ob.classList.add("not-active");
                    if (radio.value == radioVal &&  radio.checked == true){
                        for (i=0; i < inputs.length; ++i) {
                                inputs[i].disabled=true;
                        }
                        for (i=0; i < links.length; ++i) {
                                links[i].classList.add("disabled");
                        }

                    }else{
                        for (i=0; i < inputs.length; ++i) {
                                links[i].classList.add("disabled");
                                inputs[i].disabled=false;
                        }
                    }
                }
                function hiddenFromRadio(radio,radioVal,targetID){
                    ob  = document.getElementById(targetID);
                    if (radio.value == radioVal &&  radio.checked == true){
                        ob.style.visibility="hidden";
                        ob.style.display="none";
                    }else{
                        ob.style.visibility = 'visible';
                        //ob.style.display = 'inline';
                        ob.style.display = 'table-row';
                    }
                }
