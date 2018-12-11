<form action="index.php?target={$current_target}&mode=events&event_id={$event_id}" method="post" name="event_details_form">
<input type="hidden" name="action" value="update" />

<div class="input_field_1">
	<label>{$lng.lbl_status}:</label>
	<select name="event_details[status]">
		<option value="P"{if $event_data.status eq "P"} selected="selected"{/if}>{$lng.lbl_private}</option>
		<option value="G"{if $event_data.status eq "G"} selected="selected"{/if}>{$lng.lbl_public}</option>
		<option value="D"{if $event_data.status eq "D"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
	</select>
</div>
<div class="input_field_1">
	<label>{$lng.lbl_giftreg_title}:</label>
	<input type="text" size="50" maxlength="255" name="event_details[title]" value="{$event_data.title|escape}" />
</div>
<div class="input_field_1">
	<label>{$lng.lbl_giftreg_event_date}:</label>
    {include file='main/select/date.tpl' name='event_details[event_date]' value=$event_data.event_date}
</div>
<div class="input_field_0">
	<label>{$lng.lbl_description}:</label>
	<textarea cols="50" rows="4" name="event_details[description]">{$event_data.description|escape}</textarea>
</div>
<div class="input_field_0">
	<label>{$lng.lbl_giftreg_html_content}:</label>
	<textarea cols="50" rows="20" name="event_details[html_content]">{$event_data.html_content|escape}</textarea>
</div>
<div class="input_field_1">
	<label>{$lng.lbl_giftreg_guestbook}:</label>
    {include file='main/select/yes_no.tpl' name='event_details[guestbook]' value=$event_data.guestbook}
</div>

{if $event_data}
{include file='buttons/update.tpl' href="javascript: cw_submit_form('event_details_form');" style='btn'}
{else}
{include file='buttons/button.tpl' button_title=$lng.lbl_create href="javascript: cw_submit_form('event_details_form');" style='btn'}
{/if}

</form>
