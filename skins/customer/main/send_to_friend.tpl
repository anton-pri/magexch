<div class="send2friend_container">
<script type="text/javascript">
<!--
var requiredFields = new Array();
requiredFields[0] = new Array('send_name', "{$lng.lbl_send_your_name|strip_tags|replace:'"':'\"'}", false);
requiredFields[1] = new Array('send_to', "{$lng.lbl_fiends_email|strip_tags|replace:'"':'\"'}", false);
requiredFields[2] = new Array('send_msg', "{$lng.lbl_your_message|strip_tags|replace:'"':'\"'}", false);
function process_send2friend_form() {ldelim}
{if $addons.image_verification and $show_antibot.on_send_to_friend eq 'Y'}
    if(checkRequired(requiredFields) && typeof cw_spambot_form_check === 'function')
        cw_spambot_form_check('send');
{else} 
    if(checkRequired(requiredFields)) 
        cw_submit_form('send');
{/if}
{rdelim}
-->
</script>
{include_once file='js/check_required_fields_js.tpl'}
{include_once file='js/check_email_script.tpl'}

<div style="clear: both" class="sendto">
<p>{$lng.lbl_send_to_friend_text}</p>
</div>
<div class="product_info">
    <div class="image">{include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id}</div>
    <div class="product">{$product.product}
<div class="send_to_friend_form">
<form action="{pages_url var="product" product_id=$product.product_id}" method="post" name="send">
<input type="hidden" name="action" value="send" />

<div class="input_field_0">
    <label>{$lng.lbl_send_your_name}</label>
    <input id="send_name" type="text" name="name" value="{$send_to_friend_info.name|escape}" {if $customer_id}readonly{/if} />
    {if $send_to_friend_info.fill_err and $send_to_friend_info.name eq ''}<span class="field_error">&lt;&lt;</span>{/if}
</div>

<div class="input_field_0">
    <label>{$lng.lbl_fiends_email}</label>
    <input id="send_to" type="email" name="email" onchange="javascript: checkEmailAddress(this);" value="{$send_to_friend_info.email|escape}" {if $customer_id}readonly{/if}/>
    {if $send_to_friend_info.fill_err and $send_to_friend_info.email eq ''}<span class="field_error">&lt;&lt;</span>{/if}
    <div class="clear" style="height: 10px;"></div>
</div>


<div class="input_field_1">
    <label>{$lng.lbl_your_message}</label>
    <textarea id="send_msg" rows="4" cols="40" name="from">{$send_to_friend_info.from}</textarea>
    {if $send_to_friend_info.fill_err and $send_to_friend_info.from eq ''}<span class="field_error">&lt;&lt;</span>{/if}
</div>

{if $addons.image_verification and $show_antibot.on_send_to_friend eq 'Y'}
{include file="addons/image_verification/spambot_arrest.tpl" mode="simple" id=$antibot_sections.on_send_to_friend}
{/if}

</form>
</div>



    </div>
</div>

<div class="clear"></div>
<div class="button_left_align">
{include file='buttons/button.tpl' style='btn' button_title=$lng.lbl_send href="javascript: process_send2friend_form();"}
{include file='buttons/button.tpl' style='btn' button_title=$lng.lbl_close_window href="javascript:hm('send_to_friend_dialog');" view="top_"}
</div>
</div>
