var pf_is_use_ajax = new String;
var pf_range_values = new Array();
var pf_range_data = new Array();
var pf_range_ids = new Array();
var navigation_script = new String;

if ($('#replaced_url').length) {
window.onpopstate = function(event) {
	var is_chrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase());
	var is_safari = navigator.userAgent.indexOf("Safari") > -1;
	if (
		(event.state && (is_chrome || is_safari))
		|| (!is_chrome && !is_safari)
	) {	// chrome and safari fires onpopstate on page load
		document.location.reload();
	}
};
}

function cw_pf_add_filter(nav, att_id) {
    nav = append_url(nav, 'att['+att_id+'][min]='+$('#att_min_'+att_id).val() + '&att['+att_id+'][max]='+$('#att_max_'+att_id).val());
    cw_pf_load(nav);
}

function cw_pf_add_substring(nav) {
    nav = append_url(nav,'att[substring]='+$('#att_substring').val());
    cw_pf_load(nav);
}

function cw_pf_load(url) {
	
    if (pf_is_use_ajax == 'Y') {
        blockElements('product_list', true);
        url = append_url(url, 'ajax_filter=1');
        $.get(url, function(data) {
// kornev, load the DOM and use only the required attributes
            $('body').html(data);
			if ($('#replaced_url').length) {
				window.history.pushState({state: true}, "", $('#replaced_url').val());
			} else {
				window.location.hash = "";
			}
            return;
/* kornev, partial replace is not success because the js code
        var el = $(data);
        $('#product_filter').html(el.find('#product_filter').html());
        $('.main-center').html(el.find('.main-center').html());
        cw_pf_onload();
*/
        }, 'html');
    }
    else 
        window.location.href = url;
}

function cw_pf_onload() {
    for (i in pf_range_values) {
        $("#pf_slider_"+i).slider({
            range: true,
            min: 0,
// pf_range_data[i][0],
            max: pf_range_data[i][0],
            values: [pf_range_data[i][1], pf_range_data[i][2]],
            slide: function( event, ui ) {
                var i = explode('_', $(this).attr('id')).pop();
                $("#att_min_"+i).val(pf_range_ids[i][ui.values[0]]);
                $("#att_max_"+i).val(pf_range_ids[i][ui.values[1]]);
                if ($('#att_val_'+i+'_min').length) {
                    // Min and max containers exist and should be updated separately
                    $('#att_val_'+i+'_min').html(pf_range_values[i][ui.values[0]]);
                    $('#att_val_'+i+'_max').html(pf_range_values[i][ui.values[1]]);
                } else {
                    // Values range stored in one container
                    $("#att_val_"+i).html(pf_range_values[i][ui.values[0]] + ' - ' + pf_range_values[i][ui.values[1]]);
                }
            },
            change: function(event, ui) {    
                var i = explode('_', $(this).attr('id')).pop();
                cw_pf_add_filter(navigation_script, i);
            }
        });
    }
}

