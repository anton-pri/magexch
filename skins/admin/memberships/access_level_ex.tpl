{math equation="a+1" a=$level assign="level"}
{foreach from=$def item=it key=key}
{assign var="keyM" value="__$key"}
{assign var="keyAmount" value="____$key"}
<tr{cycle values=", class='cycle'"}>
    <td width="98%" class="access_level_{$level}">{lng name=$it.name}</td>
    <td width="1%" align="center">
        {if $it.amount}
        <input type="text" name="up_access_level[{$keyAmount}]" value="{$access_level.$keyAmount}" size="4"/>
        {/if}
        <input type="hidden" name="up_access_level[{$key}]" value="0">
        <input type="checkbox" id="{$key}" name="up_access_level[{$key}]" value="1" onclick="javascript: authm('{$key}');" {if $access_level.$key}checked{/if}>
    </td>
    <td width="1%" align="center">
        <input type="hidden" name="up_access_level[{$keyM}]" value="0">
        <input type="checkbox" id="{$keyM}" name="up_access_level[{$keyM}]" value="1" onclick="javascript: authm('{$keyM}');" {if $access_level.$keyM}checked{/if}{if !$access_level.$key} disabled{/if} />
    </td>
</tr>
{if $it.sub}
{include file='admin/memberships/access_level_ex.tpl' def=$it.sub level=$level}
{/if}
{/foreach}
