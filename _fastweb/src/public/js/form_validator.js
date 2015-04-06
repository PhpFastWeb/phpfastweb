Array.prototype.has=function(v){
	for (i=0;i<this.length;i++){
		if (this[i]==v) return i;
	}
	return false;
}

function nifValido(nif) {
	numero = nif.substr(0,nif.length-1);
	let = nif.substr(nif.length-1,1);
	numero = numero % 23;
	letra='TRWAGMYFPDXBNJZSQVHLCKET';
	letra=letra.substring(numero,numero+1);
	if (letra!=let) 
		return false;
	return true;	
}

function validate(oform, optional) {
	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	var valid = true; var msg = ''; var email = ''; var msg_oblig = '';
	for (var i = 0 ; i < oform.elements.length ; i++ ) {		
		if ( ! optional.has(oform.elements[i].name) ) {
			if (oform.elements[i].type == 'checkbox' &&  ! oform.elements[i].checked) {
				valid = false;
				msg_oblig = "Debe rellenar y marcar todos los campos obligatorios.\n";
			} else if ( oform.elements[i].value == "" ) {
				valid = false;
				msg_oblig = "Debe rellenar y marcar todos los campos obligatorios.\n";
			}
		}
		if ( ( oform.elements[i].name == 'dni' || oform.elements[i].name == 'nif' ) &&
			oform.elements[i].value != '') {
				oform.elements[i].value = oform.elements[i].value.split('-').join('');
				oform.elements[i].value = oform.elements[i].value.split('.').join('');
				oform.elements[i].value = oform.elements[i].value.split(' ').join('');
				oform.elements[i].value = oform.elements[i].value.toUpperCase()
				if ( ! nifValido(oform.elements[i].value) ) {
					valid = false;
					msg += "Debe especificar un NIF válido.\n";
				}
		}
							
		if ( oform.elements[i].name == 'email' ) {
			email = oform.elements[i].value;
			if (email != '' && ! ( emailPattern.test(email) )) {
				valid = false;
				msg += "Debe especificar un email válido.\n";
			}
		}
	}
	msg = msg_oblig + msg;
	if ( ! valid ) alert ( msg + " " );
	return (msg == '');
}