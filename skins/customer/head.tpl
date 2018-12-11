<div id="header_container">

<div class="top_menu">
  <div class="line960">
    {include file='customer/top_menu.tpl'}
    {*include file='main/select/language_flag.tpl'*}
  </div>
</div>

<header>
<div class="header_logo">
     <!-- cw@logo [ -->
    <div class="logo"><a href="{$catalogs.customer}">{include file='main/images/webmaster_image.tpl' image='logo'}</a></div>
     <!-- cw@logo ] -->

    <!-- cw@search [ -->
    <div class="search">{include file='customer/search.tpl'}</div>
    <!-- cw@search ] -->

    <!-- cw@minicart [ -->
    <div class="top_cart">{include file='customer/menu/microcart.tpl'}</div>
    <!-- cw@minicart ] -->

    <!-- cw@top_auth [ -->
    <div class="top_icons">
        <div class="square">
           {include file='customer/menu/register.tpl'}
           {include file='customer/menu/login.tpl'}
        </div>
    </div>
    <!-- cw@top_auth ] -->


</div>
<div class="clear"></div>

</header>
<!-- cw@top_categories [ -->
{if $config.Appearance.top_categories eq 'Y'} 
{include file='customer/top_categories.tpl'}
{/if}
<!-- cw@top_categories ] -->

</div>
