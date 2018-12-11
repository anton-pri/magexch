<div class='select_attribute_row push-20-t'>
<div class="form-group">
<input type='hidden' name='{$posted_name}[{$index}][attribute_id]' value='{$attribute.attribute_id}' />
<label><strong>{$attribute.name}</strong></label>

<select class="form-control" name='{$posted_name}[{$index}][operation]' id='operation_{$index}'>
<option value='eq'> = </option>
{if $no_extra_cmp eq '' || ($attribute.type eq 'text' || $attribute.type eq 'integer' || $attribute.type eq 'decimal' || $attribute.type eq 'rating')}
<option value='lt'>&lt;</option>
<option value='le'>&lt;=</option>
<option value='gt'>&gt;</option>
<option value='ge'>&gt;=</option>
{/if}
{if $attribute.type eq 'text' || $attribute.type eq 'integer' || $attribute.type eq 'decimal' || $attribute.type eq 'rating'}
<option value='bt'>between (values separated by coma)</option>
<option value='in'>in (values separated by coma)</option>
{/if}
</select>
{include file='admin/attributes/default_types.tpl' fieldname="`$posted_name`[`$index`][value]" rating_show=1}
	<a onclick="javascript: $(this).closest('.select_attribute_row').remove();" href="javascript: void(0);">
    <img src="{$ImagesDir}/admin/minus.png">
	</a>
<div class='clear'></div>
{if $operation}
<script>
$('#operation_{$index}').find('option[value="{$operation}"]').attr('selected','selected');
</script>
{/if}
</div>
</div>
