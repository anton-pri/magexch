AjaxCategories = function (element_id, category_id, onSelected, onClose) {
	this.timeout = null;
	this.onSelected = onSelected || null;
	this.onClose = onClose || null;
	this.hidden = false;

    this.category_id = category_id;

	// HTML elements
	this.element = null;
    this.element_id = element_id;
	// Information
	this.categoryClicked = false;
};

AjaxCategories.is_ie = ( /msie/i.test(navigator.userAgent) &&
		   !/opera/i.test(navigator.userAgent) );
AjaxCategories.is_khtml = /Konqueror|Safari|KHTML/i.test(navigator.userAgent);

AjaxCategories.getAbsolutePos = function(el) {
	var SL = 0, ST = 0;
	var is_div = /^div$/i.test(el.tagName);
	if (is_div && el.scrollLeft)
		SL = el.scrollLeft;
	if (is_div && el.scrollTop)
		ST = el.scrollTop;
	var r = { x: el.offsetLeft - SL, y: el.offsetTop - ST };
	if (el.offsetParent) {
		var tmp = this.getAbsolutePos(el.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}
	return r;
};

AjaxCategories.MouseDown = function (ev) {
    return AjaxCategories.stopEvent(ev);
};

AjaxCategories.addEvent = function(el, evname, func) {
    if (el.attachEvent) { // IE
        el.attachEvent("on" + evname, func);
    } else if (el.addEventListener) { // Gecko / W3C
        el.addEventListener(evname, func, true);
    } else {
        el["on" + evname] = func;
    }
};

AjaxCategories.prototype.create = function (_par) {
	var parent = null;
    if (! _par)
        parent = document.getElementsByTagName("body")[0];
    else
        parent = _par;

	var element = document.createElement('div');
    element.id = this.element_id+'_div';
    element.style.height = '150px';
    element.style.width = '150px';
    element.style.overflow = 'auto';
    element.style.position = 'absolute';
    element.style.display = 'none';
    AjaxCategories.addEvent(element, 'mousedown', AjaxCategories.MouseDown);

	this.element = element;
    cw_ajax_categories(element.id, 0, this.category_id);

	parent.appendChild(this.element);
};

/** Calls the first user handler (selectedHandler). */
AjaxCategories.prototype.callHandler = function () {
	if (this.onSelected) {
		this.onSelected(this, this.date.print(this.dateFormat));
	}
};

/** Calls the second user handler (closeHandler). */
AjaxCategories.prototype.callCloseHandler = function () {
	if (this.onClose) {
		this.onClose(this);
	}
};

/** Removes the AjaxCategories object from the DOM tree and destroys it. */
AjaxCategories.prototype.destroy = function () {
	var el = this.element.parentNode;
	el.removeChild(this.element);
	window._dynarch_popupCategory = null;
};

AjaxCategories.getElement = function(ev) { 
    var f = AjaxCategories.is_ie ? window.event.srcElement : ev.currentTarget;
    while (f.nodeType != 1 || /^div$/i.test(f.tagName))
        f = f.parentNode;
    return f;
};  
    
AjaxCategories.getTargetElement = function(ev) {
    var f = AjaxCategories.is_ie ? window.event.srcElement : ev.target;
    while (f.nodeType != 1)
        f = f.parentNode;
    return f;
};  

AjaxCategories._checkCategories = function(ev) {
    var __cat = window._dynarch_popupCategory;
    if (!__cat) return false;

    var el = AjaxCategories.is_ie ? AjaxCategories.getElement(ev) : AjaxCategories.getTargetElement(ev);
    for (; el != null && el != __cat.element; el = el.parentNode);
    if (el == null) {
        window._dynarch_popupCategory.callCloseHandler();
        return AjaxCategories.stopEvent(ev);
    }
};

AjaxCategories.stopEvent = function(ev) {
    ev || (ev = window.event);
    if (AjaxCategories.is_ie) {
        ev.cancelBubble = true;
        ev.returnValue = false;
    } else {
        ev.preventDefault();
        ev.stopPropagation();
    }
    return false;
};

AjaxCategories.removeEvent = function(el, evname, func) {
    if (el.detachEvent) { // IE
        el.detachEvent("on" + evname, func);
    } else if (el.removeEventListener) { // Gecko / W3C
        el.removeEventListener(evname, func, true);
    } else {
        el["on" + evname] = null;
    }
};

/** Shows the AjaxCategories. */
AjaxCategories.prototype.show = function () {
	this.element.style.display = "block";
	this.hidden = false;
    AjaxCategories.addEvent(document, 'mousedown', AjaxCategories._checkCategories);
};

/**
 *  Hides the AjaxCategories.  Also removes any "hilite" from the class of any TD
 *  element.
 */
AjaxCategories.prototype.hide = function () {
	this.element.style.display = "none";
	this.hidden = true;
    AjaxCategories.removeEvent(document, 'mousedown', AjaxCategories._checkCategories);
};

/**
 *  Shows the AjaxCategories at a given absolute position (beware that, depending on
 *  the AjaxCategories element style -- position property -- this might be relative
 *  to the parent's containing rectangle).
 */
AjaxCategories.prototype.showAt = function (x, y) {
	var s = this.element.style;
	s.left = x + "px";
	s.top = y + "px";
	this.show();
};

/** Shows the AjaxCategories near a given element. */
AjaxCategories.prototype.showAtElement = function (el, opts) {
	var self = this;
	var p = AjaxCategories.getAbsolutePos(el);
	if (!opts || typeof opts != "string") {
		this.showAt(p.x, p.y + el.offsetHeight);
		return true;
	}
	function fixPosition(box) {
		if (box.x < 0)
			box.x = 0;
		if (box.y < 0)
			box.y = 0;
		var cp = document.createElement("div");
		var s = cp.style;
		s.position = "absolute";
		s.right = s.bottom = s.width = s.height = "0px";
		document.body.appendChild(cp);
		var br = AjaxCategories.getAbsolutePos(cp);
		document.body.removeChild(cp);
		if (AjaxCategories.is_ie) {
			br.y += document.body.scrollTop;
			br.x += document.body.scrollLeft;
		} else {
			br.y += window.scrollY;
			br.x += window.scrollX;
		}
		var tmp = box.x + box.width - br.x;
		if (tmp > 0) box.x -= tmp;
		tmp = box.y + box.height - br.y;
		if (tmp > 0) box.y -= tmp;
	};
	this.element.style.display = "block";
	AjaxCategories.continuation_for_the_khtml_browser = function() {
		var w = self.element.offsetWidth;
		var h = self.element.offsetHeight;
		self.element.style.display = "none";
		var valign = opts.substr(0, 1);
		var halign = "l";
		if (opts.length > 1) {
			halign = opts.substr(1, 1);
		}
		// vertical alignment
		switch (valign) {
		    case "T": p.y -= h; break;
		    case "B": p.y += el.offsetHeight; break;
		    case "C": p.y += (el.offsetHeight - h) / 2; break;
		    case "t": p.y += el.offsetHeight - h; break;
		    case "b": break; // already there
		}
		// horizontal alignment
		switch (halign) {
		    case "L": p.x -= w; break;
		    case "R": p.x += el.offsetWidth; break;
		    case "C": p.x += (el.offsetWidth - w) / 2; break;
		    case "l": p.x += el.offsetWidth - w; break;
		    case "r": break; // already there
		}
		p.width = w;
		p.height = h + 40;
		fixPosition(p);
		self.showAt(p.x, p.y);
	};
	if (AjaxCategories.is_khtml)
		setTimeout("AjaxCategories.continuation_for_the_khtml_browser()", 10);
	else
		AjaxCategories.continuation_for_the_khtml_browser();
};

// global object that remembers the AjaxCategories
window._dynarch_popupCategory = null;
