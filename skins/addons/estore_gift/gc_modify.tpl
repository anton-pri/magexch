<form name="frm_gc" action="index.php?target=giftcerts" method="post">
<input type="hidden" name="action" value="{$smarty.get.mode|escape:"html"}" />
<input type="hidden" name="gc_id" value="{$smarty.get.gc_id|escape:"html"}" />
<input type="hidden" name="send_via" value="E" />

<div class="input_field_1">
    <label>{$lng.lbl_from}</label>
    <input type="text" name="purchaser" size="30" value="{if $usertype eq "A" && $smarty.get.mode eq 'add_gc'}{$config.Company.company_name|escape}{elseif $giftcert.purchaser}{$giftcert.purchaser|escape:"html"}{else}{if $userinfo.firstname ne ''}{$userinfo.firstname|escape} {/if}{$userinfo.lastname|escape}{/if}" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_to}</label>
    <input type="text" name="recipient" size="30" value="{$giftcert.recipient|escape:"html"}" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_message}</label>
    <textarea name="message" rows="8" cols="50">{$giftcert.message}</textarea>
</label>

<div class="input_field_1">
    <label>{$lng.lbl_gc_choose_amount_subtitle} {$config.General.currency_symbol}</label>
    <input type="text" name="amount" size="10" maxlength="9" value="{$giftcert.amount|formatprice}" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_email}</label>
    <input type="text" name="recipient_email" size="30" value="{$giftcert.recipient_email|escape}" />
</div>

</form>

{if $mode eq "modify_gc"}
    {include file="buttons/gc_update.tpl" href="javascript: cw_submit_form('frm_gc');"}
{else}
    {include file="buttons/button.tpl" button_title=$lng.lbl_gc_create href="javascript: cw_submit_form('frm_gc');"}
{/if}
