function session_expire(url_login) {
    
    //if ( ! document.getElementById("div_block_website") ) {
        
        var divTag = document.createElement("div");
        divTag.id = "div_block_website2";
        divTag.setAttribute("align","center");                            
        divTag.style.cssText = "z-index:20; text-align:center; padding:100px 0; font-size:14px; position:absolute; left: 0px; top: 0px; width:100%; background-color: white; opacity:0.9; filter:alpha(opacity=90);";                            
        divTag.innerHTML = "<div style=\"background-color:white; width:550px; background-color:white; border:1px solid gray; padding: 10px 10px; margin:0 auto;\">Su sesión ha permanecido demasiado tiempo inactiva, y ha sido cerrada por seguridad.<br /><a href=\""+url_login+"\">Inicie sesión de nuevo en el sistema</a>";                    
        
		divTag.style.cssText += "height: " +
	    Math.max( //Devuelve el alto real de la página renderizada, distintos métodos para distintos navegadores
	        Math.max(document.body.scrollHeight, D.documentElement.scrollHeight),
	        Math.max(document.body.offsetHeight, D.documentElement.offsetHeight),
	        Math.max(document.body.clientHeight, D.documentElement.clientHeight)
	    ) + "px;" ; //height:100% no tiene en cuenta elementos con position:absolute ,hay que calcular
        
		if ( document.getElementById("cambios") && document.getElementById("cambios").display != "none") {            
            divTag.innerHTML += "<br /><b>Usted tenía cambios sin guardar, verifíquelos cuando vuelva a iniciar sesión</b>";            
        }
        divTag.innerHTML += "</div>";
        document.body.appendChild(divTag);
     //}  
     if ( document.getElementById("cambios") && document.getElementById("cambios").display != "none") {
        	document.getElementById("cambios").display = "none";
        	checkChanges = false;
     }
     
     document.activeElement.blur();              
}