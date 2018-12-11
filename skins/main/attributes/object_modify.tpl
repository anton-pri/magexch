{if $attributes}
<a id="attr"></a>
{capture name='attributes'}
{foreach from=$attributes item=attribute}
{if $attribute.attr_class_name ne '' && $attribute.attr_class_id ne ''}
  {if $cur_attr_class_id neq $attribute.attr_class_id}
    {assign var=cur_attr_class_id value=$attribute.attr_class_id}
    {*include file='common/subheader.tpl' title=$attribute.attr_class_name*}
    <div class="addon_title">{$attribute.attr_class_name}</div>

  {/if}
    {if $attribute.type ne 'hidden'}
<div class="input_field_{$attribute.is_required}">
    <label class='{if $attribute.is_required}required{/if} {if ($attribute.type eq 'text' || $attribute.type eq 'textarea')}multilan{/if}'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[{$attribute.field}]" />{/if}
        {$attribute.name}
    </label>
    {if $attribute.fieldname}
		{assign var=fieldname value=$attribute.fieldname}
	{else}
		{assign var=fieldname value="attributes"}
	{/if}
    {include file='main/attributes/default_types.tpl' fieldname=$fieldname}
</div>
    {/if}
{/if}
{/foreach}
{/capture}
{if $smarty.capture.attributes}
{if $hide_subheader ne 'Y'}
{include file='common/subheader.tpl' title=$lng.lbl_object_attributes_settings class=attributes}
{/if}
{$smarty.capture.attributes}
{/if}

<a id="addons"></a>
{capture name='attributes'}
{foreach from=$attributes item=attribute}
{if $attribute.addon ne '' && $attribute.addon_lng ne ''}
  {if $cur_attribute_mod neq $attribute.addon}
    {assign var=cur_attribute_mod value=$attribute.addon}
    {*include file='common/subheader.tpl' title=$attribute.addon_lng*}
    <div class="addon_title">{$attribute.addon_lng}</div>

  {/if}
    {if $attribute.type ne 'hidden'}
<div class="input_field_{$attribute.is_required}">
    <label class='{if $attribute.is_required}required{/if} {if ($attribute.type eq 'text' || $attribute.type eq 'textarea')}multilan{/if}'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[{$attribute.field}]" />{/if}
        {$attribute.name}
    </label>
    {if $attribute.fieldname}
		{assign var=fieldname value=$attribute.fieldname}
	{else}
		{assign var=fieldname value="attributes"}
	{/if}
    {include file='main/attributes/default_types.tpl' fieldname=$fieldname}
</div>
    {/if}
{/if}
{/foreach}
{/capture}
{if $smarty.capture.attributes}
    {if $hide_subheader ne 'Y'} 
    {include file='common/subheader.tpl' title=$lng.lbl_addons_attr_settings class=addons}
    {/if}
    {$smarty.capture.attributes}
{/if}


{/if}
