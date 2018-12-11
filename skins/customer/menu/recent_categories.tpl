{if $recent_categories}
{capture name=menu}
<ul>
{foreach from=$recent_categories item=c}
{*<img src="{$ImagesDir}/recent_bullet.gif" width="12" height="12" />*}
<li><a href="{pages_url var='index' cat=$c.category_id}">{$c.category}</a></li>
{/foreach}
</ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_recent_categories content=$smarty.capture.menu style='categories'}
{/if}
