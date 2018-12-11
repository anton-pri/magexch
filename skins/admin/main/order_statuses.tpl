<script type="text/javascript">
<!--
{literal}
function popup_preview_order_emails(status_code, preview_area) {

    if ($('#preview_order_emails').length==0)
        $('body').append('<div id="preview_order_emails" style="overflow:hidden"></div>');

    var hash = status_code+preview_area;
    if (hash != $('#preview_order_emails').data('hash')) {
        // Load iframe with image selector into dialog
        $('#preview_order_emails').html("<iframe frameborder='no' width='800' height='490' src='index.php?target=preview_order_emails&status_code="+status_code+"&preview_area="+preview_area+"'></iframe>");
    }

    $('#preview_order_emails').data('hash', hash);
    // Show dialog
    sm('preview_order_emails', 830, 530, false, 'Preview order '+preview_area+' emails for status '+status_code);
}
{/literal}
-->
</script>


<form name="order_statuses_form" method="post" action="index.php?target=orders_statuses">
<input type="hidden" name="action" value="update_statuses" />

{if $smarty.get.mode neq 'add'}

{capture name=section}
{capture name=block}

<div class="form-horizontal">
<table class="table table-striped dataTable" width="100%">
{foreach from=$order_statuses item=os}
<tr><th colspan="2"><span id="order_status_{$os.code}" {if $os.is_system}style="color:brown"{/if}>{$lng.lbl_os_code|default:'Code'}: {$os.code} - {include file="main/select/doc_status.tpl" mode="static" status=$os.code}</span>
</th></tr>
{if $os.deleted eq 0}
<tr>
<td valign="top" width="10" style="padding-right:35px;"><img height="1" width="10" alt="" src="{$ImagesDir}/spacer.gif">
<input type="checkbox" name="posted_data[{$os.code}][deleted]" {if $os.is_system}disabled="disabled" style="background: gray"{/if} value="1" /></td>
<td>
<div class="form-group">
  <label class='required col-xs-12'>{$lng.lbl_name|default:'Name'}</label>
  <div class="col-xs-12">
  	<input type="text" class='required form-control' name="posted_data[{$os.code}][name]" value="{$os.name}" />
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_decrease_inventory|default:'Decrease inventory'} <input type="checkbox" name="posted_data[{$os.code}][inventory_decreasing]" {if $os.inventory_decreasing}checked="checked"{/if} value="1"/>
</label>
</div>

<div class="form-group">
<label class="col-xs-12">{$lng.lbl_customer_is_notified|default:'Email to customer'} <input type="checkbox" name="posted_data[{$os.code}][email_customer]" {if $os.email_customer}checked="checked"{/if} value="1"/>
</label>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_customer_email_subject|default:'Customer email subject'}</label>
  <div class="col-xs-12">
	<input type="text" class="form-control" name="posted_data[{$os.code}][email_subject_customer]" value="{$os.email_subject_customer|escape}" size="65" />
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_customer_email_message|default:'Customer email'}</label>
  <div class="col-xs-12">
	<textarea name="posted_data[{$os.code}][email_message_customer]" class="form-control" style="width: auto" cols="65" rows="7">{$os.email_message_customer}</textarea>
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_customer_email_message_modify_mode|default:'Customer email body:'}</label>
  <div class="col-xs-12">
	<select class="form-control" name="posted_data[{$os.code}][email_message_customer_mode]">
		<option value="I" {if $os.email_message_customer_mode eq "" or $os.email_message_customer_mode eq "I"}selected="selected"{/if}>{$lng.lbl_inserted_into_default_template|default:'inserted into default template'}</option>
		<option value="R" {if $os.email_message_customer_mode eq "R"}selected="selected"{/if}>{$lng.lbl_replaces_default_template|default:'replaces default template'}</option>
	</select>
  </div>
</div>
<div class="form-group">
<div class="col-xs-12"><a href="javascript: popup_preview_order_emails('{$os.code}', 'customer'); void(0);" class="btn btn-minw btn-info">{$lng.lbl_os_customer_emails_preview|default:'Customer emails preview'}</a></div>
</div>


<div class="form-group">
<label class="col-xs-12">{$lng.lbl_admin_is_notified|default:'Email to Order department'} <input type="checkbox" name="posted_data[{$os.code}][email_admin]" {if $os.email_admin}checked="checked"{/if} value="1"/>
</label>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_admin_email_subject|default:'Admin email subject'}</label>
  <div class="col-xs-12">
	<input type="text" class="form-control" name="posted_data[{$os.code}][email_subject_admin]" value="{$os.email_subject_admin|escape}" size="65"/>
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_admin_email_message|default:'Admin email'}</label>
  <div class="col-xs-12">
	<textarea class="form-control" name="posted_data[{$os.code}][email_message_admin]" style="width: auto" cols="65" rows="7">{$os.email_message_admin}</textarea>
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_admin_email_message_modify_mode|default:'Admin email body:'}</label>
  <div class="col-xs-12">
	<select name="posted_data[{$os.code}][email_message_admin_mode]" class="form-control">
		<option value="I" {if $os.email_message_admin_mode eq "" or $os.email_message_admin_mode eq "I"}selected="selected"{/if}>{$lng.lbl_inserted_into_default_template|default:'inserted into default template'}</option>
		<option value="R" {if $os.email_message_admin_mode eq "R"}selected="selected"{/if}>{$lng.lbl_replaces_default_template|default:'replaces default template'}</option>
	</select>
  </div>
