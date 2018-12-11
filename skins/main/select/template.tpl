<select name="{$name}" id="{$name|id}"{if $disabled} disabled{/if}{if $onchange} onchange="{$onchange}"{/if}>
{if $is_please_select}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$layouts item=item}
{assign var='id' value=$item.layout_id}
{assign var='field_value' value=$item.title}
{assign var='default_title' value="lbl_`$item.layout`"}
{lng name=$default_title|replace:'docs':'docs_info' assign='lng_default_title'}
<option value="{$id}"{if $id eq $value} selected="selected"{/if}>{$field_value|default:$lng.lbl_default_template|substitute:'doc':$lng_default_title}</option>
{/foreach}
</select>
