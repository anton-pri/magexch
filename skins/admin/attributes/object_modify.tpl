{if $attributes}
<a id="attr"></a>
{capture name='attributes'}
{foreach from=$attributes item=attribute name=attrs}
{if $attribute.attr_class_name ne '' && $attribute.attr_class_id ne ''}
  {if $cur_attr_class_id neq $attribute.attr_class_id}
    {assign var=cur_attr_class_id value=$attribute.attr_class_id}
    {*include file='common/subheader.tpl' title=$attribute.attr_class_name*}
    <h2 class="content-heading {if $smarty.foreach.attrs.first}first-item{/if}">{$attribute.attr_class_name}</h2>

  {/if}
    {if $attribute.type ne 'hidden'}
<div class="input_field_{$attribute.is_required} form-group {if $attribute.is_required}required{/if}">
    <label class='{if $attribute.is_required}required{/if} {if ($attribute.type eq 'text' || $attribute.type eq 'textarea')}multilan{/if} col-xs-12'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[{$attribute.field}]" />{/if}
        {$attribute.name}
    </label>
    {if $attribute.fieldname}
		{assign var=fieldname value=$attribute.fieldname}
	{else}
		{assign var=fieldname value="attributes"}
	{/if}
    <div class="col-xs-12">{include file=admin/attributes/default_types.tpl' fieldname=$fieldname}</div>
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
    <h2 class="content-heading {*if $smarty.foreach.attr.first}first-item{/if*}">{$attribute.addon_lng}</h2>

  {/if}
    {if $attribute.type ne 'hidden'}
<div class="input_field_{$attribute.is_required} form-group {if $attribute.is_required}required{/if}">
    <label class='{if $attribute.is_required}required{/if} {if ($attribute.type eq 'text' || $attribute.type eq 'textarea')}multilan{/if} col-xs-12'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[{$attribute.field}]" />{/if}
        {$attribute.name}
    </label>
    {if $attribute.fieldname}
		{assign var=fieldname value=$attribute.fieldname}
	{else}
		{assign var=fieldname value="attributes"}
	{/if}
    <div class="col-xs-12 {if $attribute.field eq 'domains'} col-md-6{/if}">{include file='admin/attributes/default_types.tpl' fieldname=$fieldname}</div>
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
