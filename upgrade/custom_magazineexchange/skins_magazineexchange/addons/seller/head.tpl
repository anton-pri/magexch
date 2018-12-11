<header>
<div class="head cl_{$main}">
<div class="header_logo">
    <div class="logo"><a href="{$catalogs.seller}/index.php">{include file='main/images/webmaster_image.tpl' image='logo'}</a></div>
{if $customer_id}
    <div class="auth">{include file='elements/authbox_top.tpl'}</div>
{/if}
</div>

<div class="search"></div>

<div class="clear"></div>
</header>
<div class="header_line">
<div class="top_menu">{include file='menu/common.tpl'}</div>
{include file='common/top-filters.tpl'}
</div>

<div class="clear"></div>
</div>

