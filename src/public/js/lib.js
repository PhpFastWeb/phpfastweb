function $() {
  var elements = new Array();
  for (var i = 0; i < arguments.length; i++) {
    var element = arguments[i];
    if (typeof element == 'string')
      element = document.getElementById(element);
    if (arguments.length == 1)
      return element;
    elements.push(element);
  }
  return elements;
}
function addEvent( obj, type, fn ) {
  if ( obj.attachEvent ) {
    obj['e'+type+fn] = fn;
    obj[type+fn] = function(){obj['e'+type+fn]( window.event );}
    obj.attachEvent( 'on'+type, obj[type+fn] );
  } else
    obj.addEventListener( type, fn, false );
}
function removeEvent( obj, type, fn ) {
  if ( obj.detachEvent ) {
    obj.detachEvent( 'on'+type, obj[type+fn] );
    obj[type+fn] = null;
  } else
    obj.removeEventListener( type, fn, false );
}
//-------------------------------------------------------------
/*
function focusFormEditDelayed(){
    var bFound = false;
    var f = document.getElementById('form_edit');
    for(i=0; i < f.elements.length; i++) {
      if (f.elements[i].type != "hidden") {
        if (f.elements[i].disabled != true) {
            f.elements[i].focus();
            var bFound = true;
        }
      }
      if (bFound == true) {
        //alert(f.elements[i].name);
        break;
      }
    }
}
*/
function focusFormEdit() {
    focusFormEditDelayed();
    /*
    if(document.addEventListener) {
        document.addEventListener('load',focusFormEditDelayed,false); //W3C
    } else {
        document.attachEvent('onload',focusFormEditDelayed); //IE
    }
    */
}

//--------------------------------------------------------------------------------------------  
  //Timer functions
  function toMinuteAndSecond( x ) {
    var minutes = Math.floor(x/60);
    var seconds = x % 60;
    if (minutes > 5 ) {
        return minutes + " minutos";
    } else if (minutes > 1 ) {
        return minutes + " minutos " + seconds + " segundos";
    } else if (minutes == 1 ) {
        return minutes + " minuto " + seconds + " segundos";
    } else {
        return seconds + " segundos";
    }
    
  }
	
  function setTimer( remain, actions ) {
    var action;
    (function countdown() {
       document.getElementById("countdown").innerHTML = toMinuteAndSecond(remain);		
       if (action = actions[remain]) {
         action();
       }
       if (remain > 0) {
         remain -= 1;
         setTimeout(arguments.callee, 1000);
       }
    })(); // End countdown
  }
  
//  Example
//  setTimer(20, {
//    10: function () { document.getElementById("notifier").innerHTML="Faltan solo 10 segundos para comenzar el mantenimiento"; },
//     5: function () { document.getElementById("notifier").innerHTML="Faltan solo 5 segundos, se va a cerrar el sistema";        },
//     0: function () { document.getElementById("notifier").innerHTML="Ha comenzado el mantenimiento.";       }
//  });
  
  //--------------------------------------------------------------------------------------------------------------
  
function preloadImages() {
   if (document.images) {
      for (var i = 0; i < preloadImages.arguments.length; i++) {
         (new Image()).src = preloadImages.arguments[i];
      }
   }
}
//onload="preloadImages('images/sample1.gif', 'images/sample2.gif');"

//Esta función bloquea los controles del formulario una vez que se pulsa el botón de enviar.
//De esta forma, se evita que se envie más de una vez (lo que es importante si lleva algún
//fichero adjunto), así como evita modificar el contenido de algun control input lo que daría
//la falsa impresión de que se envia el nuevo valor.

function block_form_for_send(f) {
	for (i=0; i<f.elements.length; i++ ) {
		if (f[i].type.toLowerCase() == "submit") {
			f[i].value = 'Enviado...';
		}
		f[i].disabled = true;
	}
	return true;
}

