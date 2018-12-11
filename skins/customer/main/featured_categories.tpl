{if $featured_categories}
{foreach from=$featured_categories item=category}
<div class="featured_category">
    
    <figure>
      <div class="f_image"><a href="{pages_url var='index' cat=$category.category_id}">{include file='common/product_image.tpl' image=$category.image width=$config.Appearance.featured_categories_images_thumb_width|default:125 height=$config.Appearance.featured_categories_images_thumb_height|default:95 no_img_id='Y' category_id=$category.category_id}</a></div>
    
      <figcaption class="cat_name"><a href="{pages_url var='index' cat=$category.category_id}">{$category.category}<i class="icon-caret-right"></i></a></figcaption>
    </figure>
</div>

{/foreach}

{/if}
