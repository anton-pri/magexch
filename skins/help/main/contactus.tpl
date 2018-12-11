<script type="text/javascript">
    {literal}
    $(document).ready(function(){
        $('#contact_us_form').validate();
    });
    {/literal}
</script>

{if $smarty.get.mode eq "update"}
{$lng.txt_contact_us_header}
{/if}
<p />
{if $fillerror ne ''}
<font class="Star">{$lng.txt_registration_error}</font><br />
{/if}
{if $antibot_err ne ''}
<font class="Star">{$lng.msg_err_antibot}</font><br />
{/if}
<form action="{pages_url var="help" section="contactus" action="contactus" is_exclude=false}" method="post" id='contact_us_form' name="contact_us_form">
<input type="hidden" name="usertype" value="{$usertype}" />

<div class="input_field_1">
    <label>{$lng.lbl_email}</label>
    <input type="email" id="email" name="email" size="32" maxlength="128" value="{$userinfo.email}" class='required email {if $fillerror ne "" and $userinfo.email eq ""}error{/if}' />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_subject}</label>
    <input type="text" id="subject" name="subject" size="32" maxlength="128" value="{$userinfo.subject}" class='required{if $fillerror ne "" and $userinfo.subject eq ""} error{/if}' />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_message}</label>
    <textarea cols="48" id="message_body" rows="12" name="body" class='required error'>{$userinfo.body}</textarea>
</div>

{if $addons.image_verification and $show_antibot.on_contact_us eq 'Y'}
{include file='addons/image_verification/spambot_arrest.tpl' mode='advanced' id=$antibot_sections.on_contact_us}
{/if}

{include file='buttons/submit.tpl' href="javascript: cw_submit_form('contact_us_form')"}
<input type='submit' style='display:none;' hidefocus="true" tabindex="-1" />
</form>
<div class="clear"></div>
