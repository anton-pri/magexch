{if $included_tab eq '1'}
{* start *}
    <div class="product_description" id="product_description">{if $product.fulldescr ne ""}{$product.fulldescr}{else}{$product.descr}{/if}</div>
    <div class="product_details">
    <aside>
      <h2>{$lng.lbl_product_details}</h2>
      <ul class="prod_fields">
        {include file='customer/products/product-fields.tpl'}
      </ul>
    </aside>
    </div>

{elseif $included_tab eq 3}
{* start *}
{$product.specifications}

{/if}
