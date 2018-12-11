<div class='dod_attribute_row'>
<input type='hidden' name='dod_bonus[D][attr][{$index}][attribute_id]' value='{$attribute.attribute_id}' />
<label style='float:left; width: 200px;'><strong>{$attribute.name}</strong></label>

<select name='dod_bonus[D][attr][{$index}][operation]' id='operation_{$index}'>
<option value='eq'> = </option>
<option value='lt'>&lt;</option>
<option value='le'>&lt;=</option>
<option value='gt'>&gt;</option>
<option value='ge'>&gt;=</option>
{if $attribute.type eq 'text' || $attribute.type eq 'integer' || $attribute.type eq 'decimal'}
<option value='bt'>between (values separated by coma)</option>
<option value='in'>in (values separated by coma)</option>
{/if}
</select>

{include file='main/attributes/default_types.tpl' fieldname="dod_bonus[D][attr][`$index`][value]"}
<input type="hidden" size="11" maxlength="11" name="dod_bonus[D][attr][{$index}][quantity]" value="1" placeholder='{$lng.lbl_quantity}' />
	<a onclick="javascript: $(this).parent('.dod_attribute_row').remove();" href="javascript: void(0);">
    <img src="{$ImagesDir}/admin/minus.png">
	</a>
<div class='clear'></div>
{if $operation}
<script>
$('#operation_{$index}').find('option[value="{$operation}"]').attr('selected','selected');
</script>
{/if}
</div>
