<select name="{$name}" id="{$name|id}">
<option value="">{$lng.lbl_please_select}</option>
{foreach from=$product_layout_elements key=field item=element}
<option value="{$field}">{lng name=$element}</option>
{/foreach}
</select>
