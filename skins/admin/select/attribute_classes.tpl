{tunnel func='cw_attributes_get_all_classes_for_products' assign='attributes_classes'}
<select name="{$name}"{if $multiple} multiple size="{$multiple}"{/if} id="{$id|default:"`$name|id`"}"{if $onchange} onchange="{$onchange}"{/if} class="form-control">
{if $is_please_select}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$attributes_classes item=tp}
{assign var='is_selected_value' value=false}
{foreach from=$values item=vls_id}{if $vls_id eq $tp.attribute_class_id}{assign var='is_selected_value' value=true}{/if}{/foreach}
<option value="{$tp.attribute_class_id}"{if $is_selected_value || (!$values && $tp.is_default)} selected{/if}>{$tp.name}</option>
{/foreach}
</select>
{if $replicate_attribute_classes eq "Y"}<br />
<input type="checkbox" id="replicate_attribute_classes" name="replicate_attribute_classes" style="margin-top:7px;" value="Y"/>
<label for="replicate_attribute_classes">Replicate attribute classes</label>
{/if}
