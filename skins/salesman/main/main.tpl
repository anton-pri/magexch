{if $salesman_reach || $salesman_reached}
{capture name=section}
{if $salesman_reach gt 0}
<p class="ErrorMessage">
{$lng.lbl_only_x_to_reach|substitute:"XXX":$salesman_reach|substitute:"CURRENCY":$config.General.currency_symbol}
</p>
    {foreach from=$salesman_premiums item=pr}
<p>{$pr.title}</p>
    {/foreach}
{elseif $salesman_reached}
<p>
<form action="index.php?target=home" method="post" name="premium_frm">
<input type="hidden" name="action" value="premiums" />
<table width="100%">
{foreach from=$salesman_premiums item=pr}
<tr>
    <td>{$pr.title}</td>
    {if !$salesman_selected}
    <td width="1%"><input type="checkbox" name="choosed_premium[{$pr.id}]" value="1"></td>
    {/if}
</tr>
{/foreach}
{if !$salesman_selected}
<tr>
    <td colspan="2" align="right">{include file='buttons/button.tpl' href="javascript:cw_submit_form(document.premium_frm);" button_title=$lng.lbl_choose}</td>
</tr>
{/if}
</table>
</form>
{/if}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_salesman_menu content=$smarty.capture.section}
{/if}

{include file="salesman/main/home_last_orders.tpl"}

{include file="salesman/main/home_orders.tpl"}
{include file="main/affiliates/affiliates_incl.tpl"}
{include file="salesman/main/home_comissions.tpl"}
