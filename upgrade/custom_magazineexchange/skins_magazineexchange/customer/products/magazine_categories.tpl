
<div class="subcategories">
<p class="subcategory-heading"><span>{$lng.lbl_magazine_titles}</span></p>
<div class="categories_list">
<div style="padding: 0 7px 10px 7px; font-size: 12px;">{$lng.lbl_subcategories_instruction}</div>

<div class="sub_box">
  <div class="nav_categories">

    {tunnel func='magexch_get_prev_next_category_ids' via='cw_call' param1=$current_category.category_id assign='prev_next'}
    <a class="navLeft{if $prev_next.prev.url eq ''} navDisabled{/if}" {if $prev_next.prev.url ne ''}href="{$prev_next.prev.url}"{/if}>{$lng.lbl_previous}</a>
    <div class="title_mag">{$current_category.category} {$lng.lbl_magazines}:</div>
    <a class="navRight{if $prev_next.next.url eq ''} navDisabled{/if}" {if $prev_next.next.url ne ''}href="{$prev_next.next.url}"{/if}>{$lng.lbl_next}</a>
    <div class="clear"></div>
  </div>

{if $vendorid}
{tunnel func='magexch_filter_categories_by_vendor' via='cw_call' param1=$subcategories param2=$vendorid assign='subcategories'}
{/if}

  {foreach from=$subcategories item=subcat}
    {tunnel func='magexch_get_attribute_value' via='cw_call' param1='C' param2=$subcat.category_id param3='magexch_category_rollover_image' assign='current_magexch_category_rollover_image'}
    <div class="magazine-category" id="mc_{$subcat.category_id}" onmouseover="document.getElementById('mc_{$subcat.category_id}').style.zIndex= 500+0; " style="z-index: 400;" onmouseout="this.style.zIndex=400+0;" onmouseleave="this.style.zIndex=400+0;" onmouseenter="document.getElementById('mc_{$subcat.category_id}').style.zIndex= 500+0; ">
      <div class="magazine-category1" onclick="self.location='{pages_url var="index" cat=$subcat.category_id}';">
        <div class="magazine-category-table">
          <div class="magazine-category2">
          <div style="#position: absolute; #top: 50%;">
          <div style="#position: relative; #top: -50%; #left: -50%;">
            {*include file='common/product_image.tpl' image=$subcat.image html_width=149 html_height=45*}
            <img src="{$subcat.image.image_path}" alt="" />
          </div>
          </div>
          </div>
        </div>
        <div class="magazine-title">{$subcat.category}</div>
        <div class="magazine-descr">{$subcat.description|strip_tags}</div>
        <div class="magazine-rollover"><img src="{$current_magexch_category_rollover_image}" alt=""></div>
      </div>
    </div>
  {/foreach}
  <div class="clear"></div>
</div>

</div>
</div>
