{include_once_src file='main/include_js.tpl' src='js/multirow.js'}
{include_once_src file='main/include_js.tpl' src='categories_ajax/categories.js'}
{include_once_src file='main/include_js.tpl' src='categories_ajax/choose_category_ajax.js'}

<script type="text/javascript">
var lbl_categories = '{$lng.lbl_categories}';
{literal}
function cw_parse_category(str) {
    cat_id = explode('_', str);
    index = cat_id.pop();
    cat_id.pop();
    return Array(index, cat_id.implode('_'));
}

function cw_show_categories(cat_id, id, index) {
    add = '';
    if (isset(index) && index != '') add = '_'+index;

    document.getElementById(id+'_show_category').style.display = '';
    $('#'+id+'_show_category').dialog({'title': lbl_categories});
    cw_ajax_categories(id, id+'_body', 0, cat_id, index);
}

function cw_select_categories(id, cat_id, category, index) {
    add = '';
    if (isset(index) && index != '') add = '_'+index;
    
    document.getElementById(id+'_catname'+add).value = category;
    document.getElementById(id+'_catid'+add).value = cat_id;
    $('#'+id+'_show_category').dialog('close');
}
{/literal}
</script>
