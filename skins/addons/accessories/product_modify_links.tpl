<div class="box">

<script type="text/javascript">
  var txt_ac_delete_selected_linked_products_warning = "{$lng.txt_ac_delete_selected_linked_products_warning|escape:javascript|strip_tags}";
</script>
{capture name=section}
<p>{$lng.txt_ac_admin_edit_accessories_top_text}</p>

<form action="index.php?target=products" method="post" name="updateLinkedProductsForm{$link_type}">
  <input type="hidden" name="mode" value="{$mode|default:'details'}" />
  <input type="hidden" name="link_type" value="{$link_type|default:0}" />
  <input type="hidden" name="action" value="" />
  <input type="hidden" name="product_id" value="{$product_id|default:0}" />
  <table width="100%" cellpadding="2" class="table table-striped dataTable vertical-center">
    <thead>
      <th class="text-center">{if $linked_products}<input type='checkbox' class='select_all' class_to_select='update_linked_products_item' />{else}&nbsp;{/if}</th>
      <th>{$lng.lbl_ac_linked_products}</th>
      <th class="text-center">{$lng.lbl_ac_bidirectional}</th>
      <th class="text-center">{$lng.lbl_ac_sort_by}</th>
      <th class="text-center">{$lng.lbl_ac_active}</th>
    </thead>
    <tbody>
      {if $linked_products}
        {foreach from=$linked_products item="linked_product"}
          {assign var="linked_product_id" value=$linked_product.product_id}
          <tr class="{cycle values=",cycle"}">
            <td align="center">
              <input type="checkbox" name="delete_linked_products[{$linked_product_id}]" class="update_linked_products_item" />
            </td>
            <td align="left">
              <a href="index.php?target=products&amp;mode=details&amp;product_id={$linked_product_id}">
                {$linked_product.product}
              </a>
            </td>
            <td align="center">
              <input type="checkbox" name="linked_products_options[{$linked_product_id}][is_bidirectional_link]"{if $linked_product.is_bidirectional_link eq "Y"} checked="checked"{/if} />
            </td>
            <td align="center">
              <input type="text" class="form-control" maxlength="8" size="4" name="linked_products[{$linked_product_id}][orderby]" value="{$linked_product.orderby|default:"0"}" style="text-align: center;" />
            </td>
            <td align="center">
              <input type="checkbox" name="linked_products[{$linked_product_id}][active]"{if $linked_product.active eq "Y"} checked="checked"{/if} />
            </td>
          </tr>
        {/foreach}
      {else}
        <tr>
          <td colspan="5" class="text-center">{$lng.txt_ac_there_are_no_linked_products}</td>
        </tr>
      {/if}
    </tbody>
  </table>
  {if $linked_products}
    <div class="buttons">
      {include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('updateLinkedProductsForm`$link_type`', 'update_linked_products');" button_title=$lng.lbl_ac_update_linked_products style="btn-green push-20"}
      {include file="adminbuttons/button.tpl" href="javascript: if (checkMarks(document.updateLinkedProductsForm`$link_type`, new RegExp('delete_linked_products\[[0-9]+\]', 'gi'))) if (confirm(txt_ac_delete_selected_linked_products_warning)) cw_submit_form('updateLinkedProductsForm`$link_type`', 'delete_linked_products');" button_title=$lng.lbl_ac_delete_linked_products style="btn-danger push-20"}
    </div>
  {/if}
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_accessories}

{*include file="common/subheader.tpl" title=$lng.lbl_ac_add_linked_product*}
{capture name=section2}
<form action="index.php?target=products" method="post" name="addLinkedProductForm{$link_type}">
  <input type="hidden" name="mode" value="{$mode|default:"details"}" />
  <input type="hidden" name="action" value="" />
  <input type="hidden" name="link_type" value="{$link_type|default:0}" />
  <input type="hidden" name="page" value="{$page|default:"1"}" />
  <input type="hidden" name="product_id" value="{$product_id|default:"0"}" />
  <table width="100%" cellpadding="2" class="table table-striped dataTable vertical-center">
    <thead>
      <tr>
        <th>{$lng.lbl_ac_linked_product_s}</th>
        <th align="center">*&nbsp;{$lng.lbl_ac_bidirectional}</th>
        <th align="center">**&nbsp;{$lng.lbl_sort_by}</th>
        <th align="center">{$lng.lbl_ac_active}</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="addlink">
			{product_selector name_for_id='linked_product_ids' name_for_name='linked_product_ids_name' prefix_id="link" form="addLinkedProductForm`$link_type`"}
        </td>
        <td align="center"><input type="checkbox" name="linked_product_options[is_bidirectional_link]" /></td>
        <td align="center"><input type="text" class="form-control"  maxlength="8" size="4" name="linked_product_options[orderby]" value="{$next_order_by|default:"0"}" style="text-align: center;" /></td>
        <td align="center"><input type="checkbox" name="linked_product_options[active]" checked="checked" /></td>
      </tr>
    </tbody>
  </table>
  <p>(*)&nbsp;{$lng.txt_ac_linked_products_bidirectional_note}</p>
  {assign var="example_orderby_0" value=700}
  {math equation="x+y" assign="example_orderby_1" x=$example_orderby_0 y=$order_by_step}
  {math equation="x+y" assign="example_orderby_2" x=$example_orderby_1 y=$order_by_step}
  <p>(**)&nbsp;{$lng.txt_ac_linked_products_orderby_note|substitute:"order_by_step":$order_by_step|substitute:"example_orderby_0":$example_orderby_0|substitute:"example_orderby_1":$example_orderby_1|substitute:"example_orderby_2":$example_orderby_2}</p>
  <div class="buttons">{include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('addLinkedProductForm`$link_type`', 'add_linked_product');" button_title=$lng.lbl_ac_add_linked_product class="btn-green push-20"}</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section2 extra='width="100%"' title=$lng.lbl_ac_add_linked_product}

</div>
