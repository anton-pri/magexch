{if $product.product_id ne '' && $show_required eq 'Y'}
{tunnel func='cw_product_shipping_get_options' via='cw_call' assign='product_shipping_options_data' param1=$product.product_id}
{assign var='custom_shipping_options' value=$product_shipping_options_data.shipping_options}
{assign var='shipping_values' value=$product_shipping_options_data.shipping_values}
<div class="addon_title">{$lng.addon_name_product_shipping_options}</div>
        <table>
            <tr>
                <td id="pso_ship_box_1">
                    <select id="pso_ship_shipping_id_0" name="product_shipping_values[0][shipping_id]" size="1">
                        <option value="0">{$lng.lbl_ps_select_element}</option>
                        {foreach from=$custom_shipping_options item=csc}
                        <optgroup label="{$csc.carrier}">
                        {foreach from=$csc.shipping item=cso} 
                        <option value="{$cso.shipping_id}" {if $shipping_values.0.shipping_id eq $cso.shipping_id}selected="selected"{/if}>{$cso.shipping}{if !$cso.active}&nbsp;({$lng.lbl_disabled}){/if}</option>
                        {/foreach}
                        </optgroup>
                        {/foreach}
                    </select>
                    <input id="pso_ship_price_0" type="text" class='micro' maxlength="11" name="product_shipping_values[0][price]" value="{$shipping_values.0.price}" />
                </td>
                <td id="pso_ship_add_button">{include file="main/multirow_add.tpl" mark="pso_ship" is_lined=true}
                <a href="javascript: void(0);" onclick="$(this).closest('tr').find('select,input[type=hidden],input[type=text]').val('');"><img src="{$ImagesDir}/admin/minus.png" /></a>
                </td>
            </tr>
        </table>

    {if $shipping_values|@count gt 1}
    {assign var='_group' value='pso_ship'}
    <script type="text/javascript">
        {foreach from=$shipping_values item=_elem name=$_group}
        {if !$smarty.foreach.$_group.first}
        add_inputset_preset('{$_group}', document.getElementById('{$_group}_add_button'), false,
        [
            {ldelim}regExp: /{$_group}_shipping_id/, value: '{$_elem.shipping_id|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_price/, value: '{$_elem.price|escape}'{rdelim},
        ]
        );
        {/if}
        {/foreach}
    </script>
    {/if}
{/if}
