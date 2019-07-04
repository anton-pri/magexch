{assign var=width value=250}

{include file="common/image_alt.tpl" item_id=$product_id|default:$category_id image=$image image_type=$image_type alt=$alt assign='alt'}
{if $product.variants.image_thumb}
    {if $no_img_id ne 'Y'}{assign var="_id" value="product_thumbnail_`$product_id`"}{/if}
    {product_image image=$product.variants.image_thumb image_type=$image_type product_id=$product_id class=$class id=$_id width=$width html_width=$html_width height=$height html_height=$html_height override_keep_file_h2w=$keep_file_h2w extra=$extra alt=$alt no_xcm_thumb_cache=$no_xcm_thumb_cache}
{else}
    {if $no_img_id ne 'Y'}{if $id ne ''}{assign var="_id" value=$id}{else}{assign var="_id" value="product_thumbnail_`$product_id`"}{/if}{/if}   
    {product_image image=$image image_type=$image_type product_id=$product_id class=$class id=$_id width=$width html_width=$html_width height=$height html_height=$html_height override_keep_file_h2w=$keep_file_h2w extra=$extra alt=$alt no_xcm_thumb_cache=$no_xcm_thumb_cache}
{/if}