//En construcción
function check_form(f) {
	error = '';
	for (i=0; i<f.elements.length; i++ ) {
		e = f.elements[i];
		if (typeof(e.max_len) != "undefined") {
			if ( e.max_len < strlen(e) ) {
				error += "Variable "+e.name+" demasiado larga. Longitud máxima: "+e.max_len+" caracteres.\n\n";
			}
		}	
	}
	//alert('test'); return false;
	if (error == '' ) return true;
	else {
		alert(error);
		return false;
	}
}
//--- habilitar una propiedad maxlength en los textarea ---------------------------------------------

function setMaxLength() {
	var x = document.getElementsByTagName('textarea');
	var counter = document.createElement('span');
	counter.className = 'max_len_counter';
	for (var i=0;i<x.length;i++) {
		if (x[i].getAttribute('maxlength')) {
			var counterClone = counter.cloneNode(true);
			counterClone.relatedElement = x[i];
			counterClone.innerHTML = '<br /><span>0</span> / '+x[i].getAttribute('maxlength')+'<br />';
			x[i].parentNode.insertBefore(counterClone,x[i].nextSibling);
			x[i].relatedElement = counterClone.getElementsByTagName('span')[0];
			x[i].onkeyup = x[i].onchange = x[i].onkeypress = checkMaxLength;
			x[i].onkeyup();
		}
	}
}

//http://www.quirksmode.org/dom/maxlength.html
function checkMaxLength() {
	var maxLength = this.getAttribute('maxlength');
	var currentLength = this.value.length;
	if (currentLength > maxLength) {
		this.relatedElement.className = 'max_len_toomuch';
		
		alert('Se ha alcanzado la longitud máxima de '+maxLength+' carateres');
		//Modificamos el contenido para evitar el "pegar" más de la cuenta
		//Si viene de un keypress, no va a pasar nada
		this.value = this.value.slice(0,maxLength);
		return false;
	}
	else
		this.relatedElement.className = '';
	this.relatedElement.firstChild.nodeValue = 'Longitud '+currentLength;
	//this.relatedElement.firstChild.innerHTML = 'Longitud '+currentLength+'/'+x[i].getAttribute('maxlength');
	// not innerHTML
}

//-----------------------------------------------------------------------------------

