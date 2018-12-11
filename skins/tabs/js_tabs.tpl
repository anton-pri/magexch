{* TODO: replace this functionality by jQuery plugin, remove corresponding css classes *}
{include_once file='tabs/tabs_js.tpl'}

{strip}
<div class="tabs">
{foreach from=$js_tabs item=tab key=ct}
    {include file='tabs/section_js_tab.tpl' title=$tab.title id="tab_`$ct`" onclick="javascript: switchOn('tab_`$ct`', 'contents_`$ct`', '`$ct`', '`$group`');"}
{/foreach}
</div>
{/strip}

<div id="contentscell{$group}" class="tab_general_content">
{foreach from=$js_tabs item=tab key=ct}
<div class="tab_content_not_selected" id="contents_{$ct}">
{include file=$tab.template included_tab=$ct}
</div>
{/foreach}
</div>

{if $js_tab_buttons}
<div class="buttons">
{foreach from=$js_tab_buttons item=button}
    {include file='buttons/button.tpl' button_title=$button.title href=$button.href style='button'}
{/foreach}
<div class="clear"></div>
</div>
{/if}

<script type="text/javascript">
    var tabs_images_dir='{$ImagesDir}/tab/';
    switchOn('tab_{$js_tab}','contents_{$js_tab}', '{$js_tab}', '{$group}');
</script>
