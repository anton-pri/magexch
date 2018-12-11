{if !$categories_short}
{tunnel func='cw_category_get_short_list' load='category' assign='categories_short'}
{/if}
{if $categories_short}
{if $type eq 'select'}
<select name="{$name}"{if $multiple} multiple size="{$multiple}"{/if} id="{$name|id}">
{if $is_please_select}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$categories_short item=cat}
<option value="{$cat.category_id}">{$cat.category_path|default:$cat.category}</option>
{/foreach}
</select>
{else}
{foreach from=$categories_short item=cat}
<div class="input_field_easy_0_0">
    <input type="checkbox" name="{$name}" value="{$cat.category_id}" /><label>{$cat.category}</label>
</div>
{/foreach}
{/if}
{/if}
