{tunnel func='cw_attributes_get_types' assign='attribute_types'}
<select class="form-control" name="{$name}"{if $multiple} multiple size="{$multiple}"{/if} id="{$name|id}"{if $onchange} onchange="{$onchange}"{/if}>
{if $is_please_select}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$attribute_types item=tp}
<option value="{$tp}"{if $value eq $tp} selected{/if}>{lng name="lbl_att_type_`$tp`"}</option>
{/foreach}
</select>