// Escribir un swf desde javascript (para ""cumplir"" los estandares por ejemplo) --------------------------------
function write_swf(swf_file,width,heigth,bgcolor,id,style,flashvars) {
  if (!id) {
    id='id';
  }
  if (!style) style="";
  if (!flashvars) flashvars="";
  document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" ');
  document.write('width="' + width +'" ');
  document.write('height="' + heigth + '" ');
  document.write('id="' + id + '" ');
  document.write('style="' + style + '" >'); 

  document.write('<param name="allowScriptAccess" value="sameDomain" />');
  document.write('<param name="movie" value="' + swf_file + '" />');
  document.write('<param name="quality" value="high" />');
  document.write('<param name="flashVars" value="' + flashvars + '" />');
  if (bgcolor=='transparent') {
    document.write('<param name="wmode" value="transparent">');
  } else {
    document.write('<param name="bgcolor" value="' + bgcolor + '" />');
  }

  document.write('<embed src="' + swf_file + '" ' );
  document.write('quality="high" ');
  if (bgcolor=='transparet') {
    document.write('wmode="transparent" ');
  } else {
  document.write('bgcolor="' + bgcolor + '" ');
  }
  document.write('width="' + width +'" '); 
  document.write('height="' + heigth + '" ');
  document.write('name="'+ id +'" ');
  document.write('flashVars="' + flashvars + '" ');
  document.write('align="middle" allowScriptAccess="sameDomain"');
  document.write('type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');
  document.write('</object>');
}
// Esta funcion no la uso ----------------------------------------------------------------------------------
function myGetElementById(id) {
	r = document.getElementById(id);
	if ( ! r ) {
		msg = 'No se econtró el elemento con id = ' + id ;
		alert(msg);
		throw(msg);
	}
}
// Funciones para lanzar excepciones -----------------------------------------------------------------------
function myThrow(exceptionMsg,errorCode) {
	if ( ! errorCode ) errorCode = 0; //No debería ser necesario, pero para consistencia de tipos
	alert(exceptionMsg);
	throw(exceptionMsg);
}
// Funciones de sonido -------------------------------------------------------------------------------------
function playSound(soundId) {
	document.all.sound.src = "click.wav";
	return;
	var sound = myGetElementById(soundId);
	try {
		sound.Stop();
		sound.Rewind();
	} catch (e) { }

	try {
		// For RealPlayer-enabled browsers.
		sound.DoPlay();
	} catch (e) {
		try {
			sound.Play();
		} catch(e) {
			
			myThrow ('El elemento con id = '+soundId+' no acepta los métodos Play() ni DoPlay() en playSound().');

		}
	}
}
//Gestión eventos -------------------------------------------------------------------------------------
function myAddEventListener(elementId,eventName,handlerFunction,doBubble) {
	element = document.getElementById(elementId);
	if ( ! element ) {
		msg = 'Elemento con id = '+elementId+' inexistente en myAddEventListener';
		alert(msg);
		throw(msg);
	}
	try {
		//Intentamos de la manera W3C
		//http://www.howtocreate.co.uk/tutorials/javascript/domevents
		if( document.implementation.hasFeature('MutationEvents','2.0') || window.MutationEvent || document.addEvelentListener ) {
  			element.addEventListener(eventName,handlerFunction,doBubble);
		} else {
			//Intentamos de la manera Microsoft Internet Explorer
			//alert(eventName+' '+handlerFunction+' '+elementId);
			element.attachEvent(eventName,handlerFunction);
			//Notese que de esta manera no puede especificarse si el evento causa Bubble
		}
	} catch(e) {
		alert('Su navegador no soporta eventos Javascript anidados.\nEstos son necesarios para el correcto funcionamiento de esta página.\nEs posible que algunos elementos no se muestren correctamente.');
	}
}

function myAddBodyOnLoadEvent(handlerFunction,doBubble) {
	try {
		//Intentamos de la manera W3C
		//http://www.howtocreate.co.uk/tutorials/javascript/domevents
		if( document.implementation.hasFeature('MutationEvents','2.0') || window.MutationEvent ) {
	  			element.addEventListener('load',handlerFunction,doBubble);
		} else if( document.addEventListener ) {
	  			document.addEventListener('load',handlerFunction,doBubble);
		} else {
			//Intentamos de la manera Microsoft Internet Explorer
			document.attachEvent(eventName,handlerFuction);
			//Notese que de esta manera no puede especificarse si el evento causa Bubble
		}
	} catch(e) {
		//document.onload=handlerFunction;
		alert('Su navegador no soporta eventos Javascript anidados.\nEstos son necesarios para el correcto funcionamiento de esta página.\nEs posible que algunos elementos no se muestren correctamente.');
	}	
}
//----------------------------------------------------------------------------------------------------------
//-- Funciones para controles de formulario
//----------------------------------------------------------------------------------------------------------

