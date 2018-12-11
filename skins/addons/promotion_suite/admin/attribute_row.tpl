<div class='ps_attribute_row'>
<input type='hidden' name='ps_conds[P][attr][{$index}][attribute_id]' value='{$attribute.attribute_id}' />
<label><strong>{$attribute.name}&nbsp;</strong></label>

<select name='ps_conds[P][attr][{$index}][operation]' id='operation_{$index}'>
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

{include file='main/attributes/default_types.tpl' fieldname="ps_conds[P][attr][`$index`][value]"}
<input type="text" class='micro' maxlength="11" name="ps_conds[P][attr][{$index}][quantity]" value="{$quantity}" placeholder='Qty' />
	<a onclick="javascript: $(this).parent('.ps_attribute_row').remove();" href="javascript: void(0);">
    <img src="{$ImagesDir}/admin/minus.png" align='top' alt='-' />
	</a>
<div class='clear'></div>
{if $operation}
<script>
$('#operation_{$index}').find('option[value="{$operation}"]').attr('selected','selected');
</script>
{/if}
</div>
