{if $usertype eq "A" && $doc.info.extras.paypal_type eq 'UKDP' && $doc.info.extras.pnref}
<div class="input_field_0">
	<table>
        <tr>
        <td><b>Status</b></td>
        <td>
        {if $doc.info.extras.capture_status eq 'A'}Pre-auth
        {elseif $doc.info.extras.capture_status eq ''}Sale (Auth&Capture)
        {elseif $doc.info.extras.capture_status eq 'C'}Captured
        {elseif $doc.info.extras.capture_status eq 'V'}Void
        {/if}
        </td>
        </tr>
	<tr>    
	<td><b>PayPal init transaction</b></td>
        <td>{$doc.info.extras.pnref}</td>
	</tr>
	<tr>
	<td><b>Transaction amount</b></td>
	<td>{if $doc.info.extras.transaction_amount}{include file='common/currency.tpl' value=$doc.info.extras.transaction_amount}
	{else}{include file='common/currency.tpl' value=$doc.info.total}{/if}</td>
	</tr>
    {if $doc.info.extras.capture_pnref}
        <tr>
        <td><b>PayPal Capture transaction</b></td>
        <td>{$doc.info.extras.capture_pnref}</td>
        </tr>
        <tr>
    {/if}
    {if $doc.info.extras.captured_amount}
    <tr>
    <td><b>Captured amount</b></td>
    <td>{include file='common/currency.tpl' value=$doc.info.extras.captured_amount}</td>
    </tr>
    {/if}
	</table>
</div>
{/if}
