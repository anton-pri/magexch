{if $type eq 'V' && $levels}
{capture name=section}

<form method="post" action="index.php?target=memberships" name="form{$type}_fees">
<input type="hidden" name="action" value="seller_fees" />

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th >{$lng.lbl_membership}</th>
	<th align="center">Amount per item, {$config.General.currency_symbol}</th>
	<th align="center">Percentage for Seller, %</th>
        <th align="center">Flat Charges</th>
</tr>
</thead>

{foreach from=$levels item=v}
{assign var="membership_id" value=$v.membership_id}
<tr{cycle name=$type values=", class='cycle'"}>
	<td>{$v.membership|escape}</td>
	<td align="center"><input type="text" size="5" name="posted_data[{$membership_id}][item]" value="{$v.fees.item}"  class="micro"/></td>
	<td align="center"><input type="text" size="5" name="posted_data[{$membership_id}][percent]" value="{$v.fees.percent}"  class="micro"/></td>
        <td align="center"><a href="index.php?target=magexch_flat_charges&membership_id={$membership_id}" target="_blank">{$lng.lbl_edit}</a></td>
</tr>
{/foreach}



</table>
    <div class="buttons">
    {if $levels}
    {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form(document.form`$type`_fees);" button_title=$lng.lbl_update style="btn-green push-5-r push-20"}
    {/if}
    </div>

</form>
{/capture}
{include file='admin/wrappers/block.tpl' content=$smarty.capture.section title="Seller Percentage"}
{/if}
