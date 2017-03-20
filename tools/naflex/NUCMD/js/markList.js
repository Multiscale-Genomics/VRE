function markAll() {
    for (i = 0; i <= document.forms.retrieve.elements.length; i++) {
        document.forms.retrieve.elements[i].checked = true;
    }
}
function unMarkAll() {
    for (i = 0; i <= document.forms.retrieve.elements.length; i++) {
        document.forms.retrieve.elements[i].checked = false;
    }
}
function oneChecked() {
	var checkedAtLeastOne = false;
	$('input[type="checkbox"]').each(function() {
	    if ($(this).is(":checked")) {
        	checkedAtLeastOne = true;
	    }
	})
	if (!checkedAtLeastOne){
		alert("Please select at least one simulation.");
		return false;
	}
	return checkedAtLeastOne;
}
function checkMarkAll(){
    if (document.forms.retrieve.bmarkAll.checked)
        markAll();
    else
        unMarkAll();
}


