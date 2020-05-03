{tunnel func='magexch_get_section_category_id' via='cw_call' param1=$product.category_id|default:$current_category.category_id assign='current_section_category_id'}

{*
{if $main eq "subcategories" && $current_category.category_id eq "7589"}
<div>hello{$category_magexch_attributes.magexch_category_main_image}{$category_magexch_attributes.magexch_category_main_image}{$magexch_category_section_image}</div>
{/if}

{elseif $main eq "subcategories" || $main eq "product" }
<div class="CategoryTop incomplete" style="background: url(http://www.magazineexchange.co.uk//skin1/images/Section_Headers/Automotive_Section_Header.png) top left no-repeat;">
  <div class="PageAvatar">
    <img src="http://www.magazineexchange.co.uk//skin1/images/Subsection_Page_Avatar_Yellow.gif" width="200" height="84" ;="" alt="New Cars / Road Tests">
  </div>
</div>
*}
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_avatar_image' assign='magexch_product_avatar_image'}
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_use_parent_avatar' assign='magexch_product_use_parent_avatar'}
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='C' param2=$current_section_category_id param3='magexch_category_section_image' assign='magexch_category_section_image'}

{if $main eq "subcategories" || $main eq "product"}
    {if $vendorid}
        <div class="CategoryTop" style="background: url('{$AltImagesDir}/Seller_Shops_Header.gif') top left no-repeat;"></div>
    {else}
        {if $category_magexch_attributes.magexch_category_type eq "Section"}
            {cms service_code="new_category_body"}
        {else}	
            <div class="CategoryTop" style="background: url({if $category_magexch_attributes.magexch_category_main_image}{$category_magexch_attributes.magexch_category_main_image}{elseif $magexch_category_section_image}{$magexch_category_section_image}{else}{$AltImagesDir}/category_bg.jpg{/if}) top left no-repeat;{if $category_magexch_attributes.magexch_category_type eq "Section"}height:182px;{/if}" >
                {if $category_magexch_attributes.magexch_category_avatar_image || $magexch_product_avatar_image}
                    <div class="PageAvatar">
                      <img {if  $magexch_product_avatar_image && $magexch_product_use_parent_avatar neq 'Y' }src="{$magexch_product_avatar_image}" width="190" height="78";  alt="{$product.product}" {elseif $category_magexch_attributes.magexch_category_avatar_image}src="{$category_magexch_attributes.magexch_category_avatar_image}" width="200" height="84"; alt="{$current_category.category}" {/if}>
                    </div>
                {/if}
                <div class="CategoryTopBanner">
                  {cms service_code="category_468x60"}
                </div>
           </div>
        {/if}
    {/if}
{elseif $main eq "pages"}
  <img src="{$AltImagesDir}/Header_Image_1.png" width="950" height="84">
{elseif $main eq "help"}
  <img src="{$AltImagesDir}/Header_Image_1.png" width="950" height="84">
{elseif $main eq "help" && $smarty.get.section eq "login_customer" || $main eq "acc_manager"}
  <img src="{$AltImagesDir}/Header_Image_2.png" width="950" height="84">
{elseif $main eq "error_message"}
  <img src="{$AltImagesDir}/Header_Image_1.png" width="950" height="84">
{elseif $main eq "cart" || $main eq "index" || $main eq "order_message"}
  <img src="{$AltImagesDir}/Header_Image_3.png" width="950" height="84">
{elseif $main eq "classifieds"}
  <img src="{$AltImagesDir}/Classifieds_Header.png" width="950" height="84">
{elseif in_array($main, array('orders', 'profile', 'document', 'message_box'))}
  <img src="{$AltImagesDir}/Header_Image_6.png" width="950" height="84">
{elseif $main eq "search"}
  <img src="{$AltImagesDir}/Header_Image_5.png" width="950" height="84">
{elseif $main eq "welcome"}
  {cms service_code='homepage_topbox'}
{else}
  {cms service_code='homepage_topbox'}</td>
{/if}
