<div class="product_field">
  {if !$product.avail}
    <select{if $id ne ""} id="{$id}"{/if} name="{$amount_field_name}"{if $product_options} onchange="javascript: accessoriesRebuildWholesale('{$product_list_name}', {$product.product_id});"{else} onChange="javascript: change_amount({$product.product_id}, this.value);"{/if}>
      <option value="0">{$lng.lbl_out_of_stock}</option>
    </select>
  {else}
    {if $product_avail.settings.unlimited_products}
      {assign var="mq" value=$config.Appearance.max_select_quantity}
    {else}
      {math equation="x/y" x=$config.Appearance.max_select_quantity y=$product.min_amount assign="tmp"}
      {if $tmp lt 2}
        {assign var="minamount" value=$product.min_amount}
      {else}
        {assign var="minamount" value=1}
      {/if}
      {math equation="min(maxquantity+minamount, productquantity+1)" assign="mq" maxquantity=$config.Appearance.max_select_quantity minamount=$minamount productquantity=$product.avail}
      {if $mq lt 0}{assign var="mq" value=1}{/if}
    {/if}
    {if !$product.distribution}
      {if $product.min_amount lt 1}
        {assign var="start_quantity" value=0}
      {else}
        {assign var="start_quantity" value=$product.min_amount}
      {/if}
      {if $product_avail.settings.unlimited_products}
        {math equation="x+y" assign="mq" x=$mq y=$start_quantity}
      {/if}
      <select{if $id ne ""} id="{$id}"{/if} name="{$amount_field_name}"{if $product_options} onchange="javascript: accessoriesRebuildWholesale('{$product_list_name}', {$product.product_id});"{else} onchange="javascript: change_amount({$product.product_id}, this.value);"{/if}>
        {section name="quantity" loop=$mq start=$start_quantity}
          <option value="{%quantity.index%}" {if $smarty.get.quantity eq %quantity.index%}selected{/if}>{%quantity.index%}</option>
        {/section}
      </select>
    {else}
      <font class="ProductDetailsTitle">1</font>
      <input type="hidden" name="amount" value="1" />
      {if $product.distribution}{$lng.txt_product_downloadable}{/if}
    {/if}
  {/if}
</div>
