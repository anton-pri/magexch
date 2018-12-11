<div class="header_logo">
    <div class="logo"><a href="{$catalogs.salesman}/index.php">{include file='main/images/webmaster_image.tpl' image='logo_admin'}</a></div>
{if $customer_id}
    <div class="auth">{include file='elements/authbox_top.tpl'}</div>
{/if}
</div>

<div class="header_line">
    <div class="top_menu">{include file='menu/common.tpl'}</div>
    <div class="lng">{include file='main/top_language.tpl' without_label=1}</div>
</div>
<div class="clear"></div>