function getVirtualRows(text,width) {
  	var lines = text.split('\n');
  	//alert(lines.Length);
  //width = width - 2; //ajuste internet explprer
	var total_len = 0;
	var line_len = 0;
	var rows = 0;
	for( var i = 0 ; i < lines.length ; i++ ) {
		words = lines[i].split(' ');
		for( var j = 0 ; j < words.length ; j++ ) { //partimos en palabras
			if ( ( line_len + words[j].length + 1 ) > width ) {
				total_len += line_len + 1;
				line_len = 0;
				rows++;
			} else {
        line_len += words[j].length + 1;
      }
		}
		rows++;
	}
	//info_lines = document.getElementById('lines'); info_letters= document.getElementById('letters');
	//info_lines.value = rows; info_letters.value = line_len;
	
	return rows;
}
function adjustTextarea(ta) {
	
	var vrows = getVirtualRows(ta.value,ta.cols-1);
	//
	//if ( !document.all && !window.opera) { //mozilla
	//alert(vrows);
	if (ta.rows != vrows ) {
		ta.rows = vrows;
		ta.style.height = vrows+'em';
	}
}
function adjustTextareaEvent(e) {
	//alert('ok');
	if ( ! e ) { 
		e = documet.event;
	} 
	e.currentTarget.rows = getVirtualRows(e.currentTarget.value,e.currentTarget.cols);
}
function setTextareaAutoCols(textareaId) {
	myAddEventListener(textareaId,'onkeyup'  ,adjustTextareaEvent,true);
	myAddEventListener(textareaId,'onkeydown',adjustTextareaEvent,true);
	myAddEventListener(textareaId,'onchange' ,adjustTextareaEvent,true);
	myAddEventListener(textareaId,'onclick'  ,adjustTextareaEvent,true);
	myAddEventListener(textareaId,'onfocus'  ,adjustTextareaEvent,true);
	myAddEventListener(textareaId,'onblur'   ,adjustTextareaEvent,true);
	myAddBodyOnLoadEvent(adjustTextarea,true);
}
//----------------------------------------------------------------------------------------------------------
//-- Funciones de impresión
//----------------------------------------------------------------------------------------------------------
function printPreview() {
	// Para que funcione, tiene que utilizarse desde Internet Explorer, y sale un aviso a menos
	// que se añada el dominio local a los dominios de confianza.
	var OLECMDID = 7;
	/* OLECMDID values:
	* 6 - print
	* 7 - print preview
	* 1 - open window
	* 4 - Save As
	*/
	var PROMPT = 1; // 2 DONTPROMPTUSER 
	var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
	//alert('aha');
	self.document.body.insertAdjacentHTML('beforeEnd', WebBrowser); 
	WebBrowser1.ExecWB(OLECMDID, PROMPT);
	WebBrowser1.outerHTML = "";
}
function printPreviewConfigAlert() {
	var msg = 'Esta característica solo funciona en Internet Explorer.\n\n';
	msg = msg + 'Debe añadir este dominio web a la zona "Intranet Local" para que el botón Vista de Impresión funcione.\n\n';
	msg = msg + 'En Herramientas, Opciones de Internet, Seguridad, Sitios de Confianza, Sitios.\n';
	msg = msg + 'Desmarque "Requerir comprobación del servidor (https)" y añada "http://'+window.location.hostname+'" a la lista.\n\n';
	msg = msg + 'No olvide al imprimir seleccionar la orientación de folio vertical si la tabla se extiende más allá del margen derecho.';
	alert(msg);
}
function printPage() {
	window.print();
}

//----------------------------------------------------------------------------------------------------------
//-- Funciones para tratamiento de cookies
//----------------------------------------------------------------------------------------------------------

// Funciones sacadas de http://www.iec.csic.es/criptonomicon/cookies/recejava.html

// Esta es la función que usa Heinle para recuperar una cookie
// name - nombre de la cookie deseada
// devuelve un string conteniendo el valor de la cookie especificada o null si la cookie no existe
function getCookie(name){
  var cname = name + "=";               
  var dc = document.cookie;             
  if (dc.length > 0) {              
    begin = dc.indexOf(cname);       
    if (begin != -1) {           
      begin += cname.length;       
      end = dc.indexOf(";", begin);
      if (end == -1) end = dc.length;
        return unescape(dc.substring(begin, end));
    } 
  }
  return null;
}

// Esta es una adaptación de la función de Dorcht para colar una cookie
// http://www.webcoder.com/scriptorium/index.html
// name - nombre de la cookie
// value - valor de la cookie
// [expires] - fecha de caducidad de la cookie (por defecto, el final de la sesión)
// [path] - camino para el cual la cookie es válida (por defecto, el camino del documento que hace la llamada)
// [domain] - dominio para el cual la cookie es válida (por defecto, el dominio del documento que hace la llamada)
// [secure] - valor booleano que indica si la trasnmisión de la cookie requiere una transmisión segura
// al especificar el valor null, el argumento tomará su valor por defecto
function setCookie(name, value, expires, path, domain, secure) {
  document.cookie = name + "=" + escape(value) + 
  ((expires == null) ? "" : "; expires=" + expires.toGMTString()) +
  ((path == null) ? "" : "; path=" + path) +
  ((domain == null) ? "" : "; domain=" + domain) +
  ((secure == null) ? "" : "; secure");
}


