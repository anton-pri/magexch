var current_id = 0;
var imgObj;
var prow;

function changeImg(flag) {
	if (!imgObj)
		imgObj = document.getElementById('img');
	if (!imgObj)
		return false;

	if (flag < 0) {
		return false;
	} else if (flag > images.length-1) {
		if (current_id == flag)
			return false;
		flag = images.length-1;
	}

	current_id = flag;
	if (!images[current_id])
		return false;

	if (!images[current_id][3]) {
		images[current_id][0].src = images[current_id][2];
		images[current_id][3] = true;
	}
	if (current_id+1 <= images.length-1 && !images[current_id+1][3]) {
		images[current_id+1][0].src = images[current_id+1][2];
		images[current_id+1][3] = true;
	}
	if (current_id-1 >= 0 && !images[current_id-1][3]) {
		images[current_id-1][0].src = images[current_id-1][2];
		images[current_id-1][3] = true;
	}

	if (!prow)
		prow = document.getElementById('prow');
	if (!prow)
		return false;

/*
	Create pages row
*/
	prow.innerHTML = '';
	var sPage = 0;
	var ePage = images.length;
	var rpagesCount = 0;
	var rpagesCurrent = 0;
	if (max_nav_pages > 0) {
		rpagesCount = Math.ceil((images.length)/max_nav_pages);
		rpagesCurrent = Math.ceil((current_id+1)/max_nav_pages);
	}
	if (rpagesCount > 1) {
		sPage = (rpagesCurrent-1)*max_nav_pages;
		ePage = (rpagesCurrent == rpagesCount) ? images.length : sPage+max_nav_pages;
		if (document.getElementById('larr2'))
			document.getElementById('larr2').src = (rpagesCurrent == 1) ? larrow2_grey.src : larrow2.src;
		if (document.getElementById('rarr2'))
			document.getElementById('rarr2').src = (rpagesCurrent == rpagesCount) ? rarrow2_grey.src : rarrow2.src;
	}

	if (document.getElementById('larr'))
		document.getElementById('larr').src = (current_id == 0) ? larrow_grey.src : larrow.src;

	if (document.getElementById('rarr'))
		document.getElementById('rarr').src = (current_id == images.length-1) ? rarrow_grey.src : rarrow.src;

	var tbl = prow.appendChild(document.createElement("TABLE"));
	var r = tbl.insertRow(-1);
	for (var i = sPage; i < ePage; i++) {
		var t = r.insertCell(-1);
		if (current_id == i) {
			t.className = 'NavigationCellSel';
			t.innerHTML = i+1;
		} else {
			t.className = (i > 100) ? 'NavigationCellWide' : 'NavigationCell';
			var x = t.appendChild(document.createElement("A"));
			x.title = lbl_page+" #"+(i+1);
			x.href = "javascript: void(0);";
			x.onclick = new Function("", "changeImg("+i+");");
			x.innerHTML = i+1;
			var x = t.appendChild(document.createElement("IMG"));
			x.src = spc.src;
		}
	}

	imgObj.onload = imgOnLoad;
	imgObj.alt = '';
	imgObj.src = images[current_id][0].src;
}

function imgOnLoad(obj) {
	if (images.length == 0 && obj)
		imgObj = obj;

	var _w = 25;
	var _h = 75;
	var diff_w = 0;
	var diff_h = 0;

	if (localBFamily == "MSIE") {
		diff_w = 28;
		diff_h = 51;
		_w += 45;
		_h += (added_h == 0) ? 0 : 20;

	} else if (localBFamily == "Opera") {
		diff_w = 10;
		diff_h = (localPlatform == 'Win32' || localPlatform.search(/^Windows/) != -1) ? 42 : 41;
		_h += (added_h == 0) ? 20 : 10;

	} else if (localBFamily == "NC") {
		if (localBrowser == "Firefox") {
			diff_w = 8;
			diff_h = 51;
			_w = 29;
			_h = 105;
			if (added_h > 0)
				added_h = 25;

		} else if (localBrowser == "Netscape") {
			diff_w = 10;
			diff_h = 30;
			_w -= 10;
			if (added_h > 0)
				added_h = 25;

		} else {
			diff_h = 18;
			_w -= 10;
			if (added_h > 0)
				added_h = 25;
		}
	}

	_w += imgObj.width;
	_h += imgObj.height+added_h;

	if (getWindowWidth()+diff_w < _w) {
		if (screen.width <= _w)
			_w = screen.width;
		else if (screen.height < _h)
			_w += 20;

		window.resizeTo(_w, getWindowHeight()+diff_h);
	}

	if (getWindowHeight()+diff_h < _h) {
		if (screen.height <= _h)
			_h = screen.height;
		else if (screen.width < _w)
			_h += 20;

		window.resizeTo(getWindowWidth()+diff_w, _h);
	}

	if (images.length == 0)
		return true;

	imgObj.alt = images[current_id][1];

}
