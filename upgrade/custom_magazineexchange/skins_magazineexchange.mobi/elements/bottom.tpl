<footer>

{if ($usertype eq "C" or $usertype eq "B") && !$home_style}
<div class="bottom_links">
<nav class="line960">
  <div class="footer_block" id="about">
    <h4>{$lng.lbl_about_us}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/help-centre-buying-searching-and-browsing.html">{$lng.lbl_who_we_are}</a> </li>
      <li><a href="{$catalogs.customer}/help-centre-buying-back-issues.html">{$lng.lbl_our_service}</a> </li>
      <li><a href="{$catalogs.customer}/help-centre-buying-subscriptions.html">{$lng.lbl_careers}</a> </li>
      <li><a href="{$catalogs.customer}/help-centre-buying-digital-editions.html">{$lng.lbl_contact_us}</a> </li>
    </ul>
  </div>

  <div class="footer_block" id="buying">
    <h4>{$lng.lbl_buying_magazines}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/search-for-magazine-articles.html">{$lng.lbl_find_magazines_articles}</a></li>
      <li>{cms service_code="bying_back_issues" preload_popup="Y"}</li>
      <li><a href="{$catalogs.customer}/help-centre-buying-subscriptions.html">{$lng.lbl_bying_subscriptions}</a></li>
      <li><a href="{$catalogs.customer}/Delivery-Rates_Basics.html">{$lng.lbl_delivery_rates_info}</a></li>
    </ul>
  </div>

  <div class="footer_block" id="selling">
    <h4>{$lng.lbl_selling_magazines}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/start-selling.html">{$lng.lbl_new_seller_overview}</a></li>
      <li><a href="{$catalogs.customer}/seller-account-registration.html">{$lng.lbl_new_seller_registration}</a></li>
      <li><a href="{$catalogs.admin}/index.php">{$lng.lbl_existing_seller_login}</a></li>
  {*    <li><a href="{$catalogs.customer}/seller-account-registration.html">{$lng.lbl_seller_terms}</a> </li>*}
      <li><a href="{$catalogs.customer}/help-centre-selling-trade-services.html">{$lng.lbl_trade_services}</a> </li>
      <li><a href="{$catalogs.customer}/contribute-content-introduction.html">{$lng.lbl_contribute_content}</a> </li>
    </ul>
  </div>

  <div class="footer_block" id="advertising">
    <h4>{$lng.lbl_advertising}</h4>
    <ul>
      <li><a href="{$catalogs.customer}/advertising-guide-intro.html">{$lng.lbl_advertising_overview}</a></li>
      <li><a href="{$catalogs.customer}" class="incomplete">{$lng.lbl_rate_card}</a> </li>
      <li><a href="{$catalogs.customer}" class="incomplete">{$lng.lbl_make_booking}</a> </li>
    </ul>
  </div>
  <div class="footer_block contacts_block" id="contactus">
    <h4>{$lng.lbl_contact_us}</h4>
    <ul class="toggle-footer">
      <li>{$config.Company.company_name}</li>
      <li>
        {if $config.Company.address ne ""}{$config.Company.address}, {/if}<br />
        {if $config.Company.city ne ""}{$config.Company.city}, {/if}<br />
        {if $config.Company.state ne ""}{$config.Company.state}, {/if}
        {if $config.Company.state ne ""}{$config.Company.zipcode}{/if}
      </li>
      <li>&nbsp;</li>
      <li>&nbsp;</li>
      <li>{if $config.Company.company_phone ne ""}{$lng.lbl_phone}: <span> {$config.Company.company_phone}</span>{/if}</li>
      <li>{if $config.Company.users_department ne ""}{$lng.lbl_email}: <span><a href="mailto:{$config.Company.users_department}">{$config.Company.users_department}</a></span>{/if}</li>
    </ul>
  </div>   
</nav>


<div class="footer_promo">
  <div class="delivering">Now delivering to over 40 countries!</div>
  <div class="switch_version">{include file="addons/mobile/bottom_links.tpl"}</div>
  <div class="float-right"><img src="{$AltImagesDir}/cards.png" width="255" height="32" style="display: block;"></div>
</div>

</div>
{/if}

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

{literal}
<script type="text/javascript">
$(document).ready(function() {
  $("#about h4").click(function() {
    $("#about ul").toggle();
    $("#about h4").toggleClass("opened");
  });

  $("#buying h4").click(function() {
    $("#buying ul").toggle();
    $("#buying h4").toggleClass("opened");
  });

  $("#selling h4").click(function() {
    $("#selling ul").toggle();
    $("#selling h4").toggleClass("opened");
  });

  $("#advertising h4").click(function() {
    $("#advertising ul").toggle();
    $("#advertising h4").toggleClass("opened");
  });

  $("#contactus h4").click(function() {
    $("#contactus ul").toggle();
    $("#contactus h4").toggleClass("opened");
  });
});
</script>
{/literal}
