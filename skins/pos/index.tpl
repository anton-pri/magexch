<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>{strip}
{section name=position loop=$location step=-1}{$location[position].0|strip_tags|escape}{if not %position.last%} - {/if}{/section}
{/strip}</title>
{include file='elements/meta.tpl'}
<link rel="stylesheet" href="{$SkinDir}/general.css" type="text/css" media="screen" />
<link rel="stylesheet" href="{$SkinDir}/salesman.css" type="text/css" media="screen" />
</head>
<body{$reading_direction_tag}{if $onkeypress} onkeypress="javascript: {$onkeypress}(event, '{$order.doc_id}');"{/if}>
{if $home_style eq 'popup'}
{include file='common/head.tpl'}
{elseif $home_style ne 'iframe'}
{include file='pos/head.tpl'}
{/if}

{capture name=middle_content}
    {if $home_style ne 'iframe' && $home_style ne 'popup'}
        {include file='common/dialog_message.tpl'}
        {include file='main/settings/settings_top.tpl'}
    {/if}
    {if $current_section}
    {include file='tabs/tabs.tpl'}
    <div class="tab_general_content">
    {/if}

    {include file="`$current_main_dir`/`$current_section_dir`/`$main`.tpl"}

    {if $current_section}
    </div>
    {/if}
{/capture}
{if !$customer_id && !$home_style}
    <div class="main-left">
    {include file='elements/auth.tpl'}
    </div>
    <div class="main-center">
    {$smarty.capture.middle_content}
    </div>
{else}
    {$smarty.capture.middle_content}
{/if}

{if $home_style ne 'iframe'}
{include file='elements/bottom.tpl'}
{/if}

</body>
</html>
