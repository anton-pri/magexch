<div class="send2friend_container">
<script type="text/javascript">
<!--
var requiredFields = new Array();
requiredFields[0] = new Array('send_name', "{$lng.lbl_send_your_name|strip_tags|replace:'"':'\"'}", false);
requiredFields[1] = new Array('send_from', "{$lng.lbl_send_your_email|strip_tags|replace:'"':'\"'}", false);
requiredFields[2] = new Array('send_to', "{$lng.lbl_recipient_email|strip_tags|replace:'"':'\"'}", false);
-->
</script>
{include_once file='js/check_required_fields_js.tpl'}
{include_once file='js/check_email_script.tpl'}

<div class="send_to_friend_form">
<form action="{pages_url var="product" product_id=$product.product_id}" method="post" name="send">
<input type="hidden" name="action" value="send" />

<div class="row">
<div class="input_field_0">
    <label>{$lng.lbl_send_your_name}</label>
    <input id="send_name" type="text" name="name" value="{$send_to_friend_info.name|escape}" {if $customer_id}readonly{/if} />
    {if $send_to_friend_info.fill_err and $send_to_friend_info.name eq ''}<span class="field_error">&lt;&lt;</span>{/if}
</div>
</div>
<div class="row">
<div class="input_field_0">
    <label>{$lng.lbl_fiends_email}</label>
    <input id="send_to" type="email" name="email" onchange="javascript: checkEmailAddress(this);" value="{$send_to_friend_info.email|escape}" {if $customer_id}readonly{/if}/>
    {if $send_to_friend_info.fill_err and $send_to_friend_info.email eq ''}<span class="field_error">&lt;&lt;</span>{/if}
</div>
</div>


<div class="row">
<div class="input_field_1">
    <label>{$lng.lbl_your_message}</label>
    <textarea id="send_from" rows="4" cols="40" name="from">{$send_to_friend_info.from}</textarea>
    {if $send_to_friend_info.fill_err and $send_to_friend_info.from eq ''}<span class="field_error">&lt;&lt;</span>{/if}
</div>
</div>

<div class="row image_verification">
{if $addons.image_verification and $show_antibot.on_send_to_friend eq 'Y'}
{include file="addons/image_verification/spambot_arrest.tpl" mode="simple" id=$antibot_sections.on_send_to_friend}
{/if}
</div>

</form>
</div>

<div class="button_left_align">
{include file='buttons/button.tpl' style='btn' button_title=$lng.lbl_send_to_friend href="javascript: if(checkRequired(requiredFields)) cw_submit_form('send');"}
</div>

</div>

<div class="clear"></div>

</div>
