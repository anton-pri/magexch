{if $usertype eq "A" && $doc.info.extras.paypal_type eq 'USSTD' && $doc.info.extras.paypal_txnid}
<div class="input_field_0">
	<table>
	<tr>    
	<td><b>PayPal transaction</b></td>
        <td>{$doc.info.extras.paypal_txnid}</td>
	</tr>
    <tr>
        <td><b>Status</b></td>
        <td>
        {if $doc.info.extras.capture_status eq 'A'}Pre-auth
        {elseif $doc.info.extras.capture_status eq ''}Sale (Auth&Capture)
        {elseif $doc.info.extras.capture_status eq 'C'}Captured
        {elseif $doc.info.extras.capture_status eq 'V'}Void
        {elseif $doc.info.extras.capture_status eq 'R'}Refund
        {/if}
        </td>
    </tr>
    <tr>
    <td><b>Transaction amount</b></td>
    <td>{if $doc.info.extras.transaction_amount}{include file='common/currency.tpl' value=$doc.info.extras.transaction_amount}
    {else}{include file='common/currency.tpl' value=$doc.info.total}{/if}</td>
    </tr>
    {if $doc.info.extras.captured_amount}
    <tr>
    <td><b>Captured amount</b></td>
    <td>{include file='common/currency.tpl' value=$doc.info.extras.captured_amount}</td>
    </tr>
    {/if}
    {if $doc.info.extras.refund_txnid}
    <tr>
      <td><b>Refund transaction</b></td>
      <td>{$doc.info.extras.refund_txnid}</td>
    </tr>
    {/if}
	</table>
</div>
{/if}
