// Rutinas genericas manejo de formularios
// Pendiente de completar
//


function submitAs (f, op) {
	frm = eval('document.'+f);
	frm.op.value=op;
	frm.submit();
}

function setValue (f,n,v) {
	elem = _getObjinForm(f,n);
	switch (_getType(elem)) {
		case 'text':
		case 'select':
			_setInputValue(elem,v);
			break;
		case 'radio': 
			_setRadioValue(elem,v)
			break;
		case 'checkbox':
			_setChkState(elem,v);
			break;
	}
}

function getValue (f,n) {
	elem = _getObjinForm(f,n);
	switch (_getType(elem)) {
		case 'text':
		case 'select-one':
			return _getInputValue(elem);
			break;
		case 'radio': 
			return _getRadioValue(elem);
			break;
		case 'checkbox':
			return _getChkState(elem);
			break;
		case 'hidden':
			return elem.value;
		case 'date':
			return _ChkDate(elem);
			break;
	}
}		

function getSiNo (f,n) {
	return (getValue(f,n) == 1);
}

function setSiNo (f,n,v) {
	if (v)
		setValue(f,n,1);
	else
		setValue(f,n,2);
}

function _getObjinForm (f,e) {
	return eval ("document."+f+"."+e);
}

function _getType (elem) {
	if (typeof(elem.type) != 'undefined') 
		return elem.type;
	else
		return elem[0].type;
}

// INPUT TEXTO y SELECT
function _getInputValue (elem, n) {
	return elem.value;
}

function _setInputValue (elem, n, v) {
	elem.value = v;
}

// RADIO

function _getRadioValue (elem) {
	val = 0;
	for (var i=0;i<elem.length;i++) {
		if (elem[i].checked) 
			val = elem[i].value;
	}
	return val;
}	

function _setRadioValue (elem, v) {
	for (var i=0;i<elem.length;i++) {
		if (elem[i].value == v)
			elem[i].checked=1;
		else
			elem[i].checked=0;
	}
}

// CHECKBUT

function _getChkState(elem) {
	return elem.checked;
}

function _setChkState(elem,v) {
	elem.checked=v;
}

// CHECKDATE
function _ChkDate(elem){
	elem=new Date();
	return getYear(elem).getMonth(elem).getDay(elem);	
}
