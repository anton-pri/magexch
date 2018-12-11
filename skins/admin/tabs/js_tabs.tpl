{* TODO: replace this functionality by jQuery plugin, remove corresponding css classes *}
    {include_once file='admin/tabs/tabs_js.tpl'}
{strip}
<ul class="nav nav-tabs{if $style} {$style}{else} nav-tabs-alt{/if}">
{foreach from=$js_tabs item=tab key=ct}
    {include file='admin/tabs/section_js_tab.tpl' title=$tab.title id="tab_`$ct`" onclick="javascript: switchOn('tab_`$ct`', 'contents_`$ct`', '`$ct`', '`$group`');"}
{/foreach}
</ul>
{/strip}

<div id="contentscell{$group}" class="block-content tab-content {if $style} tab-content-{$style}{/if}">
{foreach from=$js_tabs item=tab key=ct}
<div class="tab-pane" id="contents_{$ct}">
{include file=$tab.template included_tab=$ct}
</div>
{/foreach}
</div>

{if $js_tab_buttons}
<div class="buttons">
{foreach from=$js_tab_buttons item=button}
    {include file='admin/buttons/button.tpl' button_title=$button.title href=$button.href style=$button.style}
{/foreach}
<div class="clear"></div>
</div>
{/if}
<script type="text/javascript">
    var tabs_images_dir='{$ImagesDir}/tab/';
    switchOn('tab_{$js_tab}','contents_{$js_tab}', '{$js_tab}', '{$group}');
</script>
