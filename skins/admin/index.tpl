<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>{include file='admin/main/title.tpl'}</title>
{if !$is_pdf}{include file='elements/meta.tpl'}{/if}
{if $is_email_invoice ne 'Y'}
{include file='admin/service_css.tpl'}
{include file='admin/service_js.tpl'}
{/if}
</head>
<body{$reading_direction_tag} {if $home_style eq 'iframe' || $home_style eq 'popup'}class="popup_body"{/if}>

{if !$customer_id && !$home_style}
{include file='common/dialog_message.tpl'}
  <div class="content overflow-hidden">
    <div class="row">
      {include file='elements/auth_admin.tpl'}
    </div>
  </div>
{else}
  {include file='common/form-group-toggle.tpl'}
  <div id="page-container" {if $home_style ne 'iframe'&& $home_style ne 'popup'}class="sidebar-l sidebar-o side-scroll header-navbar-fixed"{/if}>


  {if $home_style ne 'iframe'&& $home_style ne 'popup'}
    {include file='common/dialog_message.tpl'}
    {include file='admin/head.tpl'}
    {include file='admin/sidebar.tpl'}
    {capture name=middle_content}
 
    {*if $section_tabs}
    <ul class="nav nav-tabs section-tabs push-20-t push-20-l">{include file='admin/tabs/tabs.tpl'}</ul>
    
    <div class="tab_general_content {if $home_style ne 'iframe'}tab-right{/if}">
    {/if*}

    
    <div class="main">
        <!-- main template: {$current_main_dir}/{$current_section_dir}/{$main}.tpl -->
        {*if $main ne "main"}{include file='admin/main/location.tpl'}{/if*}

        {* Obsolete. We do not use page-specific settings anymore *}
        {*include file='main/settings/settings_top.tpl'*}

        {include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}

    </div>

    {if $section_tabs}
    </div>

    {/if}

  {/capture}

  <div class="middle-content" id="main-container">
    {$smarty.capture.middle_content}
  </div>




  {include file='elements/bottom_admin.tpl'}
  
  {else}
  	{*if $home_style eq 'popup'}
    	{include file='common/head.tpl'}
	{/if*}
	
  	<div class="block">
  		<div class="content">
<!-- main template: {$current_main_dir}/{$current_section_dir}/{$main}.tpl -->
  			{include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}
  		</div>
  	</div>
  	
  {/if}
  </div>

{/if}

</body>
</html>
