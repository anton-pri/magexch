<div class="box">


<div class="input_field_1">
    <label>{$lng.lbl_payment_quotes}</label>
    <label>
    <input type="hidden" name="posted_data[is_quotes]" value="0" />
    <input type="checkbox" name="posted_data[is_quotes]" value="1" {if $payment.is_quotes} checked{/if} />
    </label>
</div>

<table width="100%" class="header quotes">
<tr>
    <th>{$lng.lbl_del}</th>
    <th>{$lng.lbl_quote}</th>
    <th>{$lng.lbl_payment}</th>
    <th>{$lng.lbl_expare_days}</th>
    <th>{$lng.lbl_start_expare_days}</th>
    <th>{$lng.lbl_fixed_days}</th>
    <th>{$lng.lbl_net} (%)</th>
    <th>{$lng.lbl_tax} (%)</th>
    <th>{$lng.lbl_fees} (%)</th>
    <th>{$lng.lbl_commission} (%)</th>
{*
    <th>{$lng.lbl_expire_notification}</th>
*}
</tr>
{if $payment.quotes}
{foreach from=$payment.quotes item=quote}
<tr>
    <td><input type="checkbox" name="quotes[{$quote.quote_id}][del]" value="1" /></td>
    <td><input type="text" name="quotes[{$quote.quote_id}][quote]" value="{$quote.quote}" class="quote_name" /></td>
    <td><div style="width: 80px">{include file='main/select/payment.tpl' name="quotes[`$quote.quote_id`][inc_payment_id]" value=$quote.inc_payment_id}</div></td>
    <td align="center"><input type="text" name="quotes[{$quote.quote_id}][exp_days]" value="{$quote.exp_days}" size="2" /></td>
    <td align="center"><div style="width: 80px">{include file='main/select/payment_quote_exp.tpl' name="quotes[`$quote.quote_id`][start_exp_days]" value=$quote.start_exp_days}</div></td>
    <td align="center"><input type="text" name="quotes[{$quote.quote_id}][fixed_days]" value="{$quote.fixed_days}" size="2" /></td>
    <td align="center"><input type="text" name="quotes[{$quote.quote_id}][is_net]" value="{$quote.is_net}" size="2" /></td>
    <td align="center"><input type="text" name="quotes[{$quote.quote_id}][is_vat]" value="{$quote.is_vat}" size="2" /></td>
    <td align="center"><input type="text" name="quotes[{$quote.quote_id}][is_fee]" value="{$quote.is_fee}" size="2" /></td>
    <td align="center"><input type="text" name="quotes[{$quote.quote_id}][commission]" value="{$quote.commission}" size="2" /></td>
{*
    <td>
        <input type="checkbox" name="quotes[{$quote.quote_id}][mail_before]" value="{$quote.mail_before}" size="4" />
        <input type="checkbox" name="quotes[{$quote.quote_id}][mail_after]" value="{$quote.mail_after}" size="4" />
    </td>
*}
</tr>
{/foreach}
{else}
<tr>
    <td colspan="10" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<tr>
    <td colspan="10">{include file='common/subheader.tpl' title=$lng.lbl_add_new}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td><input type="text" name="quotes[0][quote]" value="" class="quote_name" /></td>
    <td><div style="width: 80px">{include file='main/select/payment.tpl' name="quotes[0][inc_payment_id]"}</div></td>
    <td align="center"><input type="text" name="quotes[0][exp_days]" value="" size="3" /></td>
    <td align="center"><div style="width: 80px">{include file='main/select/payment_quote_exp.tpl' name="quotes[0][start_exp_days]"}</div></td>
    <td align="center"><input type="text" name="quotes[0][fixed_days]" size="3" /></td>
    <td align="center"><input type="text" name="quotes[0][is_net]" value="" size="3" /></td>
    <td align="center"><input type="text" name="quotes[0][is_vat]" value="" size="3" /></td>
    <td align="center"><input type="text" name="quotes[0][is_fee]" value="0" size="3" /></td>
    <td align="center"><input type="text" name="quotes[0][comission]" value="0" size="3" /></td>
</tr>
</table>

</div>