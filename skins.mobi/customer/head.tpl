<div id="header_container">

<div class="top_menu">
  <div class="line960">
    {include file='customer/top_menu.tpl'}
    {*include file='main/select/language_flag.tpl'*}
  </div>
</div>

<header>
<div class="header_logo">
    <div class="logo"><a href="{$catalogs.customer}/index.php">{include file='main/images/webmaster_image.tpl' image='logo'}</a></div>
    <div class="search">{include file='customer/search.tpl'}</div>
    <div class="top_cart">{include file='customer/menu/microcart.tpl'}</div>

    <div class="top_icons">
        <div class="square">
           {include file='customer/menu/register.tpl'}
           {include file='customer/menu/login.tpl'}
        </div>
    </div>


</div>
<div class="clear"></div>

</header>
{*if $config.Appearance.top_categories eq 'Y'}
{include file='customer/top_categories.tpl'}
{/if*}
</div>