// Esta es una adaptación de la función de Dorcht para borrar una cookie
// http://www.hidaho.com/cookies/cookie.txt
// name - nombre de la cookie
// [path] - camino de la cookie (debe ser el mismo camino que el especificado al crear la cookie)
// [domain] - dominio de la cookie (debe ser el mismo dominio que el especificado al crear la cookie)
// se considera el camino y dominio por defecto si se especifica null o no se proporcionan argumentos
function delCookie (name,path,domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" +
    ((path == null) ? "" : "; path=" + path) +
    ((domain == null) ? "" : "; domain=" + domain) +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}
//Funciones de tabla -------------------------------------------------------------------------------------
function ctable_data() {
	
	//Control de guardado de datos de la tabla
	this.unsaved = false;
	this.checkUnsaved = true;
	this.isUnsaved = function() {
		if ( this.checkUnsaved && this.unsaved ) {
			return true;
		}
	};
	this.onSetUnsaved = function() {};
	this.form_id = 'data_table_form';
	this.is_saved = function() {
		return (! unsaved);
	};
	this.setUnsaved = function(val) {
		if ( val != this.unsaved ) {
		  this.unsaved = true;
		  this.onSetUnsaved();
		}
	};
	
	this.submit = function() {
		f = document.getElementById(this.form_id);
		if ( ! f ) {
			msg = 'Error: No se ha definido formulario para la tabla';
			alert(msg); throw(msg); return false;
		}
		
		if ( this.checkSubmit() ) {
			this.unsaved = false; //Para evitar alertas de aviso de datos sin guardar
			f.submit();
		} else {
			return false;
		}		
	};
	this.checkSubmit = function() { return true; };
	
}
this.removeFormNonchangedValues = function() {
	/*
	f = document.getElementById(this.form_id);
	if ( ! f ) {
		msg = 'Error: No se ha definido formulario para la tabla';
		alert(msg); throw(msg);
	}
	for ( i = 0 ; i < f.items.lengtth ; i++ ) {
		if ( f.items[i].type = 3 ) {
			//Eliminamos los botones submit
			delete ( f.items[i] );
		} else if ( f.items[i].name.substr(0,6) == 'FILTER' ) {
			//Eliminamos los campos de filtros (ya fueron aplicados en los get de url)
			delete ( f.items[i] );
		} else {
			//Se trata probablemente de un campo de datos
			//Si se conserva su valor anterior
			anterior = document.getElementById(f.items[i].id+':anterior');
			if ( anterior ) {
				if ( anterior.value == f.items[i] ) {
					//Su valor no ha cambiado: eliminamos ambos
					delete(anterior);
					delete(f.items[i]);
				}
			}
		}
	}
	*/
};
table_data = new ctable_data();

function checkbox_all(chkall) {

}
function submit_jump( target, id_form) {
	f = document.getElementById('update_libreto');
	f.action = target;
	f.submit();
	return true;
}
//Funciones genericas -------------------------------------------------------------------------------------
function showhide (id, set) {
	divobj = document.getElementById(id);
	if (divobj==null) alert('Error js accediendo a id="'+id+'" en showhide()');
	if ( typeof(set) == 'undefined') {
		var set = ( divobj.style.display == 'none' );
	}
	if (set == false) {
		
		divobj.style.display = 'none';
	} else {
		divobj.style.display = 'block';
	}
	return false;
}
//Funciones de columnas--------------------------------------------------------------------------------------
function checkOnchangeKeyup(obinput) {
	
	if (document.getElementById(obinput.id+'_prev_')) {
		if (document.getElementById(obinput.id+'_prev_').value != obinput.value) {
			//alert(document.getElementById(obinput.id+'_prev_').value + ' != '+obinput.value);
			obinput.onchange();
		}
	}
}

