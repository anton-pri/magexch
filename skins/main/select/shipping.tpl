{if !$shippings}
    {tunnel func='cw_shipping_search' assign='shippings'}
{/if}
<select name="{$name}"{if $multiple} multiple="multiple" size="5"{/if}{if $hidden} style="display: none;"{/if}{if $read_only} disabled{/if}{if $onchange} onchange="{$onchange}"{/if} class="form-control">
    {if $is_please_select}
        <option value="">{$lng.lbl_please_select}</option>
    {/if}
    {foreach from=$shippings item=shipping}
        {assign var="id" value=$shipping.shipping_id}
        {if $main eq 'orders'}
            <option value='{$id}'
                    {foreach from=$value item=v}
                {if $id eq $v}selected="selected"{/if}
                    {/foreach}>
                {$shipping.shipping|trademark}</option>
        {else}
            <option value='{$id}'{if ($multiple && $values.$id) || (!$multiple && $id eq $value)} selected="selected"{/if}>{$shipping.shipping|trademark}</option>
        {/if}
    {/foreach}
</select>
