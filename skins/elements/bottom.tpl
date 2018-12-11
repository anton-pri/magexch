<footer>
{if ($usertype eq "C" or $usertype eq "B") && !$home_style}
<div class="bottom_links">
<nav class="line960">
  <div class="footer_block">
    <h4>{$lng.lbl_information}</h4>
    <ul>
      <li><a href="{$catalogs.customer}">{$lng.lbl_home}</a></li>
      <li><a href="index.php?target=help&section=contactus">{$lng.lbl_contact_us}</a> </li>
      <li>{if $usertype eq "C"}<a href="{$current_location}/index.php?target=cart">{$lng.lbl_shopping_cart}</a> {/if}</li>
      <li><a href="{pages_url var="help" section="business"}">{$lng.lbl_privacy_statement}</a></li>
      <li><a href="{pages_url var="help" section="conditions"}">{$lng.lbl_terms_n_conditions}</a></li>
      <li><a href="{pages_url var="help"}">{$lng.lbl_customer_service}</a></li>
      <li><a href="{pages_url var="help" section="about"}">{$lng.lbl_about_us}</a> </li>
     {include file='customer/elements/bottom_links.tpl'}

    </ul>
  </div>

  <div class="footer_block">
    <h4>{$lng.lbl_categories}</h4> 
    <ul>
    {select_categories category_id=0 current_category_id=$cat assign='categories'}
    {foreach from=$categories item=c}
      <li>
        <a href="{pages_url var='index' cat=$c.category_id}">{$c.category}</a>
      </li>
    {/foreach}
    </ul>
  </div>

  <div class="footer_block">
    <h4>{$lng.lbl_follow_us}</h4>
    <ul>
      <li class="facebook"><a href="http://facebook.com" target="_blank">{$lng.lbl_facebook}</a></li>
      <li class="twitter"><a href="http://twitter.com" target="_blank">{$lng.lbl_twitter}</a> </li>
    </ul>
  </div>

  <div class="footer_block contacts_block">
    <ul class="toggle-footer">
      <li>{$config.Company.company_name}</li>
      <li>
        {if $config.Company.address ne ""}{$config.Company.address}, {/if}
        {if $config.Company.city ne ""}{$config.Company.city}, {/if}
        {if $config.Company.state ne ""}{$config.Company.state}, {/if}
        {if $config.Company.state ne ""}{$config.Company.zipcode}{/if}
      </li>
      <li>{if $config.Company.company_phone ne ""}Call us now: <span> {$config.Company.company_phone}</span>{/if}</li>
      <li>{if $config.Company.users_department ne ""}Email: <span><a href="mailto:{$config.Company.users_department}">{$config.Company.users_department}</a></span>{/if}</li>
    </ul>
  </div>
        
</nav>
</div>
{/if}


<div id="footer_container">
<div id="footer">
{$lng.txt_lpop_warning_C}
<div class="bottom_line">{include file='elements/copyright.tpl'}</div>
{*
<div class="newsletter">
    <input type="submit" value="{$lng.lbl_subscribe_button}" class="subscribe_button" />
    <input type="text" value="{$lng.lbl_sign_up_for_our_newsletter}" class="news_input" />
</div>
*}


<div class="clear"></div>


{if $addons.now_online && $home_style ne 'popup' && $users_online}
{include file='addons/now_online/menu_users_online.tpl'}
{/if}
</div>
</div>
</footer>
