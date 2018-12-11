

<footer>
 {if $speed_bar}
  <div class="footer_bar">
    <ul>
    {select_categories category_id=0 current_category_id=$cat assign='categories'}
    {foreach from=$speed_bar item=sb}
      <li>
        <a href="{eval var=$sb.link}"{if $smarty.foreach.speed_bar.last} id="last"{/if}>{$sb.title} {$lng.lbl_magazines}</a>
      </li>
    {/foreach}
    </ul>
  </div>
  {/if}

<div class="bottom_links">
<nav class="line960">
  <div class="footer_block">
    <h4>{$lng.lbl_about_us}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/help-centre-about-us-who-we-are.html">{$lng.lbl_who_we_are}</a> </li>
    </ul>
  </div>

  <div class="footer_block">
    <h4>{$lng.lbl_buying_magazines}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/search-for-magazine-articles.html">{$lng.lbl_find_magazines_articles}</a></li>
      <li><a href="{$catalogs.customer}/help-centre-buying-back-issues-1.html">{$lng.lbl_bying_back_issues}</a></li>
      <li><a href="{$catalogs.customer}/help-centre-buying-digital-editions.html">{$lng.lbl_buying_digital_issues}</a></li>
      <li><a href="{$catalogs.customer}/help-centre-buying-subscriptions.html">{$lng.lbl_bying_subscriptions}</a></li>
      <li><a href="{$catalogs.customer}/Delivery-Rates_Basics.html">{$lng.lbl_delivery_rates_info}</a></li>
    </ul>
  </div>

  <div class="footer_block">
    <h4>{$lng.lbl_selling_magazines}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/start-selling.html">{$lng.lbl_new_seller_overview}</a></li>
      <li><a href="{$catalogs.customer}/seller-account-registration.html">{$lng.lbl_new_seller_registration}</a></li>
      <li><a href="{$catalogs.admin}/index.php">{$lng.lbl_existing_seller_login}</a></li>
            <li><a href="{$catalogs.customer}/contribute-content-introduction.html">{$lng.lbl_contribute_content}</a> </li>
    </ul>
  </div>

  <div class="footer_block">
    <h4>{$lng.lbl_publishers}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/help-centre-selling-trade-services.html">{$lng.lbl_trade_services}<br><br></a> </li>
<li></li>
    </ul>
<h4>{$lng.lbl_advertising}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/advertising-guide-intro.html">{$lng.lbl_advertising_overview}</a></li>
    </ul>
  </div>
  <div class="footer_block contacts_block">
    <ul class="toggle-footer">
      <li><h4>{$lng.lbl_contact_us}</h4></li>

      <li>{$config.Company.company_name}</li>
      <li>
        {if $config.Company.address ne ""}{$config.Company.address}, {/if}<br />
        {if $config.Company.city ne ""}{$config.Company.city}, {/if}<br />
        {if $config.Company.state ne ""}{$config.Company.zipcode}{/if}
      </li>
    </ul>
  </div>
  <div class="footer_block">
    <ul class="toggle-footer">
      <li>&nbsp;</li>
      <li>&nbsp;</li>
      <li>{if $config.Company.company_phone ne ""}{$lng.lbl_phone}: <span> {$config.Company.company_phone}</span>{/if}</li>
      <li>{if $config.Company.users_department ne ""}{$lng.lbl_email}: <span><a href="mailto:{$config.Company.users_department}">{$config.Company.users_department}</a></span>{/if}</li>
    </ul>
  </div>   
</nav>

<div style="margin: 5px auto; max-width: 950px; width: 100%; height: 1px; border-top:1px solid #515151;"></div>

<div style="max-width: 950px; width: 100%; margin: 0 auto;padding-bottom: 3px;overflow: hidden;">
  {cms service_code="fb"} {cms service_code="twitter"} {cms service_code="mobile"} {cms service_code="blog"}
  <div class="delivering">Now delivering to over 40 countries!</div>      
  <div class="switch_version">{include file="addons/mobile/bottom_links.tpl"}</div>

  <div class="float-right"><img src="{$AltImagesDir}/cards.png" width="255" height="32" style="display: block;"></div>
</div>

</div>

<div id="footer_container">
<div id="footer">
<div class="bottom_line">{include file='elements/custom_copyright.tpl'}</div>


<div class="clear"></div>


{if $addons.now_online && $home_style ne 'popup' && $users_online}
{include file='addons/now_online/menu_users_online.tpl'}
{/if}
</div>
</div>
</footer>
