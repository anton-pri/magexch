{include_once file='tabs/tabs_js.tpl'}

{strip}
<div class="tabs-adm">
    <div class="tab_links">
    {foreach from=$js_tabs item=tab key=ct}
        {include file='tabs/section_js_tab_admin.tpl' title=$tab.title id="tab_`$ct`" onclick="javascript: switchOn('tab_`$ct`', 'contents_`$ct`', '`$ct`', '`$group`');"}
    {/foreach}</div>
    <div class="clear"></div>
</div>
{/strip}

{capture name=section}
<div id="contentscell{$group}" class="tab_general_content tab_admin">
    {foreach from=$js_tabs item=tab key=ct}
    <div class="tab_content_not_selected" id="contents_{$ct}">
        {include file=$tab.template included_tab=$ct}
    </div>
    {/foreach}
</div>

{if $js_tab_buttons}
<div id="sticky_content" class="buttons">
    {foreach from=$js_tab_buttons item=button}
        {include file='buttons/button.tpl' button_title=$button.title href=$button.href  style='button'}
    {/foreach}
    <div class="clear"></div>
</div>
{/if}
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_settings}

<script type="text/javascript">
    var tabs_images_dir='{$ImagesDir}/tab/';
    switchOn('tab_{$js_tab}','contents_{$js_tab}', '{$js_tab}', '{$group}');
</script>
