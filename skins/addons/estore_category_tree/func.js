function handler_categories(data) {
    parent_cat_id = data.parent_category_id;
    el = $('#categories_tree_subcat_'+parent_cat_id);
    if (el) el.html(data.categories_categories_tree);
}

function cw_categories_tree_ajax_categories(cat_id) {
    $.ajax({ 
    "url":current_location+"/index.php?target=ajax&mode=categories&cat="+cat_id,
    "success":handler_categories, 
    "dataType":"json",
    "type":"post"
    });
}

function cw_categories_tree_show_subcategories(cat_id) {
    el_main_img = document.getElementById('categories_tree_'+cat_id+'_img');
    el = document.getElementById('categories_tree_subcat_'+cat_id);
    if (!el.innerHTML) {
        cw_categories_tree_ajax_categories(cat_id);
        el_main_img.className = 'minus';
    }
    else {
        if (el.style.display == '') {
            el.style.display = 'none';
            el_main_img.className = 'plus';
        }
        else {
            el.style.display = '';
            el_main_img.className = 'minus';
        }
    }
}
