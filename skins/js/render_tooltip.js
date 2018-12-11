var _tiktak, _tooltip_obj;
function clearTiktak() {
	clearTimeout(_tiktak);
	_tiktak = null;
	var _id = $(_tooltip_obj).data('index');

	if ($("#slideout_tooltip_" + _id).data('hover')) {
		$("#slideout_tooltip_" + _id).mouseleave(function () {
			if ($("#slideout_tooltip_" + _id).css('display') == 'block') {
				$("#slideout_tooltip_" + _id).hide(500);
			}
		});
	} else if (!$(_tooltip_obj).data('hover')) {
		if ($("#slideout_tooltip_" + _id).css('display') == 'block') {
			$("#slideout_tooltip_" + _id).hide(500);
		}
	}
	_tooltip_obj = null;
}


function cw_init_lng_tooltip() {
    $('.lng_tooltip').each(function(index) {

        // if not in select box
        if (!$(this).closest('option').length && !$(this).closest('title').length) {
            // if in link tag
            if ($(this).closest('a').length) {
                $(this).closest('a').css('text-decoration', 'none');

                var content = $(this).attr("title");
                $(this).attr("title", null);
				$(this).data('index', index);
                $('body').append('<div style="display: none;" class="slideout_tooltip" id="slideout_tooltip_' + index + '">' + content + '</div>');

                var text = ' <span class="text_tooltip">' + $(this).text() + '</span> <span class="questions_tooltip">?</span>';
                $(this).html(text);

                $(this).mouseenter(function () {
					var _id = $(this).data('index');
                    if ($("#slideout_tooltip_" + _id).css('display') == 'none') {
						_tiktak = setTimeout("clearTiktak()", 300);
						_tooltip_obj = this;
                        $("#slideout_tooltip_" + _id).show(500);
                    }
                }).mouseleave(function () {
					if (_tiktak == null) {
						var _id = $(this).data('index');
						if ($("#slideout_tooltip_" + _id).css('display') == 'block') {
							$("#slideout_tooltip_" + _id).hide(500);
						}
                    }
                }).hover(function(){
					$(this).data('hover', 1); //store in that element that the mouse is over it
				},
				function(){
					$(this).data('hover', 0); //store in that element that the mouse is no longer over it
				});

				$("#slideout_tooltip_" + index).hover(function(){
					$(this).data('hover', 1); //store in that element that the mouse is over it
				},
				function(){
					$(this).data('hover', 0); //store in that element that the mouse is no longer over it
				});
            }
            else {  // simple tooltip
                $(this).tooltip({
                    'onCreate': function(ele, options) {
                        options.openTrigger = 'hover';
                        options.closeTrigger = 'hover';
                        options.content = $(ele).attr("title");
                        $(ele).attr("title", null);
                        var text = ' <span class="text_tooltip">' + $(ele).html() + '</span> <span class="questions_tooltip">?</span>';
                        $(ele).html(text);
                    }
                });
            }
        }
        else {  // clear tags
            var text = $(this).text();
            $(this).replaceWith(text);
        }
    });
}

$(document).ready(function() {
    cw_init_lng_tooltip();
});
