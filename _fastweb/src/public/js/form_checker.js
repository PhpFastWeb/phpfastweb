
// Funcion que verifica el formulario, debe ser llamada en onSubmit()
// como return check_form(this);
// Ver ejemplo
/*
<form action="paso2.php" method="post"
onSubmit="
this.movil.numeric = true;
this.movil.optional = true;
this.email.optional = true;
this.codigo_postal.numeric = true;
this.telefono.numeric = true;
return check_form(this);
"
>
*/

function check_form(f) {
	var msg;
	var empty_fields = "";
	var errors = "";
	// Loop through the elements of the form, looking for all
	// text and textarea elements that don't have an "optional" property
	// defined. Then, check for fields that are empty and make a list of them.
	// Also, if any of these elements have a "min" or a "max" property defined,
	// then verify that they are numbers and that they are in the right range.
	// Put together error messages for fields that are wrong.

	var radios = new Array(); //Almacenamos qué familia de radio buttons están checkeados
	for (var i = 0; i < f.length; i++) {
		var e = f.elements[i];

		//TIPO RADIO
		if ((e.type == "radio" && !e.optional)) {
			radios[e.name] = radios[e.name] | e.checked;
		} else if ( ( (e.type == "text") || (e.type == "textarea") || (e.type == "select-one" ) ) ) {
			//TIPOS TEXT TEXTAREA SELECT-ONE
			// Comprobamos que los obligatorios no estan vacios
			//alert(e.name + " " + e.optional + " : " + " - " + isblank(e,value));
			if ( !e.optional && ( (e.value == null) || (e.value == "") || isblank(e.value) || ( e.nonvalid && e.value == e.nonvalid ) ) ) {
				//alert(e.name);
				empty_fields += "\n " + e.name;
			} else if ( !(e.optional && (e.value == null || isblank(e.value) ) )   &&
			(e.numeric || (e.min != null) || (e.max != null)) ) {
				// Comprobamos que los numéricos no-vacios-obligatorios sean válidos
				var v = parseFloat(e.value);
				if (isNaN(v) ||	((e.min != null) && (v < e.min)) ||	((e.max != null) && (v > e.max))) {
					errors += "- El campo " + e.name + " debe ser numérico";
					if (e.min != null)
					errors += " mayor de " + e.min;
					if (e.max != null && e.min != null)
					errors += " y menor de " + e.max;
					else if (e.max != null)
					errors += " que es menor de " + e.max;
					errors += ".\n";
				}
			}
			// Comprobamos que los select-one no estén en un valor del tipo "-elija-"
			//if (e.type=="select-one" && typeof(e.nonvalid) != 'undefined' && e.value == e.nonvalid) {
			// empty_fields += "\n " + e.name;
			//}
		}
	}
	//fin del recorrido

	for (prop in radios) {
		//alert(prop + '= ' + radios[prop]);
		if (!radios[prop]) {
			empty_fields += "\n " + prop;
		}
	}

	// Si no hay errores, devuelve true
	if (!empty_fields && !errors) return true;
	// Si hay errores, indica un mensaje de error
	error_msg(errors,empty_fields);
	return false;
}

// -----------------------------------------------------------------------------

// Esta función devuelve true si la cadena pasada solo tiene blancos

function isblank(s) {
	if ( s == "" || s == null ) return true;
	for (var i = 0 ; i < s.length ; i++) {
		var c = s.charAt(i);
		if ( (c != ' ') && (c != '\n') && (c != '\t') )
		return false;
	}
	return true;
}

//------------------------------------------------------------------------------

function error_msg(errors, empty_fields) {
	msg = "______________________________________________________________\n\n"
	msg += "Debe rellenar los campos obligatorios:\n";
	msg += "______________________________________________________________\n\n"
	if (empty_fields) {
		msg += "- Los siguientes campos no están rellenos:"
		+ empty_fields + "\n";
		if (errors) msg += "\n";
	}
	msg += errors;
	alert(msg);
	return false;
}


function check_items_form(form, varname_number) {
	eval( 'n = form. ' + varname_number + '.value;');
	if (typeof(form.list_optionals) == 'undefined' ) {
		form.list_optionals = false;
	}
	errors = ''; empty_fields = '';
	radios = new Array();
	//alert(form.trabajo_inicio_mes1.nonvalid);
	for( var i = 0 ; i < form.length ; i++ ) {
		var e = form.elements[i];

		if ( ( e.name.length>0 ) && ( e.name[e.name.length-1] <= n ) ) {
			//alert(e.name + '\n' + e.name[e.name.length-1]);
			if ( ( e.type == "text" ) || ( e.type == "textarea" ) || ( e.type=="select-one" ) ) {
				//alert(e.name+ '\n' + e.nonvalid);
				if ( isblank(e.value) || ( e.nonvalid && e.value == e.nonvalid ) ) {
					empty_fields += "\n " + e.name;
				}
			} else if (e.type == "radio") {
				radios[e.name] = radios[e.name] | e.checked ;
				//eval('radios.' + e.name + ' = radios.' + e.name + ' | e.checked;');
			}
			if (e.numeric || (e.min != null) || (e.max != null)) {
				var v = parseFloat(e.value);
				if (isNaN(v) ||
				((e.min != null) && (v < e.min)) ||
				((e.max != null) && (v > e.max))) {
					errors += "- El campo " + e.name + " debe ser numérico";
					if (e.min != null)
					errors += " que es mayor de " + e.min;
					if (e.max != null && e.min != null)
					errors += " y menor de " + e.max;
					else if (e.max != null)
					errors += " que es menor de " + e.max;
					errors += ".\n";
				}
			}
		}
	}

	//Recorremos los radios para comprobar si hay alguno en blanco
	for (prop in radios) {
		//alert(prop + '= ' + radios[prop]);
		if (!radios[prop]) {
			empty_fields += "\n " + prop;
		}
	}

	// Si no hay errores, devuelve true
	if (!empty_fields && !errors) return true;

	// Si hay errores, indica un mensaje de error
	error_msg(errors,empty_fields);
	return false;
}

