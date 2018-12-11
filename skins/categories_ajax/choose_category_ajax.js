function cw_ajax_categories(id, el_name, cat_id, current_category, index) {
    $.ajax({ 
    "url":current_location+"/index.php?target=ajax&mode=categories&id="+id+"&return_type=html&cat="+cat_id+"&current_category="+current_category+"&index="+index,
    "success": function(data) {
        $('#'+el_name).html(data);
    }, 
    'dataType':'html',
    "type":"post"
    });
}

function cw_ajax_show_subcategories(id, el_name, cat_id, current_category, plus, index) {
    el = document.getElementById(el_name);
    if (!el.innerHTML) {
        cw_ajax_categories(id, el_name, cat_id, current_category, index);
        plus.src = images_dir+'/minus.gif';
    }
    else {
        if (el.style.display == '') {
            el.style.display = 'none';
            plus.src = images_dir+'/plus.gif';
        }
        else {
            el.style.display = '';
            plus.src = images_dir+'/minus.gif';
        }
    }
}
