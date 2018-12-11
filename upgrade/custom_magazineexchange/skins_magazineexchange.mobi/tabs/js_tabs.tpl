{* TODO: replace this functionality by jQuery plugin, remove corresponding css classes *}
{include_once file='tabs/tabs_js.tpl'}

{strip}
<div class="accordion">

{foreach from=$js_tabs item=tab key=ct}
    <div class="accordion_title">{$tab.title}</div>
    <div class="accordion_content">
      {include file=$tab.template included_tab=$ct}
    </div>
{/foreach}
</div>
{/strip}

{if $js_tab_buttons}
<div class="buttons">
{foreach from=$js_tab_buttons item=button}
    {include file='buttons/button.tpl' button_title=$button.title href=$button.href style='button'}
{/foreach}
<div class="clear"></div>
</div>
{/if}
{*
<script type="text/javascript">
    var tabs_images_dir='{$ImagesDir}/tab/';
    switchOn('tab_{$js_tab}','contents_{$js_tab}', '{$js_tab}', '{$group}');
</script>
*}