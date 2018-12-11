<select class="form-control" name="{$name}"{if $multiple} multiple size="{$multiple}"{/if} id="{$id|default:"`$name|id`"}"{if $onchange} onchange="{$onchange}"{/if}>
{if $is_please_select}
<option value="">{$lng.lbl_please_select_image}</option>
{/if}
{foreach from=$images item=tp}
<option value="{$tp.image_id}"{if (!$multiple && $value eq $tp.image_id) || ($multiple && count($value) && in_array($tp.image_id, $value))} selected{/if}>{$tp.filename}</option>
{/foreach}
</select>
