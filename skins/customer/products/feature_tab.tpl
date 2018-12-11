{foreach from=$attributes item=attribute}
    {if !$list_of_attributes || ($list_of_attributes && in_array($attribute.attribute_id, $list_of_attributes))}
<div class="input_field_1">
    <label>{$attribute.name}</label>
    {include file='main/attributes/show.tpl' attribute=$attribute}
</div>
    {/if}
{/foreach}