</div>

<div class="form-group">
<label class="col-xs-12">{$lng.lbl_extra_email_to_send_admin_message|default:'Extra email address to send admin message (coma separated)'}</label>
  <div class="col-xs-12">
	<textarea class="form-control" name="posted_data[{$os.code}][extra_admin_email]" style="width: auto" cols="65" rows="2">{$os.extra_admin_email}</textarea>
  </div>
</div>
<div class="form-group">
<div class="col-xs-12"><a href="javascript: popup_preview_order_emails('{$os.code}', 'admin'); void(0);" class="btn btn-minw btn-info">{$lng.lbl_os_admin_emails_preview|default:'Admin emails preview'}</a></div>
</div>

<!-- cw@order_statuses_edit -->

<div class="form-group">
<label class="col-xs-12">{$lng.lbl_color|default:'Color'}</label>
  <div class="col-xs-12">
	<input type="text" class="form-control" name="posted_data[{$os.code}][color]" value="{$os.color}" id="order_color_{$os.code}" />
  </div>
<script type="text/javascript">
<!--
$("#order_color_{$os.code}").spectrum({ldelim}
    preferredFormat: "hex",
    color: "{$os.color|default:'#fff'}"
{rdelim});
-->
</script>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_orderby}</label>
  <div class="col-xs-12">
	<input type="text" class="form-control" name="posted_data[{$os.code}][orderby]" value="{$os.orderby}" size="5"/>
  </div>
</div>
        {include file='admin/attributes/object_modify.tpl' attributes=$os.attributes hide_subheader='Y'}

</td>
</tr>


{/if}
{/foreach}
</table>

<div id="sticky_content" class="buttons">
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('order_statuses_form');" style="btn-green push-20"}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=orders_statuses&mode=add" style="btn-green push-20"}

</div>

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_os_statuses_list|default:'Statuses List'}

{else}


{capture name=section}
{capture name=block}

<div class="box">
<table class="header statuses" width="100%">
<th colspan="2"><label class='required'>{$lng.lbl_os_new_code|default:'New Code'}:</label> <input id="new_os_code" class='required' type="text" name="added_data[code]" value="{$new_os.code}" size="2" maxlength="2" pattern="[A-Z]" /></th>
<tr>
<td valign="top"><img height="1" width="10" alt="" src="{$ImagesDir}/spacer.gif">
</td>
<td>
<div class="form-group">
  <label class='required'>{$lng.lbl_name|default:'Name'}</label>
  <input class='required' type="text" name="added_data[name]" value="{$new_os.name}" />
</div>
<div class="form-group">
<label>{$lng.lbl_decrease_inventory|default:'Decrease inventory'}</label>
<input type="checkbox" name="added_data[inventory_decreasing]" {if $new_os.inventory_decreasing}checked="checked"{/if} value="1"/>
</div>

<div class="form-group">
<label>{$lng.lbl_customer_is_notified|default:'Email to customer'}</label>
<input type="checkbox" name="added_data[email_customer]" {if $new_os.email_customer}checked="checked"{/if} value="1" />
</div>
<div class="form-group">
<label>{$lng.lbl_admin_email_subject|default:'Customer email subject'}</label>
<input type="text" name="added_data[email_subject_customer]" value="{$new_os.email_subject_customer}" size="60" />
</div>
<div class="form-group">
<label>{$lng.lbl_admin_email_message|default:'Customer email'}</label>
<textarea name="added_data[email_message_customer]" style="width: auto" cols="60" rows="7">{$new_os.email_message_customer}</textarea>
</div>


<div class="form-group">
<label>{$lng.lbl_admin_is_notified|default:'Email to admin'}</label>
<input type="checkbox" name="added_data[email_admin]" {if $new_os.email_admin}checked="checked"{/if} value="1" />
</div>
<div class="form-group">
<label>{$lng.lbl_admin_email_subject|default:'Admin email subject'}</label>
<input type="text" name="added_data[email_subject_admin]" value="{$new_os.email_subject_admin}" size="60"/>
</div>
<div class="form-group">
<label>{$lng.lbl_admin_email_message|default:'Admin email'}</label>
<textarea name="added_data[email_message_admin]" style="width: auto" cols="60" rows="7">{$new_os.email_message_admin}</textarea>
</div>

<div class="form-group">
<label>{$lng.lbl_extra_email_to_send_admin_message|default:'Extra email address to send admin message'}</label>
<textarea name="added_data[extra_admin_email]" style="width: auto" cols="65" rows="2">{$new_os.extra_admin_email}</textarea>
</div>
<div class="form-group">
<label>{$lng.lbl_orderby}</label>
<input type="text" name="added_data[orderby]" value="{$new_os.orderby}" size="5"/>
</div>
</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td align="left" style="padding:0;">
        {include file='main/attributes/object_modify.tpl' hide_subheader='Y'}
    </td>
</tr>
</table>

<br />
<br />
<div id="sticky_content" class="buttons">
    {include file='buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('order_statuses_form');"}
</div>

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_add_new|default:'Statuses List'}
{/if}

</form>
