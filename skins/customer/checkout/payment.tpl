<input type="hidden" name="paymentid" value="{$payment_data.payment_id}" />

{if $payment_data.payment_template}
{include file=$payment_data.payment_template}
{elseif $payment_data.payment_type}
{include file="customer/payment/`$payment_data.payment_type`_info.tpl"}
{elseif $payment_data.payment_code}
{include file="customer/payment/`$payment_data.payment_code`_payment.tpl"}
{/if}
