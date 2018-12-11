{if $included_tab eq 'add_message' && $messageid}
{assign var="message" value=""}
{/if}

<form action="index.php?target={$current_target}" method="post" name="message_{$included_tab}_form">
<input type="hidden" name="mode" value="messages" />
<input type="hidden" name="list_id" value="{$list_id}" />
<input type="hidden" name="action" value="{$action}" />
<input type="hidden" name="message[news_id]" value="{$message.news_id}" />

<div class="form-horizontal">

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_subject}</label>
	<div class="col-xs-12">
		<input type="text" class="form-control" name="message[subject]" value="{$message.subject|escape}" />
		{if $error.subject and !$message.subject}<font class="field_error">&lt;&lt;</font>{/if}
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_news_body}</label>
	<div class="col-xs-12">
    	{include file='main/textarea.tpl' name='message[body]' data=$message.body}
		{if $error.body and !$message.body}<font class="field_error">&lt;&lt;</font>{/if}
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_html_tags_allowed}</label>
	<div class="col-xs-6 col-md-2">
    	{include file='admin/select/yes_no.tpl' name='message[allow_html]' value=$message.allow_html}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_show_as_news}:</label>
    <div class="col-xs-6 col-md-2">
    	<select name="message[show_as_news]" class="form-control">
    	<option value="1"{if $message.show_as_news eq "1"} selected="selected"{/if}>{$lng.lbl_yes}</option>
    	<option value="0"{if $message.show_as_news eq "0"} selected="selected"{/if}>{$lng.lbl_no}</option>
    	</select>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_countries}<br />* <sub>If country specified then only users with defined address will receive letters</sub></label>
	<div class="col-xs-8 col-md-6">
    	{include file='admin/select/country.tpl' name='countries[]' value=$message.countries multiple=5}
    </div>
</div>

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_send_to_test_emails}</label>
    <div class="col-xs-12 col-md-6">
	<input type="text" class="form-control push-5" name="message[email1]" value="{$message.email1}" /><br />
	<input type="text" class="form-control push-5" name="message[email2]" value="{$message.email2}" /><br />
	<input type="text" class="form-control push-5" name="message[email3]" value="{$message.email3}" />
    </div>
</div>

</div>

<div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('message_`$included_tab`_form');" style="btn-green push-20"}</div>

</form>
