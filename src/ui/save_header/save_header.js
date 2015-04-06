var formHasChanges = false;
var formSending = false;
var checkChanges = true;
//var changes = 'Cambios: ';
/*
function applyChangedStyle(ob) {
	//var name = ob.style.className; alert(name);
	ob.style.backgroundColor = '#FEFEE2';
	//ob.style.className = name;
}
*/
function hasChanges(ob) {
	
	if ( ob != null && ob.type == 'textarea' ) {
		adaptTextarea(ob);
	}
	if ( ! checkChanges ) return;
	if ( ! document.getElementById('cambios')) return;
	//alert('hasChanges');
	//applyChangedStyle(ob); //.style.backgroundColor = '#FEFEE2';
	ob.style.backgroundColor = '#FEFEE2';
	if ( ! formHasChanges ) {
		//changes = changes + ob.id + ' ';
		//alert(changes);		
		formHasChanges = true; //TODO: no usar globales
		if ( formSending == true ) { //|| document.getElementById('cambios').style.display == 'block' ) {
			return;
		}
        //If changes bar is hidden, we show it
        if ( document.getElementById('cambios').style.display != 'block' ) {
            changeOpac(0, 'cambios'); 
            document.getElementById('cambios').style.display = 'block';
            opacity('cambios', 0, 100, 1000);
        }
        //If buttons are disabled, we enable it
        document.getElementById('cambios_deshacer').disabled = false;
        document.getElementById('cambios_guardar').disabled = false;
        //If 'pending changes' is not visible, we enable it
        document.getElementById('unsaved_msg').style.display = 'inline';
	}
}
function undoChanges(target) {
    if ( ! confirm ("¿Está seguro de que quiere descartar los cambios?") ) return;
    formSending=true;
    document.getElementById('cambios_deshacer').disabled = true;
    document.getElementById('cambios_guardar').disabled = true;
    if ( document.getElementById('form_submit') ) document.getElementById('form_submit').disabled = true;
    document.getElementById('unsaved_msg').style.display = 'inline';
    document.getElementById('unsaved_msg').innerHTML = 'Deshaciendo...'; 
    document.location = target; 
}
function saveChanges(target) {
    formSending=true;
    document.getElementById('cambios_deshacer').disabled = true;
    document.getElementById('cambios_guardar').disabled = true;
    if ( document.getElementById('form_submit') ) document.getElementById('form_submit').disabled = true;
    document.getElementById('unsaved_msg').style.display = 'inline';
    document.getElementById('unsaved_msg').innerHTML = 'Guardando...';
    document.location = target; 
}