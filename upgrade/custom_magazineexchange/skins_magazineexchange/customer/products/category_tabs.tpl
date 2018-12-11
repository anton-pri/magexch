{if $vendorid}
{assign var='tab2content' value=$category_magexch_attributes.magexch_category_tab_content_2}
{else}
{tunnel func='magexch_get_cms_by_tab_content_id' via='cw_call' param1=$category_magexch_attributes.magexch_category_tab_content_2 assign='tab2content'}
{tunnel func='magexch_get_cms_by_tab_content_id' via='cw_call' param1=$category_magexch_attributes.magexch_category_tab_content_3 assign='tab3content'}
{tunnel func='magexch_get_cms_by_tab_content_id' via='cw_call' param1=$category_magexch_attributes.magexch_category_tab_content_4 assign='tab4content'}
{/if}

{if $included_tab eq '1'}
{* start *}
  {if $subcategories && $vendorid eq ''}
  <div class="notes">{$lng.lbl_browse_issues}</div>
  <div class="sub_box year_category">

  <div class="MagazineCategory">{$current_category.category} {$lng.lbl_back_issues}</div>

  {if $vendorid}
    {tunnel func='magexch_filter_categories_by_vendor' via='cw_call' param1=$subcategories param2=$vendorid assign='subcategories'}
  {/if}

  {foreach from=$subcategories item=subcat}
        <div class="magazine_year"><a href="{pages_url var="index" cat=$subcat.category_id}" class="cat-button"><span class="cat-right"><span class="cat-left">{$subcat.category}</span></span></a></div>
  {/foreach}
  <br />
  </div>

    <div class="years_column">
                     <div style="padding: 0px 20px 0px 30px">
						{cms service_code='years_list'}<br>
			<a class="ProductBlue" href="/advertising-guide-intro.html">Advertise your business</a></div>
                     
                     
    </div>
    <div clss="clear"></div>
  {elseif $products}
  {tunnel func='cw_category_get' assign='parent_category' cat=$current_category.parent_id}
  <div style="padding: 0 7px 10px 7px; font-size: 12px;">{$lng.lbl_click_cover_images}</div>

    <div class="sub_box">

      <div class="nav_categories">
        {tunnel func='magexch_get_prev_next_category_ids' via='cw_call' param1=$current_category.category_id param2=$parent_category.category_id assign='prev_next'}
        <a class="navLeft{if $prev_next.prev.url eq ''} navDisabled{/if}" {if $prev_next.prev.url ne ''}href="{$prev_next.prev.url}"{/if}>{$lng.lbl_previous}</a>
        <div class="title_mag">{$parent_category.category} {$lng.lbl_back_issues} - {$current_category.category}:</div>
        <a class="navRight{if $prev_next.next.url eq ''} navDisabled{/if}" {if $prev_next.next.url ne ''}href="{$prev_next.next.url}"{/if}>{$lng.lbl_next}</a>
        
        <div class="clear"></div>
      </div>
      {include file="customer/products/`$product_list_template`.tpl" hidefc='Y'}
      {if $vendorid eq ''}
        {if $navigation.total_pages gt 2}<div class="nav_bottom">{include file='common/navigation_customer.tpl'}</div>{/if}
      {/if}

    </div>
  {/if}
{elseif $included_tab eq 2}
{* start *}
{$tab2content}

{elseif $included_tab eq 3}
{* start *}
{$tab3content}

{elseif $included_tab eq 4}
{* start *}
{$tab4content}
{/if}