function is_enabling( idinput, values_enable ) {
	
	var obinput = document.getElementById(idinput);
	
	if (obinput==null) {
		
		//¿Se trata de un conjunto de radio button?
		var val;
	    for (var i=0 ; i < values_enable.length ; i++ ) {
	    	val = values_enable[i];
			var obradio = document.getElementById(idinput+'_'+val+'_');
			if ( obradio && obradio.type == 'radio' ) {
				//alert(obradio.id+':'+obradio.checked+'('+val+'-'+values_enable);
				if (obradio.checked==true) {
					return true;
				} 
			}
	    }
	    return false;
		
	} else {
		
			
		if (obinput.type == "checkbox") {
			//alert(idinput+','+obinput+','+obinput.type+','+obinput.checked);
			if ( ( obinput.checked && in_array_or_value(1,values_enable) ) || 
				 (!obinput.checked && in_array_or_value(0,values_enable) )	
			) {
				return true;
			}
		} else if (obinput.value in oc(values_enable)) {
			return true;
		}
		return false;
	}
}

function in_array_or_value(val,arr) {
	//alert(val+','+arr);
	if (val==arr) {
		return true;
	} else {
		var found = false;		
		for( var i=0 ; i<arr.length ; i++ ) {
		    if (arr[i]==val) found = true;
		}
		return found;
	}
}

function oc(a) {
  var o = {};
  for(var i=0;i<a.length;i++) {
    o[a[i]]='';
  }
  return o;
}

function fireEvent(element,event) {
    if (element==null) {
        //Maybe element is already hidden
        //return; //TODO: Handle error on Development enviroment
    }
    if (document.createEventObject){
        // dispatch for IE
        var evt = document.createEventObject();
        return element.fireEvent('on'+event,evt)
    } else {
        // dispatch for firefox + others
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(event, true, true ); // event type,bubbling,cancelable
        return !element.dispatchEvent(evt);
    }
}

