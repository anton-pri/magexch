{capture name=section}

{array_chunk var=$current_sections assign="sections_per_row" cols=4}
<table width="100%" cellpadding="3" cellspacing="0">
{foreach from=$sections_per_row item=chunk}
<tr>
    {foreach from=$chunk item=info}
    <td width="25%">
<a href="{$info.link}">{$info.title_lng}</a>
    </td>
    {/foreach}
</tr>
{/foreach}
</table>

{/capture}
{include file='common/section.tpl' title=$lng.lbl_main_page_sections content=$smarty.capture.section}
