function addJavascript(jsname,pos) {
	var th = document.getElementsByTagName(pos)[0];
	var s = document.createElement('script');
	s.setAttribute('type','text/javascript');
	s.setAttribute('src',jsname);
	th.appendChild(s);
}
/*
	
	document.getElementsByTagName('head')[0].appendChild(
		document.createElement('script')
			.setAttribute('type','text/javascript')
			.setAttribute('src','s5base.js'));

 */
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

function delegate(that, thatMethod) {
	return function() { return thatMethod.call(that); }
}