function bind_input( idinput, values_enable, id_target_input, id_target_div ) {
	//alert(values_enable);
	if ( is_enabling( idinput, values_enable ) ) {
		set_visible_control( true, id_target_div, id_target_input );
	} else {
		set_visible_control( false, id_target_div, id_target_input );
	}
    //alert(id_target_input);
    //fireEvent(document.getElementById(id_target_input),'change');
    return true;
}
function bind_input_anim( idinput, values_enable, id_target_input, id_target_div ) {
	 
	//alert('bind_input_anim(idinput='+idinput+' ; values_enable=['+values_enable+'] ; id_target_input='+id_target_input+' ; id_target_div='+id_target_div+')');
	if ( is_enabling ( idinput, values_enable ) ) {
		set_visible_control( true, id_target_div, id_target_input );
	} else {
		set_visible_control( false, id_target_div, id_target_input );
	}
    
    var target_input = document.getElementById( id_target_input );
    
    if ( target_input ) {
        fireEvent( target_input, 'change' );
    } else {
        //It may be a radio button group
        var e = document.getElementsByName( id_target_input );
        if ( ! e ) return true;
        for ( var i = 0 ; i < e.length ; i++ ) {
            fireEvent (e[i], 'change' );
        }
    }
	return true;
}
function set_visible_control( visibility, id_target_div, id_target_input ) {
	if ( visibility == true ) {
		//Mostramos el elemento
		var td = document.getElementById( id_target_div );
		if ( td != null ) {
			if ( td.type == 'text' ) {
				//anim_show(id_target_div,'inline');
				td.style.display = 'inline';
			} else {
				//anim_show(id_target_div,'block');
				td.style.display = 'block';
			}
		}
	} else {

		var t;
		//Borramos el contenido del input
		if ( document.getElementById( id_target_input ) == null ) {
			//Comprobamos si se trata de un conjunto radio button, intentamos anular todos los radio
            //alert('radio');
            e = document.getElementsByName( id_target_input );
            if ( e ) {
                //console.log(id_target_input);
                for ( var i = 0 ; i < e.length ; i++ ) {
                    if ( e[i].checked ) {
                        //console.log( id_target_input + '.' + e[i].id );
                        e[i].checked = false;
    			        fireEvent( e[i], 'change' );
                    }
                     
                }
            }
            
            /*
			var i=0;
			while (document.getElementById(id_target_input+'_'+i+'_') != null) {				
				document.getElementById(id_target_input+'_'+i+'_').checked = false;	
				i++;
			}
			t = id_target_input+'_'+1+'_';
            */
			
		} else {
			t = id_target_input;
            
			if ( document.getElementById(id_target_input).type == 'checkbox' ) {
				document.getElementById(id_target_input).checked = false;
			} else if ( document.getElementById(id_target_input).type == 'select-one' ) {
				document.getElementById(id_target_input).options[0].selected='selected';
			} else if ( document.getElementById(id_target_input).type == 'textarea ') {
				document.getElementById(id_target_input).value = '';
			} else { //if (document.getElementById(id_target_input).type == 'text' ) {
				document.getElementById( id_target_input ).value = '';
			}
		}
		
		//Ocultamos elemento y anulamos valor del input
		if (document.getElementById( id_target_div ) != null) {
			document.getElementById( id_target_div ).style.display = 'none';
		} else {
			//intentamos ocultar el control
		}	
		
		//Disparamos su onchange
		//(algunos navegadores/controles no lo disparan automáticamente tras cambiar)
		//if (t != id_target_input) {
		//	checkChanges = false;
		//	document.getElementById(t).onchange();
		//}
        
		return true;
	}
}
function anim_show( id, display ) {
	var ob = document.getElementById(id);
	//ob.style.height = '1px';
	ob.style.display = display;
	//ob.style.overflow = 'hidden';
	//ob.style.scrollbars = 'none';	
	var h = ob.scrollHeight;
	ob.style.overflow = '';
//	ob.style.scrollbars = '';
	ob.style.height = '0';
	var anim = new Animator({ duration: 1000})
	.addSubject(new NumericalStyleSubject(
		ob, 'height', 0, 100 ));
	//.addSubject(new CSSStyleSubject(
	//	$('personaje'), 'height:210px;'));
	anim.play();	
	
}
function anim_hide( id ) {
	var ob = document.getElementById(id);
	var chain = [];
	chain[0] = Animator.apply(ob, "opacity:0",{duration:750,transition: Animator.makeEaseIn(5)});
	chain[1] = Animator.apply(ob, "height:0",{duration:1000,transition: Animator.makeEaseIn(5)});
	var anim = new AnimatorChain(chain);
	anim.play();
}
function adaptTextarea( ta ) {
	//Resize textareas
	if ( ta != null ) {
		if ( typeof(ta) == 'string' ) {
			ob = document.getElementById(ta);
		} else {
			ob = ta;
		}
		if ( ob.type == 'textarea' ) {
			//alert(ob.style.height);
			if ( ob.style.height == '' ) {
				//First time we asign a value to height so we can compare later
				ob.style.height = ob.scrollHeight + 'px';
			} else {
				var h = parseInt(ob.style.height.substring(0,ob.style.height.length-2));
				//Chrome adds 4 pixels to height compared to scroollHeight
				if ( (h + 4) < ob.scrollHeight ) {
					ob.style.height=ob.scrollHeight + 'px';
				}
			}
		}
	}
}
//----------------------------------------------------------------------------


