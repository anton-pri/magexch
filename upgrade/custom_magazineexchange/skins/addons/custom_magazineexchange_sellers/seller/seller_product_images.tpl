{include_once_src file='main/include_js.tpl' src='main/popup_image_selection.js'}
<script language="javascript">
    var tmp_update_timeout = null;

    function seller_product_image_delete_{$in_type}(imgid, is_permanent) {ldelim}
        const p_id = "{$product_id|default:0}";
        const in_type = "{$in_type}";
        console.log(imgid, is_permanent);
        if (confirm("Please confirm you want to delete this image. \nThis will permanently delete the image from current product")) {ldelim}
            ajaxGet("index.php?target=seller_product_images&in_type=" + in_type + "&to_delete=" + imgid + "&is_permanent=" + is_permanent + "&product_id=" + p_id, 'seller_product_images');
        {rdelim}    
    {rdelim}

    $(document).ready(function() {ldelim}
        const p_id = "{$product_id|default:0}";
        const in_type = "{$in_type}";
        ajaxGet("index.php?target=seller_product_images&in_type=" + in_type + "&product_id=" + p_id, 'seller_product_images_' + in_type);
        $('body').on('DOMSubtreeModified', '#seller_product_images_' + in_type + '_tmp', function(e){ldelim}
            if (tmp_update_timeout)
                clearTimeout(tmp_update_timeout);
            tmp_update_timeout = setTimeout(function() {ldelim}
                ajaxGet("index.php?target=seller_product_images&in_type=" + in_type + "&product_id=" + p_id, 'seller_product_images_' + in_type);
            {rdelim}, 500);
        {rdelim});
    {rdelim});
</script>
<div id="seller_product_images_{$in_type}" class="seller_product_images">
</div>

<div id="seller_product_images_{$in_type}_tmp" style="display:none;">
</div>
