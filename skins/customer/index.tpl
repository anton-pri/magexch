<!DOCTYPE html>
<html>
<head>
<title>{tunnel func='cw_core_get_meta' via='cw_call' param1='title'}</title>
{include file='customer/js_insert_codes.tpl'}

{include file='elements/meta.tpl'}

{include file='customer/service_css.tpl'}
{include file='customer/service_js.tpl'}


</head>
<body{$reading_direction_tag}{if $body_onload ne ''} onload="javascript: {$body_onload}"{/if}>
    <!-- cw@after_body -->

<div id="page-container">
<div id="page-container2">
{if $is_pdf}
    {include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}
{else}
<script language="javascript" type='text/javascript'>window.name = "main_window";</script>
{if $addons.google_analytics}
{include file='addons/google_analytics/google.tpl'}
{/if}

{if $home_style eq 'popup'}
    {*include file='common/head.tpl'*}
{elseif $home_style ne 'iframe'}
    {include file='customer/head.tpl'}
{/if}


<div id="main_area" class="{$main}_class">
{tunnel func='cw_check_if_breadcrumbs_enabled' via='cw_call' param1=$main assign='is_breadcrumbs_enabled'}
{if $is_breadcrumbs_enabled}
    {include file='customer/main/location.tpl'}
{/if}
{if $show_left_bar}
    <aside class="main-left">
      <!-- cw@left_menu [ -->
      {include file='customer/menu/menu_sections.tpl' sections=$left_sections}
      <!-- cw@left_menu ] -->

    </aside>
    <div class="main-center">

{/if}
{if $home_style ne 'iframe'}

    {include file='common/dialog_message.tpl'}

    {if $config.product.pf_position eq 'top'}
    {include_once_src file='main/include_js.tpl' src='customer/product-filter/product-filter.js'}
    {include file="customer/product-filter/top-view/`$config.product.pf_template`.tpl" }
    {/if}

{/if}

{if $current_section}
{include file='tabs/tabs.tpl'}
<div class="tab_general_content">
{/if}

{* {$current_main_dir}/{$current_section_dir}/{$main}.tpl *}
{if $main}
{include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}
{/if}

{if $current_section}
</div>
{/if}

{if $show_left_bar}
        </div>

{*
        <div class="main-right">
         {include file='customer/menu/menu_sections.tpl' sections=$right_sections}
  
        </div>
*}
    {/if}
<div class="clear"></div>
</div>

    {if $home_style ne 'iframe' && $home_style ne 'popup'}
        {include file='elements/bottom.tpl'}
    {/if}

{/if}
</div>
</div>
</body>
</html>
