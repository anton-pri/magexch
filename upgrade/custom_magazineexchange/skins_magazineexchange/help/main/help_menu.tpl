<div class="help_column">
<div align="center" class="ProductDetailsTitle">{$lng.lbl_help_guides}</div>
{capture name=menu}
  <ul>
    <li><a href="{$catalogs.customer}/help-centre-buying-searching-and-browsing.html">{$lng.lbl_searching_browsing}</a></li>
    <li><a href="{$catalogs.customer}/help-centre-buying-back-issues.html">{$lng.lbl_buying} - {$lng.lbl_back_issues}</a></li>
    <li><a href="{$catalogs.customer}/help-centre-buying-subscriptions.html">{$lng.lbl_buying} - {$lng.lbl_subscribtions}</a></li>
    <li><a href="{$catalogs.customer}/help-centre-buying-digital-editions.html">{$lng.lbl_buying} - {$lng.lbl_digital_editions}</a></li>

  </ul>

{/capture}
{include file='common/menu.tpl' title=$lng.lbl_buying content=$smarty.capture.menu style='help_menu'}

{capture name=menu_selling}
  <ul>
    <li><a href="{$catalogs.customer}/help-centre-selling-back-issues.html">{$lng.lbl_selling} - {$lng.lbl_back_issues}</a></li>
    <li><a href="{$catalogs.customer}/help-centre-selling-trade-services.html">{$lng.lbl_trade_services}</a></li>

  </ul>

{/capture}
{include file='common/menu.tpl' title=$lng.lbl_selling content=$smarty.capture.menu_selling style='help_menu'}

{capture name=menu_about}
  <ul>
    <li><a href="{$catalogs.customer}/help-centre-about-us-who-we-are.html">{$lng.lbl_who_we_are}</a></li>
    <li><a href="{$catalogs.customer}/help-centre-about-us-legal-stuff.html">{$lng.lbl_legal_stuff}</a></li>

  </ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_about_us content=$smarty.capture.menu_about style='help_menu'}


{capture name=menu_you}
  <ul>
    <li><a href="{$catalogs.customer}/help-centre-about-you-managing-your-account.html">{$lng.lbl_managing_your_account}</a></li>

  </ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_about_you content=$smarty.capture.menu_you style='help_menu'}

{capture name=menu_adv}
  <ul>
    <li><a href="{$catalogs.customer}/help-centre-advertising-information-and-rates.html">{$lng.lbl_info_rates}</a></li>

  </ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_advertising content=$smarty.capture.menu_adv style='help_menu'}

  <div class="simple_menu">
    <div align="center" class="ProductDetailsTitle">{$lng.lbl_quick_start_quides}</div>
    <ul>
      <li><a href="{$catalogs.customer}/new-seller-quick-start-guide-gt-who-can-sell.html">{$lng.lbl_quide_new_seller}</a></li>
      <li><a href="{$catalogs.customer}/search-for-magazine-articles.html">{$lng.lbl_searching_for_articles}</a></li>
      <li><a href="{$catalogs.customer}/Delivery-Rates_Basics.html">{$lng.lbl_delivery_rates_info}</a></li>
      <li><a href="{$catalogs.customer}/contribute-content-introduction.html">{$lng.lbl_adding_data_to_site}</a></li>
    </ul>
  </div>

  <div class="simple_menu">
    <div align="center" class="ProductDetailsTitle">{$lng.lbl_other_quick_links}</div>
    <ul>
      <li><a href="{$catalogs.customer}/help-centre-buying-searching-and-browsing.html">{$lng.lbl_contact_us}</a></li>
      <li><a href="{$catalogs.customer}/help-centre-buying-subscriptions.html">{$lng.lbl_payment_options}</a></li>

    </ul>
  </div>
</div>
