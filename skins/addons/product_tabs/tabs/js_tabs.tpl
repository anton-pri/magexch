{if $product_tabs ne '' && $app_area eq 'customer'}
    {foreach from=$product_tabs item=tab}
        {assign var="ct" value="`$ct+1`"}
        {include file='tabs/section_js_tab.tpl' title=$tab.title id="tab_`$ct`" onclick="javascript: switchOn('tab_`$ct`', 'contents_`$ct`', '`$ct`', '`$group`');"}
    {/foreach}
{/if}