function showhide (id, set) {
  divobj = document.getElementById(id);
  if (set == false) {
    divobj.style.display = 'none';
  } else {
    divobj.style.display = 'block';
  }
  return true;
}
//Ejemplo de uso:
//<div id="zona"> Hola! </div>
//<input type=button value="ocultar hola" onClick="showhide('zona',false);" />

//Otro ejemplo de uso:
//<div id="zona1">
//zona1<br>
//<input type=button value="pasar a zona 2" onClick="showhide('zona1',false); showhide('zona2',true);" />
//</div>
//<div id="zona2" style="display:none;">
//zona2<br>
//<input type=button value="pasar a zona 2" onClick="showhide('zona2',false); showhide('zona1',true);" />
//</div>

function showhide2(obj, set) {
  if (set == false) {
    obj.style.display = 'none';
  } else {
    obj.style.display = 'block';
  }
}
//Ejemplo de uso:
//<input type=button value="test" onClick="showhide2(this,false);" />
//<input type=button value="ocultar este form" onClick="showhide2(this.form,false);" />

function showhideSwich(id) {
  divobj = document.getElementById(id);
  if (divobj.style.display != 'none') {
    divobj.style.display = 'none';
  } else {
    divobj.style.display = 'block';
  }
  return true;
}
//Ejemplo de uso:
//<div id="zona"> Hola! </div>
//<input type=button value="mostrar/ocultar" onClick="showhide('zona');" />

//Otro ejemplo de uso
//<div id="zona1">
//zona1<br>
//<input type=button value="pasar a zona 2" onClick="showhide('zona1',false); showhide('zona2',true);" />
//</div>
//
//<div id="zona2">
//zona2<br>
//<input type=button value="pasar a zona 2" onClick="showhide('zona2',false); showhide('zona1',true);" />
//</div>


// -----------------------------------------------------------------------------

function items_init() {
  display_boxes();
  display_controls();
  actualice_resume('resume');
}

function actualice_resume(id) {
  if (!id) id='resume';
  var resume='';
  var resume_div = document.getElementById(id);
  eval ('n='+items_number_name+';');
  for (i=1; i<=n; i++) {
    //eval ("t = ( typeof(" + items_list_prefix + items_list_inputs[0]+i+".nonvalid ) != 'undefined' && " +
    //      items_list_prefix + items_list_inputs[0]+i+".nonvalid != " + items_list_prefix + items_list_inputs[0]+i+".value );"
    //      );
    //if (t) {
    //  resume = resume + '<b>'+i+':</b><br />';
    //} else {
      eval("resume = resume + '<b>'+i+': '+"+items_list_prefix+items_list_inputs[0]+i+".value+'</b><br />';");
    //}
  }
  eval("resume_div.innerHTML='"+resume+"';");
}

function display_boxes() {
  eval ('n='+items_number_name+';');
  for (i=1; i<=items_max; i++) {
    //showhide(items_id+i,i<=n);
    showhide(items_id+i,i==n);
  }
  actualice_resume('resume');
}

function display_controls() {
  eval ('n='+items_number_name+';');
  showhide('add_item_button',!( n >= items_max ));
  showhide('del_item_button',!(items_min >= n ));
}

function add_item(button,varname_number) {
  t = true;
  if (button && varname_number) {
    t = check_items_form(button.form,varname_number);
    if (!t) return t;
  }
  eval ( 'a=++' + items_number_name + ';' );
  showhide2(button,true);
  //eval ("t = showhide('" + id + a + "',true);" );
  items_init();
  return t;
}

function del_item(button) {
  t = true;
  eval ( 'e=' + items_number_name + '--;' );
  showhide2(button,true);
  clear_elements(e);
  items_init();
  return t;
}

function clear_elements(n) {
  for(i=0; i<items_list_inputs.length; i++) {
    eval ('hasnonvalid = (typeof('+items_list_prefix+items_list_inputs[i]+n+'.nonvalid)!="undefined");');
    //alert('hasnonvalid = (typeof('+items_list_prefix+items_list_inputs[i]+n+'.nonvalid)!="undefined")='+hasnonvalid);

    if (hasnonvalid) {
      eval(items_list_prefix+items_list_inputs[i]+n+".value="+items_list_prefix+items_list_inputs[i]+n+".nonvalid;");
      //alert("nonvalid");
    } else {
      eval(items_list_prefix+items_list_inputs[i]+n+".value='';");
    }
  }
  if (typeof(items_list_notchecked) != "undefined") {
    for( i=0 ; i<items_list_notchecked.length ; i++ ) {
      eval(items_list_prefix+items_list_notchecked[i]+n+".checked=false;");
    }
  }
  if (typeof(items_list_checked) != "undefined") {
    for( i=0 ; i<items_list_checked.length ; i++ ) {
      eval(items_list_prefix+items_list_checked[i]+n+".checked=false;");
    }
  }
  return true;
}

function itemsClass(items_max, items_min, items_id, items_number_name, items_list_prefix, items_list_inputs) {
  this.items_max = items_max;
  this.items_min = items_min;
  this.items_id=items_id;
  this.items_number_name = items_number_name;
  this.items_list_prefix = items_list_prefix;
  this.items_list_inputs = items_list_inputs;
}

function fillFormData(form, data) {
  //alert('fillFormData 2');
  for (e in form) {
    //alert( typeof(form[e].type) + " : " + form[e].type );
    if ( form[e] && typeof(form[e].type) == 'undefined' && typeof(form[e][0]) != 'undefined' &&
         typeof(form[e][0].type) == 'string' && form[e][0].type == 'radio' ) {
      //es un radio
      //alert('radio: ' + typeof(form[e][0].type) +' '+ form[e][0].type);
      for(r in form[e]) {
        //alert(form[e][r].name + " =");
        if (form[e][r].name && typeof(data[form[e][r].name]) != 'undefined') {
          if (data[form[e][r].name] == form[e][r].value) {
            form[e][r].checked = true;
          }
        }
      }
    } else if ( form[e] && form[e].name && typeof(data[form[e].name]) != 'undefined' ) {
      //alert( "form[" + e + "].value = " + data[form[e].name] );
      if (form[e].type == 'radio' ) {
        //alert(form[e].name);
        if ( form[e].value == data[form[e].name]) {
          form[e].checked = true;
        }
      } else {
        form[e].value = data[form[e].name];
      }
    }
  }
  return true;
}
