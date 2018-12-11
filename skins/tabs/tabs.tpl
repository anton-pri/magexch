<div class="tabs">{foreach from=$section_tabs item=tab}
{if $tab}{include file='tabs/section_tab.tpl' title=$tab.title_lng selected=$tab.selected href=$tab.link}{/if}
{/foreach}
</div>
<div class="clear none"></div>&nbsp;
