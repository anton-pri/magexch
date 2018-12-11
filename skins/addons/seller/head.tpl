<div class="head cl_{$main}">
<div class="header_logo">
    <div class="logo"><a href="{$catalogs.seller}/index.php">{include file='main/images/webmaster_image.tpl' image='logo'}</a></div>
{if $customer_id}
    <ul class="nav-header pull-right">
    	<li>
    <div class="btn-group">
      <button class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
        <img src="{$ImagesDir}/avatar.png" alt="Avatar">
        <span class="caret"></span>
      </button>
      {include file='elements/authbox_top_admin.tpl'}
    </div>
    	
    	</li>
    	
    </ul>
{/if}
</div>
<div class="clear"></div>

<div class="header_line">
{include file='common/top-filters.tpl'}
</div>

<div class="clear"></div>
</div>

