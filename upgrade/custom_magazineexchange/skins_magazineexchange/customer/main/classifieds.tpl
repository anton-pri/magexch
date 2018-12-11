{*<aside class="main-left">

<div class="menu categories">
    <img src="{$AltImagesDir}/Classifieds_menu_header.gif" alt="">
    <div class="content" style="margin-top: 0;">
    <ul>
      <li>
        <a href="http://sidesell.co.uk/wp/">{$lng.lbl_browse_all_adverts}</a>
      </li>
      <li>
        <a href="http://sidesell.co.uk/wp/">{$lng.lbl_search_adverts}</a>
      </li>
      <li>
        <a href="http://sidesell.co.uk/wp/">{$lng.lbl_place_new_advert}</a>
      </li>
    </ul>
    </div>

</div>


<div id="belowsidebarmenu_widgets">
    <div class="i123_widget">
	    <div class="widget_header">{$lng.lbl_classifieds_help}</div>
		<div class="textwidget">
			<a href="/wp/what-the-classifieds-section-is-for/"><div style="font-weight: normal; font-size: 12px; line-height: 4px; font-family: Verdana,Arial,Helvetica,Sans-serif; color: #000000;">{$lng.lbl_what_section_is_for}</div></a>
			<br>
			<a href="/wp/buying-items-with-classifieds/"><div style="font-weight: normal; font-size: 12px; line-height: 4px; font-family: Verdana,Arial,Helvetica,Sans-serif; color: #000000;">{$lng.lbl_buying_items_with_classifieds}</div></a>
			<br>
			<a href="/wp/?page_id=32"><div style="font-weight: normal; font-size: 12px; line-height: 4px; font-family: Verdana,Arial,Helvetica,Sans-serif; color: #000000;">{$lng.lbl_selling_items_with_classifieds}</div></a>
			<br>
			<a href="/wp/collection-delivery-assistance/"><div style="font-weight: normal; font-size: 12px; line-height: 4px; font-family: Verdana,Arial,Helvetica,Sans-serif; color: #000000;">{$lng.lbl_heavy_item}</div></a>
			<br>
			<a href="/wp/placing-a-wanted-advert/"><div style="font-weight: normal; font-size: 12px; line-height: 4px; font-family: Verdana,Arial,Helvetica,Sans-serif; color: #000000;">{$lng.lbl_placing_a_wanted_advert}</div></a>
		</div>
	</div>
	
	<div class="i123_widget">
		<div class="widget_header">{$lng.lbl_featured_classifieds}</div>
		<ul class="awpcp-listings-widget-items-list"><li class="awpcp-empty-widget featured_ad_item">{$lng.lbl_no_ads_to_show}</li></ul>
	</div>
</div>

</aside>

<div class="main-center">
<div class="classifieds_tab">
{jstabs name='product_data_customer'}
default_tab={$js_tab|default:"1"}
default_template="customer/main/classifieds_tabs.tpl"

[1]
title="{$lng.lbl_browse_all_adverts}"

[2]
title="{$lng.lbl_search_ads_by_keyword}"

[3]
title="{$lng.lbl_place_new_advert}"


{/jstabs}
{include file='tabs/js_tabs.tpl'}
</div>
</div>
*}

<iframe width="100%" scrolling="no" height="5000" frameborder="0" id='frame_name_here' src='http://www.sidesell.co.uk/wp/'></iframe>
