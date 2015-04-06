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

var s5anim = new function() {

	this.fadeOut = function(id, millisec) {
		millisec = typeof (a) != 'undefined' ? millisec : 1000;
		this.changeOpac(100, id);
		this.currentOpac(id, 0, millisec);

	};

	this.fadeIn = function(id, millisec) {
		millisec = typeof (a) != 'undefined' ? millisec : 1000;
		document.getElementById(id).style.opacity = "0";
		document.getElementById(id).style.display = "block";
		this.changeOpac(0, id);
		this.currentOpac(id, 100, millisec);

	};

	this.opacity = function(id, opacStart, opacEnd, millisec) {
		//speed for each frame
		var speed = Math.round(millisec / 100);
		var timer = 0;
		// determine the direction for the blending, if start and end are the
		// same nothing happens
		if (opacStart > opacEnd) {
			for ( var i = opacStart; i >= opacEnd; i--) {
				setTimeout("s5anim.changeOpac(" + i + ",'" + id + "')",
						(timer * speed));
				timer++;
			}
		} else if (opacStart < opacEnd) {
			for ( var i = opacStart; i <= opacEnd; i++) {
				setTimeout("s5anim.changeOpac(" + i + ",'" + id + "')",
						(timer * speed));
				timer++;
			}
		}
	};
	this.changeOpac = function(opacity, id) {
		var object = document.getElementById(id).style;
		object.opacity = (opacity / 100);
		object.MozOpacity = (opacity / 100);
		object.KhtmlOpacity = (opacity / 100);
		object.filter = "alpha(opacity=" + opacity + ")";
	};

	this.shiftOpacity = function(id, millisec) {
		//if an element is invisible, make it visible, else make it ivisible
		if (document.getElementById(id).style.opacity == 0) {
			this.opacity(id, 0, 100, millisec);
		} else {
			this.opacity(id, 100, 0, millisec);
		}
	};

	this.blendimage = function(divid, imageid, imagefile, millisec) {
		var speed = Math.round(millisec / 100);
		var timer = 0;
		// set the current image as background
		document.getElementById(divid).style.backgroundImage = "url("
				+ document.getElementById(imageid).src + ")";
		// make image transparent
		this.changeOpac(0, imageid);
		// make new image
		document.getElementById(imageid).src = imagefile;
		// fade in image
		for ( var i = 0; i <= 100; i++) {
			setTimeout("changeOpac(" + i + ",'" + imageid + "')",
					(timer * speed));
			timer++;
		}
	};

	this.currentOpac = function(id, opacEnd, millisec) {
		//standard opacity is 100
		var currentOpac = 100;

		// if the element has an opacity set, get it
		if (document.getElementById(id).style.opacity < 100) {
			currentOpac = document.getElementById(id).style.opacity * 100;
		}
		//call for the function that changes the opacity
		this.opacity(id, currentOpac, opacEnd, millisec)
	};
	// ------------------------------------------------------------------------------
	this.openCurtain = function(id, millisec) {
		var ob = document.getElementById(id);
		var size = parseInt(ob.style.height.split('px')[0]);
		ob.style.height = 0 + 'px';
		ob.style.overflow = "hidden";
		ob.style.display = "block";
		var speed = Math.round(millisec / 1000);
		var timer = 0;
		var step = 0;

		for ( var i = 0; i <= size; i++) {
			step = this.smoothTimer(timer, size, speed);
			// setTimeout("s5anim.curtainTimer(" + i + ",'" + id + "')",(timer *
			// speed));
			setTimeout("s5anim.curtainTimer(" + i + ",'" + id + "')", step);
			timer++;
		}
	};

	this.closeCurtain = function(id, millisec) {
		var speed = Math.round(millisec / 1000);
		var ob = document.getElementById(id);
		var size = parseInt(ob.style.height.split('px')[0]);
		var timer = 0;
		var step = 0;
		// determine the direction for the blending, if start and end are the
		// same nothing happens
		for ( var i = size; i >= 0; i = i - 2) {
			step = this.smoothTimer(step, i, speed);
			setTimeout("s5anim.curtainTimer(" + i + ",'" + id + "')", step);
			timer++;
		}
	};
	this.smoothTimer = function(value, finalValue, speed) {
		var t = value / finalValue; // De-escalamos
		// --
		// t = (1/( 1 + Math.pow(Math.E,(-1*t))));
		// t = Math.sin(t * Math.PI / 2); //Sigmoide
		t = 2 / t;
		// --
		t = t * finalValue; // Re-escalamos
		t = t * speed; // Velocidad
		return t;

	};

	this.curtainTimer = function(size, id) {
		document.getElementById(id).style.height = size + 'px';
	};
	/**
	 * Easing equation function for a quintic (t^5) easing in/out: acceleration
	 * until halfway, then deceleration.
	 * 
	 * @param t
	 *            Current time (in frames or seconds).
	 * @param b
	 *            Starting value.
	 * @param c
	 *            Change needed in value.
	 * @param d
	 *            Expected easing duration (in frames or seconds).
	 * @return The correct value.
	 */
	this.easeInOutQuint = function(t, b, c, d) {
		if ((t /= d / 2) < 1)
			return c / 2 * t * t * t * t * t + b;
		return c / 2 * ((t -= 2) * t * t * t * t + 2) + b;
	};

	// ----------------------------------------------------------------------
	this._typingText = "content of text here";
	this._typingDelay = 60;
	this._typingCurrentChar = 0;
	this._typingDestination = "[not defined]";
	this._typingBuffer = "";

	this.type = function() {
		if (this._typingText == "") return;
		var dest = document.getElementById(this._typingDestination);
		var car = this._typingText.substr(this._typingCurrentChar, 1);
		if (car == '<') {
			var i = 1;
			var fcar = this._typingText.substr(this._typingCurrentChar + i, 1);
			while (fcar != '>' && i < this._typingText.length) {
				i++;
				fcar = this._typingText.substr(this._typingCurrentChar + i, 1);
			}
			dest.innerHTML = this._typingText.substr(0, this._typingCurrentChar	+ i);
			this._typingCurrentChar = this._typingCurrentChar + i + 1;
			this.type();
		} else {
			dest.innerHTML = this._typingText
					.substr(0, this._typingCurrentChar);
			this._typingCurrentChar++;
			if (this._typingCurrentChar > this._typingText.length) {
				this._typingCurrentChar = 1;
			} else {
				setTimeout("s5anim.type()", this._typingDelay);
			}
		}

	};
	this.startTyping = function(textParam, delayParam, destinationParam) {

		this._typingText = textParam;
		this._typingDelay = delayParam;
		this._typingCurrentChar = 0;
		this._typingDestination = destinationParam;

		this.type();
	};
	this.stopTyping = function() {
		this._typingText = "";
		this._typingDelay = 0;
		this._typingCurrentChar = 0;
	};

	//startTyping(text, 50, "textDestination");

};
