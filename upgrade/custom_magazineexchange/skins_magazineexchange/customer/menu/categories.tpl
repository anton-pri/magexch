{if $main ne "subcategories" || $cat eq $config.custom_magazineexchange.magexch_default_root_category}
  {select_categories category_id=$config.custom_magazineexchange.magexch_default_root_category|default:0 current_category_id=$cat assign='categories' images="Y"}
{else}
  {tunnel func='magexch_get_section_category_id' via='cw_call' param1=$cat assign='section_category_id'}
{assign var='magexch_get_subcategories_flag' value='current'}
  {select_categories category_id=$section_category_id current_category_id=0 assign='categories' images="Y"}
{assign var='magexch_get_subcategories_flag' value=''}
{/if}

{if $vendorid}
{tunnel func='magexch_filter_categories_by_vendor' via='cw_call' param1=$categories param2=$vendorid assign='categories'}
{/if}

{if $categories}
  {capture name=menu}
    {if $addons.estore_category_tree}
      {include file='addons/estore_category_tree/categories.tpl'}
    {else}
    <ul>
      {foreach from=$categories item=c}
        {tunnel func='magexch_get_attribute_value' via='cw_call' param1='C' param2=$c.category_id param3='magexch_popup_category_image' assign='current_magexch_popup_category_image'}
        <li>
          <a href="{pages_url var='index' cat=$c.category_id}">{$c.category}<div class="img"><img src="{$current_magexch_popup_category_image}" alt="" /></div></a>
        </li>
      {/foreach}
<!--{if !$vendorid}
      <li class="classifieds"><a href="Classifieds.html">{$lng.lbl_classifieds}</a></li>-->
{/if}
    </ul>
   {/if}
  {/capture}

  {if $main ne "subcategories"}
    {include file='common/menu.tpl' title=$lng.lbl_browse_main_sections content=$smarty.capture.menu style='categories'}
  {else}
    {if $vendorid}
        {include file='common/menu.tpl' title="magazines from this seller" content=$smarty.capture.menu style='categories'}
    {else}  
        {include file='common/menu.tpl' title=$lng.lbl_magazines_in_this_section content=$smarty.capture.menu style='categories'}
    {/if}
  {/if}
{/if}

{if $main eq "subcategories"}{cms service_code='non_homepage_left_banner'}
{else}{if $main eq "search"}{cms service_code='search_left_banner'}
{else}{cms service_code='homepage_left_banner'}
{/if}
{/if}
{if $vendorid eq 4010}{cms service_code='shopfront_PP_Link_Keyseller'}{/if}
{*
{cms service_code="sell_magazines"}
{cms service_code="payment_cards"}
{cms service_code="testimonials"} 
*}
