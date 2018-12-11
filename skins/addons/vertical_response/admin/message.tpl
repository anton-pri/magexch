
<form action="index.php?target={$current_target}" method="post" name="message_{$included_tab}_form">
	<input type="hidden" name="mode" value="message" />
	<input type="hidden" name="list_id" value="{$list_id}" />
	<input type="hidden" name="action" value="{$action}" />
	<input type="hidden" name="message[news_id]" value="{$message.news_id}" />

	<div class="box">
		<div class="input_field_0">
			<label>{$lng.lbl_subject}</label>
			<input type="text" name="message[subject]" value="{$message.subject|escape}" />
		</div>
		<div class="input_field_1">
			<label>{$lng.lbl_body}</label>
			{include file='main/textarea.tpl' name='message[body]' data=$message.body}
			{if $error.body and !$message.body}<font class="field_error">&lt;&lt;</font>{/if}
		</div>
		<div class="input_field_0">
			<label>{$lng.lbl_products} #1</label>
			<table>
				{product_selector multiple=1 prefix_name='message[products1]' prefix_id='products1' products=$message.products1}
			</table>
		</div>
		<div class="input_field_0">
			<label>{$lng.lbl_products} #2</label>
			<table>
				{product_selector multiple=1 prefix_name='message[products2]' prefix_id='products2' products=$message.products2}
			</table>
		</div>
	</div>

	<div class="buttons">{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('message_`$included_tab`_form');"}</div>

</form>
