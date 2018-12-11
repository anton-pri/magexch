<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>{tunnel func='cw_core_get_meta'  via='cw_call' param1='title'}</title>
{include file='elements/meta.tpl'}

{include file='customer/service_css.tpl'}
{include file='customer/service_js.tpl'}

<![if IE 7]>
    <link rel="stylesheet" href="{$SkinDir}/customer.IE7.css" type="text/css" media="screen" />
<![endif]>
{if $addons.estore_category_tree}
<link rel="stylesheet" href="{$SkinDir}/addons/estore_category_tree/styles.css" type="text/css" media="screen" />
{/if}
{if $addons.estore_gift}
<link rel="stylesheet" href="{$SkinDir}/addons/estore_gift/styles.css" type="text/css" media="screen" />
{/if}
</head>
<body{$reading_direction_tag}{if $body_onload ne ''} onload="javascript: {$body_onload}"{/if}>
<div id="page-container">
<div id="page-container2">
{if $is_pdf}
    {include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}
{else}
<script language="javascript">window.name = "main_window";</script>
{if $addons.google_analytics}
{include file='addons/google_analytics/google.tpl'}
{/if}

{if $home_style eq 'popup'}
    {include file='common/head.tpl'}
{elseif $home_style ne 'iframe'}
    {include file='customer/head.tpl'}
{/if}



<div id="main_area" class="{$main}_class">
{*if !$home_style && !$is_cart}
    <div class="main-left">
    {include file='customer/menu/menu_sections.tpl' sections=$left_sections}

    </div>
    <div class="main-center">

{/if*}
{if $home_style ne 'iframe'}
{*if $main !== "welcome"}
    {include file='customer/main/location.tpl'}
{/if*}
    {include file='common/dialog_message.tpl'}

    {if $config.product_filter.position eq 'top'}
    {include_once_src file='main/include_js.tpl' src='customer/product-filter/product-filter.js'}
    {include file="customer/product-filter/top-view/`$config.product_filter.template`.tpl" }
    {/if}

{/if}

{if $current_section}
{include file='tabs/tabs.tpl'}
<div class="tab_general_content">
{/if}

{* {$current_main_dir}/{$current_section_dir}/{$main}.tpl *}
{include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}

{if $current_section}
</div>
{/if}

    {if !$home_style && !$is_cart}
        </div>
{*
        <div class="main-right">
         {include file='customer/menu/menu_sections.tpl' sections=$right_sections}
  
        </div>
*}
    {/if}
</div>

    {if $home_style ne 'iframe' && $home_style ne 'popup'}
        {include file='elements/bottom.tpl'}
    {/if}

{/if}
</div>
</div>
</body>
</html>
