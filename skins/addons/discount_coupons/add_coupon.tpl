<a name='coupon'></a>

{capture name=coupon}
<form action="index.php?target={$current_target}" name="coupon_form" method="post">
<input type="hidden" name="action" value="add_coupon" />
<div class="input_field_1 coupon">
	<input type="text" size="32" name="coupon" />
       {include file="buttons/submit.tpl" href="javascript: cw_submit_form('coupon_form');"}
</div>
</form>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.coupon title="Redeem a discount coupon"}