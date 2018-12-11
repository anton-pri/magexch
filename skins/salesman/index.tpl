<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>{strip}
{section name=position loop=$location step=-1}{$location[position].0|strip_tags|escape}{if not %position.last%} - {/if}{/section}
{/strip}</title>
{if !$is_pdf}{include file='elements/meta.tpl'}{/if}

<link rel="stylesheet" href="{$catalogs.$app_area}/custom.css" type="text/css" />
{include file='admin/service_css.tpl'}
{include file='admin/service_js.tpl'}

{literal}
	<script type="text/javascript">
		$(function () {
			$('input[type="radio"]').ezMark();
		});
	</script>
{/literal}

</head>
<body{$reading_direction_tag}>
<div class="admin_all">
<div class="admin_content">

{if !$customer_id && !$home_style}
    <div class="admin-login">
{/if}
<div class="admin">
{if $home_style eq 'popup'}
    <div class="admin_popup">
    {include file='common/head.tpl'}
{elseif $home_style ne 'iframe'}
    {include file='admin/head.tpl'}
{/if}

{capture name=middle_content}
    
    
    
    {if $current_section}
    <div class="tab-left">{include file='tabs/tabs.tpl'}</div>
    <div class="tab_general_content tab-right">
    {/if}



{*{$current_main_dir}/{$current_section_dir}/{$main}.tpl*}
    <div class="main">

        {if $main ne "main"}{include file='admin/main/location.tpl'}{/if}

        {if $home_style ne 'iframe' && $home_style ne 'popup'}
            {include file='common/dialog_message.tpl'}
            {include file='main/settings/settings_top.tpl'}
        {/if}

        {include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}

    </div>

    {if $current_section}
    </div>
    {/if}
{/capture}
{if !$customer_id && !$home_style}

      <div class="auth_admin">
        {include file='elements/auth_admin.tpl'}
      </div>
    {*$smarty.capture.middle_content*}
{else}
<div class="middle-content">
    {$smarty.capture.middle_content}
    <div class="clear"></div>
</div>
{/if}


{if $home_style eq 'popup'}
    </div>
{/if}
</div>
{if !$customer_id && !$home_style}
    </div>
{/if}
</div>

{if $home_style ne 'iframe'&& $home_style ne 'popup'}
{include file='elements/bottom_admin.tpl'}
{/if}
</div>

</body>
</html>