function goodbye(e) { 	
	if ( navigator.userAgent.toLowerCase().indexOf('webkit') > -1) {
		if (!( (! checkChanges) || formSending || ( ! formHasChanges ) )) {
			return 'Hay cambios no guardados. ¿Abandonar los cambios?';
		}
	} else {
		if(!e) e = window.event;
		//e.cancelBubble is supported by IE - this will kill the bubbling process.
		if ( (! checkChanges) || formSending || ( ! formHasChanges ) ) {
			e.cancelBubble = false;
			return
		}
		e.cancelBubble = true;
		e.returnValue = 'Hay cambios no guardados. ¿Abandonar los cambios?\n\nAceptar = Abandonar cambios\nCancelar = Mantenerse en esta página, y continuar editando'; //This is displayed on the dialog
	
		//e.stopPropagation works in Firefox.
		if (e.stopPropagation) {
			e.stopPropagation();
			e.preventDefault();
		}
	}
}

window.onbeforeunload = goodbye;

//-----------------------------------------------------
var newwin;
var newwin_idinput;
function openPopup( idinput, url, params ) {
	if ( ! newwin || newwin.closed ) {
		var top = 50; var left = 350; var height = 650; var width = 650; //var height = 420;//var width = 600;
		var param  = 'width='+width+',height='+height+',scrollbars=YES,top='+top+',left='+left;
		newwin = window.open(url,'popSingle',param);
		newwin_idinput = idinput;
	}
	if ( newwin ) {
		newwin_idinput = idinput;
		newwin.focus();
	}
}
function selectPopup(pks,data) {
	document.getElementById( newwin_idinput ).value = pks;
	document.getElementById( newwin_idinput+"_text_" ).innerHTML = data;
	newwin.close();
	hasChanges(document.getElementById( newwin_idinput ) );
	//alert(pks + ' - ' + data);
}
function addPopup( pks ,data ) {
	document.getElementById( newwin_idinput ).value = pks;
	//document.getElementById(newwin_idinput+"_text_").innerHTML = data;
	newwin.close();
	document.getElementById( 'form_add' ).submit();
	//hasChanges();
	//alert(pks + ' - ' + data);
}
//-----------------------------------------------------
function image_popup( link_obj ) {
    if ( typeof image_popup.imgpopwin == 'undefined' || image_popup.imgpopwin.closed ) {
		var top = 50; var left = 350; var height = 500; var width = 500; //var height = 420;//var width = 600;
		var param  = 'width='+width+',height='+height+',scrollbars=NO,resizable=1,top='+top+',left='+left;
		
		url = link_obj.href;
		//url = 'http://localhost/sernetcanf/_inc/get_img_usuario.php?file=vherrera_foto_facebook.jpg';
		image_popup.imgpopwin = window.open( url, 'imgpopup', param );
		image_popup.imgpopwin.document.title = 'Imágen';
		
		image_popup.imgpopwin[image_popup.imgpopwin.addEventListener ? 'addEventListener' : 'attachEvent'](
		  ( image_popup.imgpopwin.attachEvent ? 'on' : '' ) + 'load', auto_resize, false
		);

	} else if ( image_popup.imgpopwin ) {
		//image_popup.imgpopwin.focus();
		auto_resize();
	}
	return false;
}
function auto_resize() {
		
    iWidth = image_popup.imgpopwin.window.innerWidth; 
    iHeight = image_popup.imgpopwin.window.innerWidth;
    
    iWidth = image_popup.imgpopwin.document.images[0].width - iWidth+0; 
    iHeight = image_popup.imgpopwin.document.images[0].height - iHeight+0; 
    image_popup.imgpopwin.window.resizeBy(iWidth, iHeight);
    
    image_popup.imgpopwin.window.resizeBy( -20, -20 ); 
    image_popup.imgpopwin.self.focus(); 
    return false;
}
function linked_change(oinput) {
	//if ( oinput.value == oinput.defaultValue ) return;	
	if ( oinput.value.trim() == "" ) {
		document.getElementById( oinput.id+'_text_' ).innerHTML='&nbsp;';
	} else {
		document.getElementById( oinput.id+'_text_' ).innerHTML='(guarde para mostrar el nuevo valor)';
	}
	hasChanges(oinput);
}