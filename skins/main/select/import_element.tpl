<select name="{$name}" id="{$name|id}">
<option value="">{$lng.lbl_please_select}</option>
<option value="none">{$lng.lbl_none}</option>
{foreach from=$elements item=element}
<option value="{$element.field}">{$element.field}</option>
{/foreach}
</select>
