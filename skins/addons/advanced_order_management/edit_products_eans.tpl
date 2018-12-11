{if !$id_prefix}{assign var='id_prefix' value='ean'}{/if}

{if ($order.type eq 'G' && $order.pos.pos_user_info.order_entering_format && !$short_format) || $order.type ne 'G'}
<table class="header">
<tr>
    <th>{$lng.lbl_default_amount}</th>
    <th>{$lng.lbl_use_only_ean}</th>
</tr>
<tr>
    <td><input type="text" name="default_number" value="{$default_amount|default:1}" size="20"></td>
    <td align="center">
        <input type="hidden" name="default_use_only_ean" value="N">
        <input type="checkbox" name="default_use_only_ean" id="{$id_prefix}_use_only_ean" {if $default_use_only_ean eq 'Y'}checked{/if} onchange="javascript: cw_use_ean('{$id_prefix}')" value="Y" />
    </td>
</tr>
</table>
{/if}

<table class="header">
<tr>
    <th>{$lng.lbl_eancode}</th>
{if ($order.type eq 'G' && $order.pos.pos_user_info.order_entering_format && !$short_format) || $order.type ne 'G'}
    <th>{$lng.lbl_amount}</th>
    <th>{$lng.lbl_discount}</th>
{/if}
    <th width="300">{$lng.lbl_product}</th>
    <th>&nbsp;</th>
</tr>
<tbody id="{$id_prefix}_ean_table">
<tr>
    <td id="{$id_prefix}_add_box_0"><input type="text" name="eans[0][ean]" value="" size="20" id="{$id_prefix}_ean_0" onkeydown="javascript: return cw_focus_next_element(event, this, 0, '{$id_prefix}')"  />
    </td>
{if ($order.type eq 'G' && $order.pos.pos_user_info.order_entering_format && !$short_format) || $order.type ne 'G'}
    <td id="{$id_prefix}_add_box_1"><input type="text" name="eans[0][amount]" value="" size="20" id="{$id_prefix}_serial_0" onkeydown="javascript: return cw_create_next_el(event, this, '{$id_prefix}')" style="width: 50px;"></td>
    <td id="{$id_prefix}_add_box_2"><input type="text" name="eans[0][discount]" value="" size="10" id="{$id_prefix}_discount_0"></td>
{/if}
    <td id="{$id_prefix}_add_box_3">&nbsp;</td>
    <td id="{$id_prefix}_add_button">{include file='main/multirow_add.tpl' mark="`$id_prefix`_add" is_lined=true}</td>
</tr>
</tbody>
</table>
<script language="Javascript">
multirowInputSets.{$id_prefix}_add = {ldelim}noCloneContent: true{rdelim};
{*if $js_tab eq 'products'}document.getElementById('{$id_prefix}_ean_0').focus();{/if*}
</script>
