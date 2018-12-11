{include assign="tmp_value" file='common/currency.tpl' value=$config.General.minimal_order_amount}
<font class="ErrorMessage">
{$lng.err_checkout_not_allowed_msg|substitute:"value":$tmp_value}
<br /><br />
{include file="buttons/go_back.tpl"}
</font>
